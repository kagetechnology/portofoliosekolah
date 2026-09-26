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
use Illuminate\View\View;

class TeacherImportController extends Controller
{
    public function create(): View
    {
        return view('admin.users.import-teachers', [
            'defaultPassword' => School::defaultAccountPassword(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        @set_time_limit(300);

        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx', 'max:10240'],
        ]);

        $rows = SimpleXlsx::rows($data['file']->getRealPath());
        $headerIndex = collect($rows)->search(fn (array $row) => in_array('Nama', $row, true)
            && in_array('Email', $row, true)
            && in_array('Jenis PTK', $row, true));
        abort_if($headerIndex === false, 422, 'Header Nama, Email, dan Jenis PTK tidak ditemukan.');

        $headers = array_flip($rows[$headerIndex]);
        $phoneColumn = $headers['HP'] ?? $headers['Telepon'] ?? null;
        $result = DB::transaction(function () use ($rows, $headerIndex, $headers, $phoneColumn): array {
            $records = collect(array_slice($rows, $headerIndex + 1))
                ->map(function (array $row) use ($headers, $phoneColumn): array {
                    return [
                        'name' => trim($row[$headers['Nama']] ?? ''),
                        'email' => Str::lower(trim($row[$headers['Email']] ?? '')),
                        'phone' => $phoneColumn ? trim($row[$phoneColumn] ?? '') ?: null : null,
                        'type' => trim($row[$headers['Jenis PTK']] ?? ''),
                    ];
                })
                ->filter(fn (array $row) => $row['name'] !== ''
                    && $row['type'] === 'Guru'
                    && filter_var($row['email'], FILTER_VALIDATE_EMAIL))
                ->unique('email')
                ->values();

            $existing = User::whereIn('email', $records->pluck('email'))->get()->keyBy(fn (User $user) => Str::lower($user->email));
            $usedSlugs = array_fill_keys(User::whereNotNull('slug')->pluck('slug')->all(), true);
            $passwordHash = Hash::make(School::defaultAccountPassword());
            $created = 0;
            $updated = 0;
            $skipped = 0;

            foreach ($records as $record) {
                $user = $existing->get($record['email']);
                if ($user && ! $user->isGuru()) {
                    $skipped++;

                    continue;
                }

                if ($user) {
                    $user->update([
                        'name' => $record['name'],
                        'phone' => $record['phone'],
                        'status' => 'active',
                    ]);
                    $updated++;

                    continue;
                }

                $base = Str::slug($record['name']) ?: 'guru';
                $slug = $base;
                $suffix = 2;
                while (isset($usedSlugs[$slug])) {
                    $slug = $base.'-'.$suffix++;
                }
                $usedSlugs[$slug] = true;

                User::insert([
                    'name' => $record['name'],
                    'email' => $record['email'],
                    'password' => $passwordHash,
                    'must_change_password' => true,
                    'role' => 'guru',
                    'status' => 'active',
                    'phone' => $record['phone'],
                    'slug' => $slug,
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $created++;
            }

            return compact('created', 'updated', 'skipped');
        });

        return redirect()->route('admin.users.index')->with(
            'status',
            "Import guru selesai: {$result['created']} akun baru, {$result['updated']} akun diperbarui, {$result['skipped']} konflik dilewati.",
        );
    }
}
