<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake('id_ID')->name(),
            'email' => fake('id_ID')->unique()->safeEmail(),
            'role' => 'siswa',
            'status' => 'active',
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'phone' => fake('id_ID')->numerify('08##########'),
            'school_class' => fake('id_ID')->randomElement(['XII RPL 1', 'XII RPL 2', 'XII TKJ 1', 'XII TKJ 2', 'XI RPL 1', 'XI RPL 2', 'XII MM 1']),
            'tahun_masuk' => fake('id_ID')->randomElement([2021, 2022, 2023, 2024]),
            'bio' => fake('id_ID')->sentence(12),
            'github_url' => 'https://github.com/'.fake('id_ID')->userName(),
            'instagram_url' => 'https://instagram.com/'.fake('id_ID')->userName(),
            'slug' => null,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => ['email_verified_at' => null]);
    }
}
