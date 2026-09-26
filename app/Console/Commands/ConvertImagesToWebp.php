<?php

namespace App\Console\Commands;

use App\Models\Certificate;
use App\Models\Portfolio;
use App\Models\School;
use App\Models\User;
use App\Support\ImageOptimizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ConvertImagesToWebp extends Command
{
    protected $signature = 'images:convert-to-webp
                            {--quality=82 : Kualitas WebP (1-100)}
                            {--keep-originals : Jangan hapus file gambar lama}
                            {--show-details : Tampilkan setiap file yang diproses}';

    protected $description = 'Konversi semua file gambar lama di storage dan database ke format WebP';

    public function handle(): int
    {
        if (! extension_loaded('gd') || ! function_exists('imagewebp')) {
            $this->error('Ekstensi PHP GD dengan dukungan WebP wajib aktif di server ini.');

            return self::FAILURE;
        }

        $quality = (int) $this->option('quality');
        $quality = max(10, min(100, $quality));
        $keepOriginals = (bool) $this->option('keep-originals');
        $deleteOriginals = ! $keepOriginals;
        $isVerbose = $this->output->isVerbose() || (bool) $this->option('show-details');

        $disk = Storage::disk('public');
        $storageRoot = $disk->path('');

        $this->info('=== Diagnostik Penyimpanan ===');
        $this->line("Direktori Storage Disk: <comment>{$storageRoot}</comment>");

        if (! is_dir($storageRoot)) {
            $this->error("PERINGATAN: Direktori [{$storageRoot}] tidak ditemukan!");
            $this->warn('Kemungkinan ada cache konfigurasi lama dari komputer lain.');
            $this->warn("Solusi: Jalankan 'php artisan config:clear' terlebih dahulu.");

            return self::FAILURE;
        }

        $allFilesInDisk = $disk->allFiles();
        $this->line('Total berkas di storage: <comment>'.count($allFilesInDisk).' berkas</comment>');

        if (count($allFilesInDisk) === 0) {
            $this->warn("Folder storage publik kosong. Periksa apakah berkas berada di lokasi [{$storageRoot}].");
        }

        $stats = [
            'portfolios' => 0,
            'avatars' => 0,
            'certificates' => 0,
            'schools' => 0,
            'editor' => 0,
            'other_files' => 0,
            'db_updated' => 0,
            'bytes_saved' => 0,
            'errors' => 0,
        ];

        $this->newLine();
        $this->info("Memulai konversi gambar ke WebP (Kualitas: {$quality}%)...");
        if ($keepOriginals) {
            $this->comment('Opsi --keep-originals aktif: Berkas lama tidak akan dihapus.');
        }

        // Phase 1: Convert all physical image files found on disk
        $this->info('Fase 1: Memindai dan mengonversi berkas fisik di storage/app/public...');
        $replacements = [];

        foreach ($allFilesInDisk as $file) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (! in_array($ext, ['jpg', 'jpeg', 'png', 'bmp', 'avif'], true)) {
                continue;
            }

            $oldSize = $disk->size($file);
            $maxWidth = 1600;
            $maxHeight = 1600;

            if (str_starts_with($file, 'avatars/')) {
                $maxWidth = 400;
                $maxHeight = 400;
            } elseif (str_starts_with($file, 'school/')) {
                $maxWidth = 600;
                $maxHeight = 600;
            } elseif (str_starts_with($file, 'editor/')) {
                $maxWidth = 1200;
                $maxHeight = 1200;
            } elseif (str_starts_with($file, 'certificates/')) {
                $maxWidth = 1800;
                $maxHeight = 1800;
            }

            try {
                $newPath = ImageOptimizer::convertFile($file, $maxWidth, $maxHeight, $quality, $deleteOriginals);

                if ($newPath && $newPath !== $file) {
                    $newSize = $disk->size($newPath);
                    $saved = max(0, $oldSize - $newSize);
                    $stats['bytes_saved'] += $saved;

                    $oldBase = pathinfo($file, PATHINFO_BASENAME);
                    $newBase = pathinfo($newPath, PATHINFO_BASENAME);
                    $replacements[$file] = $newPath;
                    $replacements[$oldBase] = $newBase;

                    if (str_starts_with($file, 'portfolios/')) {
                        $stats['portfolios']++;
                    } elseif (str_starts_with($file, 'avatars/')) {
                        $stats['avatars']++;
                    } elseif (str_starts_with($file, 'certificates/')) {
                        $stats['certificates']++;
                    } elseif (str_starts_with($file, 'school/')) {
                        $stats['schools']++;
                    } elseif (str_starts_with($file, 'editor/')) {
                        $stats['editor']++;
                    } else {
                        $stats['other_files']++;
                    }

                    if ($isVerbose) {
                        $this->line(" [OK] {$file} -> {$newPath} (hemat ".round($saved / 1024, 1).' KB)');
                    }
                } else {
                    $this->warn(" [GAGAL] {$file}: Tidak dapat dikonversi.");
                    $stats['errors']++;
                }
            } catch (Throwable $e) {
                $this->error(" [ERROR] {$file}: {$e->getMessage()}");
                $stats['errors']++;
            }
        }

        // Phase 2: Update Database references
        $this->info('Fase 2: Memperbarui referensi di database...');

        // 2a. Portfolios cover_image
        try {
            Portfolio::query()
                ->whereNotNull('cover_image')
                ->where('cover_image', '!=', '')
                ->chunkById(100, function ($portfolios) use (&$stats, $disk, $isVerbose) {
                    foreach ($portfolios as $p) {
                        $clean = ltrim(preg_replace('#^/?(storage|public)/#', '', $p->cover_image), '/');
                        $webpCandidate = preg_replace('/\.(jpe?g|png|bmp|avif)$/i', '.webp', $clean);

                        if ($clean !== $webpCandidate && $disk->exists($webpCandidate)) {
                            $p->update(['cover_image' => $webpCandidate]);
                            $stats['db_updated']++;
                            if ($isVerbose) {
                                $this->line(" [DB] Portfolio #{$p->id} cover_image -> {$webpCandidate}");
                            }
                        }
                    }
                });
        } catch (Throwable $e) {
            $this->warn('Gagal sinkron database portofolio: '.$e->getMessage());
        }

        // 2b. Users avatars
        try {
            User::query()
                ->whereNotNull('avatar')
                ->where('avatar', '!=', '')
                ->chunkById(100, function ($users) use (&$stats, $disk, $isVerbose) {
                    foreach ($users as $u) {
                        $clean = ltrim(preg_replace('#^/?(storage|public)/#', '', $u->avatar), '/');
                        $webpCandidate = preg_replace('/\.(jpe?g|png|bmp|avif)$/i', '.webp', $clean);

                        if ($clean !== $webpCandidate && $disk->exists($webpCandidate)) {
                            $u->update(['avatar' => $webpCandidate]);
                            $stats['db_updated']++;
                            if ($isVerbose) {
                                $this->line(" [DB] User #{$u->id} avatar -> {$webpCandidate}");
                            }
                        }
                    }
                });
        } catch (Throwable $e) {
            $this->warn('Gagal sinkron database avatar: '.$e->getMessage());
        }

        // 2c. Certificates
        try {
            Certificate::query()
                ->whereNotNull('file')
                ->where('file', '!=', '')
                ->where('file', 'not like', '%.pdf')
                ->chunkById(100, function ($certs) use (&$stats, $disk, $isVerbose) {
                    foreach ($certs as $c) {
                        $clean = ltrim(preg_replace('#^/?(storage|public)/#', '', $c->file), '/');
                        $webpCandidate = preg_replace('/\.(jpe?g|png|bmp|avif)$/i', '.webp', $clean);

                        if ($clean !== $webpCandidate && $disk->exists($webpCandidate)) {
                            $c->update(['file' => $webpCandidate]);
                            $stats['db_updated']++;
                            if ($isVerbose) {
                                $this->line(" [DB] Certificate #{$c->id} file -> {$webpCandidate}");
                            }
                        }
                    }
                });
        } catch (Throwable $e) {
            $this->warn('Gagal sinkron database sertifikat: '.$e->getMessage());
        }

        // 2d. School logo
        try {
            $schools = School::query()
                ->whereNotNull('logo')
                ->where('logo', '!=', '')
                ->get();

            foreach ($schools as $s) {
                $clean = ltrim(preg_replace('#^/?(storage|public)/#', '', $s->logo), '/');
                $webpCandidate = preg_replace('/\.(jpe?g|png|bmp|avif)$/i', '.webp', $clean);

                if ($clean !== $webpCandidate && $disk->exists($webpCandidate)) {
                    $s->update(['logo' => $webpCandidate]);
                    $stats['db_updated']++;
                    if ($isVerbose) {
                        $this->line(" [DB] School logo -> {$webpCandidate}");
                    }
                }
            }
        } catch (Throwable $e) {
            $this->warn('Gagal sinkron database logo: '.$e->getMessage());
        }

        // 2e. Editor embedded images in descriptions
        if (! empty($replacements)) {
            $this->info('Memperbarui tautan gambar pada deskripsi teks...');
            try {
                foreach ($replacements as $old => $new) {
                    Portfolio::query()
                        ->where('description', 'like', "%{$old}%")
                        ->chunkById(50, function ($portfolios) use ($old, $new, &$stats) {
                            foreach ($portfolios as $p) {
                                $p->update(['description' => str_replace($old, $new, $p->description)]);
                                $stats['db_updated']++;
                            }
                        });
                }
            } catch (Throwable $e) {
                $this->warn('Gagal memperbarui tautan deskripsi: '.$e->getMessage());
            }
        }

        $mbSaved = round($stats['bytes_saved'] / (1024 * 1024), 2);
        $totalFiles = $stats['portfolios'] + $stats['avatars'] + $stats['certificates'] + $stats['schools'] + $stats['editor'] + $stats['other_files'];

        $this->newLine();
        $this->info('=== Ringkasan Hasil Konversi ===');
        $this->table(
            ['Kategori', 'Jumlah Dikonversi'],
            [
                ['Cover Portofolio', $stats['portfolios']],
                ['Foto Profil (Avatar)', $stats['avatars']],
                ['Sertifikat (Gambar)', $stats['certificates']],
                ['Logo Sekolah', $stats['schools']],
                ['Gambar Editor (CKEditor)', $stats['editor']],
                ['Berkas Storage Lainnya', $stats['other_files']],
                ['Total Berkas Fisik Dikonversi', $totalFiles],
                ['Total Baris Database Diperbarui', $stats['db_updated']],
                ['Estimasi Ruang Penyimpanan Dihemat', "{$mbSaved} MB ({$stats['bytes_saved']} bytes)"],
            ]
        );

        if ($stats['errors'] > 0) {
            $this->warn("Terdapat {$stats['errors']} berkas yang gagal dikonversi.");
            $this->warn("Pastikan permission folder storage benar dengan menjalankan: 'chown -R www:www storage'");
        } else {
            $this->info('Selesai dengan sukses!');
        }

        return self::SUCCESS;
    }
}
