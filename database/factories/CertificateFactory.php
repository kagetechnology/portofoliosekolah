<?php

namespace Database\Factories;

use App\Models\Certificate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CertificateFactory extends Factory
{
    protected $model = Certificate::class;

    private array $titles = [
        'AWS Cloud Practitioner',
        'Google UX Design Professional Certificate',
        'Microsoft Azure Fundamentals',
        'Meta Front-End Developer',
        'TensorFlow Developer Certificate',
        'Dicoding Memulai Pemrograman dengan Python',
        'Dicoding Belajar Dasar AWS Cloud',
        'Dicoding Belajar Membuat Front-End Web untuk Pemula',
        'Coursera HTML, CSS, and Javascript for Web Developers',
        'freeCodeCamp Responsive Web Design',
        'freeCodeCamp JavaScript Algorithms',
        'HackerRank SQL Intermediate',
        'HackerRank Python Basic',
        'BNSP Junior Web Developer',
        'BNSP Teknisi Komputer',
        'TOEFL iBT Score 550',
        'IELTS Academic 6.5',
        'Cisco CCNA Routing & Switching',
        'Mikrotik MTCNA',
        'Red Hat System Administration I',
    ];

    private array $issuers = [
        'AWS', 'Google', 'Microsoft', 'Meta', 'Coursera', 'Udacity', 'Dicoding', 'freeCodeCamp',
        'HackerRank', 'Cisco Networking Academy', 'Mikrotik', 'BNSP', 'British Council', 'ETS',
    ];

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->randomElement($this->titles).' '.fake()->randomElement(['2023', '2024', '2025']),
            'slug' => null,
            'certificate_number' => strtoupper(fake()->bothify('??####-####-####')),
            'issuer' => fake()->randomElement($this->issuers),
            'issue_date' => fake()->dateTimeBetween('-3 years', 'now'),
            'file' => null,
        ];
    }
}
