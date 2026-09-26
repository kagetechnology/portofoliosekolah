<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ImageOptimizer
{
    /**
     * Resize, compress, and store an uploaded image as an optimized WebP file.
     * Falls back to standard storage if GD is unavailable or the file is not an optimizable image.
     */
    public static function optimizeAndStore(
        UploadedFile $file,
        string $directory,
        int $maxWidth = 1600,
        int $maxHeight = 1600,
        int $quality = 82
    ): string {
        $directory = trim($directory, '/');

        // Non-image files (e.g. PDFs) or if GD is missing, store directly
        if (! self::isOptimizableImage($file)) {
            return $file->store($directory, 'public');
        }

        try {
            $rawContent = file_get_contents($file->getRealPath());
            if ($rawContent === false || $rawContent === '') {
                return $file->store($directory, 'public');
            }

            $image = @imagecreatefromstring($rawContent);
            if (! $image) {
                return $file->store($directory, 'public');
            }

            // Correct EXIF orientation if available
            $image = self::correctOrientation($image, $file->getRealPath());

            // Resize if exceeds bounds
            $origWidth = imagesx($image);
            $origHeight = imagesy($image);

            if ($origWidth > $maxWidth || $origHeight > $maxHeight) {
                $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);
                $targetWidth = max(1, (int) round($origWidth * $ratio));
                $targetHeight = max(1, (int) round($origHeight * $ratio));

                $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
                imagealphablending($canvas, false);
                imagesavealpha($canvas, true);

                imagecopyresampled(
                    $canvas,
                    $image,
                    0, 0, 0, 0,
                    $targetWidth,
                    $targetHeight,
                    $origWidth,
                    $origHeight
                );

                $image = $canvas;
            } else {
                imagealphablending($image, false);
                imagesavealpha($image, true);
            }

            ob_start();
            $success = function_exists('imagewebp')
                ? imagewebp($image, null, $quality)
                : imagejpeg($image, null, $quality);
            $optimizedData = ob_get_clean();

            if (! $success || empty($optimizedData)) {
                return $file->store($directory, 'public');
            }

            $extension = function_exists('imagewebp') ? 'webp' : 'jpg';
            $filename = Str::random(40).'.'.$extension;
            $relativePath = $directory.'/'.$filename;

            Storage::disk('public')->put($relativePath, $optimizedData);

            return $relativePath;
        } catch (Throwable $e) {
            Log::warning('Gagal mengoptimalkan gambar: '.$e->getMessage(), ['file' => $file->getClientOriginalName()]);

            return $file->store($directory, 'public');
        }
    }

    /**
     * Convert an existing image file on the public disk to WebP.
     * Retains same base filename and replaces extension with .webp.
     */
    public static function convertFile(
        string $relativePath,
        int $maxWidth = 1600,
        int $maxHeight = 1600,
        int $quality = 82,
        bool $deleteOriginal = true
    ): ?string {
        if (! extension_loaded('gd') || ! function_exists('imagewebp')) {
            return null;
        }

        $disk = Storage::disk('public');
        if (! $disk->exists($relativePath)) {
            return null;
        }

        $extension = strtolower(pathinfo($relativePath, PATHINFO_EXTENSION));
        if ($extension === 'webp') {
            return $relativePath;
        }

        if (! in_array($extension, ['jpg', 'jpeg', 'png', 'bmp', 'avif'], true)) {
            return null;
        }

        try {
            $rawContent = $disk->get($relativePath);
            if ($rawContent === false || $rawContent === '') {
                return null;
            }

            $image = @imagecreatefromstring($rawContent);
            if (! $image) {
                return null;
            }

            $origWidth = imagesx($image);
            $origHeight = imagesy($image);

            if ($origWidth > $maxWidth || $origHeight > $maxHeight) {
                $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);
                $targetWidth = max(1, (int) round($origWidth * $ratio));
                $targetHeight = max(1, (int) round($origHeight * $ratio));

                $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
                imagealphablending($canvas, false);
                imagesavealpha($canvas, true);

                imagecopyresampled(
                    $canvas,
                    $image,
                    0, 0, 0, 0,
                    $targetWidth,
                    $targetHeight,
                    $origWidth,
                    $origHeight
                );

                $image = $canvas;
            } else {
                imagealphablending($image, false);
                imagesavealpha($image, true);
            }

            ob_start();
            $success = imagewebp($image, null, $quality);
            $optimizedData = ob_get_clean();

            if (! $success || empty($optimizedData)) {
                return null;
            }

            $dir = pathinfo($relativePath, PATHINFO_DIRNAME);
            $filename = pathinfo($relativePath, PATHINFO_FILENAME);
            $newPath = ($dir === '.' || $dir === '' ? '' : $dir.'/').$filename.'.webp';

            $disk->put($newPath, $optimizedData);

            if ($deleteOriginal && $newPath !== $relativePath) {
                $disk->delete($relativePath);
            }

            return $newPath;
        } catch (Throwable $e) {
            Log::warning('Gagal mengonversi file ke WebP: '.$e->getMessage(), ['file' => $relativePath]);

            return null;
        }
    }

    private static function isOptimizableImage(UploadedFile $file): bool
    {
        if (! extension_loaded('gd')) {
            return false;
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $mime = strtolower((string) $file->getMimeType());

        if (in_array($extension, ['pdf', 'svg', 'heic'], true) || str_contains($mime, 'pdf') || str_contains($mime, 'svg')) {
            return false;
        }

        return in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'bmp', 'avif'], true)
            || str_starts_with($mime, 'image/');
    }

    private static function correctOrientation($image, string $path)
    {
        if (! function_exists('exif_read_data')) {
            return $image;
        }

        try {
            $exif = @exif_read_data($path);
            if (! empty($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 3:
                        return imagerotate($image, 180, 0);
                    case 6:
                        return imagerotate($image, -90, 0);
                    case 8:
                        return imagerotate($image, 90, 0);
                }
            }
        } catch (Throwable) {
            // Ignore EXIF errors
        }

        return $image;
    }
}
