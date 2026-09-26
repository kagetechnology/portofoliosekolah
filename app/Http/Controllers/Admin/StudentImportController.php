<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use App\Support\SimpleXlsx;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentImportController extends Controller
{
    public function create()
    {
        return view('admin.users.import', [
            'defaultPassword' => School::studentDefaultPassword(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        @set_time_limit(300);

        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx', 'max:10240'],
        ]);

        $rows = SimpleXlsx::rows($data['file']->getRealPath());
        $headerIndex = collect($rows)->search(fn (array $row) => in_array('NISN', $row, true) && in_array('Nama', $row, true));
        abort_if($headerIndex === false, 422, 'Header Nama dan NISN tidak ditemukan.');

        $headers = array_flip($rows[$headerIndex]);
        $result = DB::transaction(function () use ($rows, $headerIndex, $headers): array {
            $created = 0;
            $updated = 0;
            $records = [];
            $existing = User::whereNotNull('nisn')->pluck('nisn')->flip();
            $usedSlugs = array_fill_keys(User::whereNotNull('slug')->pluck('slug')->all(), true);
            $passwordHash = Hash::make(School::studentDefaultPassword());
            $now = now();

            foreach (array_slice($rows, $headerIndex + 1) as $row) {
                $nisn = preg_replace('/\D/', '', $row[$headers['NISN'] ?? ''] ?? '');
                $nisn = str_pad($nisn, 10, '0', STR_PAD_LEFT);
                $name = trim($row[$headers['Nama'] ?? ''] ?? '');
                if (! preg_match('/^\d{10}$/', $nisn) || $name === '') {
                    continue;
                }

                $email = $nisn.'@smkn1mas.sch.id';
                $class = trim($row[$headers['Rombel Saat Ini'] ?? ''] ?? '') ?: null;
                $isNew = ! $existing->has($nisn);
                $slug = null;
                if ($isNew) {
                    $base = Str::slug($name) ?: 'siswa';
                    $slug = $base;
                    $suffix = 2;
                    while (isset($usedSlugs[$slug])) {
                        $slug = $base.'-'.$suffix++;
                    }
                    $usedSlugs[$slug] = true;
                }
                $records[] = [
                    'nisn' => $nisn,
                    'name' => $name,
                    'email' => $email,
                    'password' => $passwordHash,
                    'must_change_password' => true,
                    'role' => 'siswa',
                    'status' => 'active',
                    'school_class' => $class,
                    'tahun_masuk' => $this->entryYear($class),
                    'slug' => $slug,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if ($isNew) {
                    $created++;
                } else {
                    $updated++;
                }
            }

            foreach (array_chunk($records, 500) as $chunk) {
                User::upsert(
                    $chunk,
                    ['nisn'],
                    ['name', 'email', 'status', 'school_class', 'tahun_masuk', 'updated_at']
                );
            }

            return compact('created', 'updated');
        });

        return redirect()->route('admin.users.index')->with(
            'status',
            "Import selesai: {$result['created']} akun baru, {$result['updated']} akun diperbarui."
        );
    }

    private function entryYear(?string $class): ?int
    {
        if (! preg_match('/^(XII|XI|X)\b/i', $class ?? '', $match)) {
            return null;
        }

        return now()->year - match (strtoupper($match[1])) {
            'XII' => 2,
            'XI' => 1,
            default => 0,
        };
    }
}
