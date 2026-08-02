<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@sekolah.test'],
            [
                'name' => 'Admin Sekolah',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'siswa@sekolah.test'],
            [
                'name' => 'Siswa Contoh',
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'status' => 'active',
                'school_class' => 'XII RPL 1',
                'email_verified_at' => now(),
                'bio' => 'Suka web dev & UI design.',
            ]
        );

        School::updateOrCreate(
            ['name' => 'SMK Negeri 1 Contoh'],
            [
                'address' => 'Jl. Pendidikan No. 1, Kota Contoh',
                'phone' => '(021) 123-4567',
                'email' => 'info@sekolah.test',
                'description' => 'Sekolah unggulan bidang teknologi dan bisnis.',
                'vision' => 'Menjadi sekolah bertaraf internasional.',
                'mission' => '1. Meningkatkan kualitas sumber daya manusia.\n2. Mengembangkan teknologi pembelajaran.',
                'kepala_sekolah' => 'Drs. Kepala Sekolah',
                'npsn' => '12345678',
            ]
        );

        foreach (['Laravel', 'PHP', 'JavaScript', 'React', 'Tailwind', 'MySQL', 'Figma', 'Python'] as $name) {
            Skill::firstOrCreate(['name' => $name]);
        }
    }
}
