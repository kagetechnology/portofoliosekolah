<?php

namespace Database\Factories;

use App\Models\Portfolio;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PortfolioFactory extends Factory
{
    protected $model = Portfolio::class;

    private array $titles = [
        'Website Sekolah dengan Laravel',
        'Aplikasi Mobile Inventory',
        'Sistem Kasir POS Berbasis Web',
        'E-Commerce Toko Online dengan React',
        'Portfolio Pribadi dengan Tailwind CSS',
        'Platform LMS untuk Sekolah',
        'Aplikasi Cuaca Real-time',
        'Dashboard Analitik dengan Chart.js',
        'Sistem Antrian Puskesmas',
        'Blog Multi-Bahasa dengan i18n',
        'API REST untuk Mobile Banking',
        'Chat Realtime dengan WebSocket',
        'Sistem Inventaris Sekolah',
        'Game Puzzle dengan HTML5 Canvas',
        'To-Do List dengan Vue.js',
        'Sistem Absensi QR Code',
        'Manajemen Perpustakaan Digital',
        'Website Wedding Invitation',
        'E-Voting OSIS dengan Blockchain',
        'Movie Database dengan TMDB API',
        'Sistem Reservasi Hotel',
        'Task Management ala Trello',
        'E-Learning Platform Lengkap',
        'CRUD API dengan JWT Auth',
        'Sistem Kas Sekolah Digital',
        'Company Profile PT. Contoh',
        'Forum Diskusi Siswa',
        'Portal Berita Sekolah',
        'Sistem Booking Ruang Rapat',
        'Sistem Antrian Loket',
    ];

    private array $descriptions = [
        'Aplikasi yang dibuat untuk memenuhi tugas akhir. Fitur lengkap dengan CRUD, authentication, dan dashboard admin.',
        'Project pribadi untuk mengasah skill. Memakai best practice dan struktur kode yang clean.',
        'Kerja sama dengan teman sekelas untuk membuat sistem ini. Bertanggung jawab pada bagian backend dan database.',
        'Implementasi dari pembelajaran di kelas. Fokus pada UX yang responsif di semua device.',
        'Build dengan stack modern. Performa cepat, accessibility-friendly, dan SEO-ready.',
        'Project eksplorasi AI dan machine learning. Dataset diambil dari Kaggle, model dilatih dengan TensorFlow.',
        'Solusi digital untuk UMKM sekitar sekolah. Digunakan oleh 10+商家 aktif setiap hari.',
    ];

    private array $categories = ['Web', 'Mobile', 'Desktop', 'IoT', 'UI/UX', 'Data', 'Game', 'API'];

    public function definition(): array
    {
        $title = fake()->randomElement($this->titles).' '.Str::random(3);

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => null,
            'description' => fake()->randomElement($this->descriptions),
            'category' => fake()->randomElement($this->categories),
            'cover_image' => null,
            'project_url' => fake()->boolean(40) ? fake()->url() : null,
            'github_url' => fake()->boolean(70) ? 'https://github.com/'.fake()->userName().'/'.Str::random(8) : null,
            'is_featured' => fake()->boolean(20),
            'views' => fake()->numberBetween(0, 350),
        ];
    }
}
