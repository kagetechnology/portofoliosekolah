<?php

namespace Database\Factories;

use App\Models\Portfolio;
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
        'Aplikasi ini dikembangkan untuk memenuhi tugas akhir kejuruan dengan menerapkan standar industri modern. Fitur lengkap mencakup autentikasi multi-peran, manajemen data berbasis CRUD yang aman, pelaporan interaktif, dan antarmuka responsif yang ramah pengguna. Seluruh alur sistem telah diuji fungsionalitasnya agar siap dipakai oleh pengguna akhir.',
        'Project pribadi yang dirancang secara mandiri untuk mengasah penguasaan arsitektur kode bersih dan best practice industri. Memanfaatkan teknologi modern dengan performa tinggi, validasi input berlapis, serta pengolahan data terstruktur. Dokumentasi teknis dan alur antarmuka disusun rapi agar mudah dipelajari serta dikembangkan lebih lanjut.',
        'Karya kolaborasi bersama tim untuk menyelesaikan permasalahan operasional nyata di lingkungan sekolah. Tim bertanggung jawab mulai dari perancangan skema basis data, pembuatan REST API terintegrasi, hingga integrasi antarmuka yang intuitif. Melalui pembagian tugas yang terstruktur, proyek ini berhasil diselesaikan sesuai tenggat waktu yang ditetapkan.',
        'Implementasi nyata dari materi kejuruan dengan fokus pada pengalaman pengguna (UX) yang cepat dan adaptif di berbagai perangkat. Aplikasi dilengkapi dengan sistem pencarian instan, validasi formulir terpadu, dan integrasi media dokumentasi yang memudahkan pengguna dalam meninjau detail karya secara menyeluruh.',
        'Solusi perangkat lunak yang dibangun menggunakan stack modern berorientasi performa tinggi dan ramah aksesibilitas. Menyediakan alur kerja digital yang efisien, pelaporan data real-time, serta struktur kode modular yang mudah diuji. Karya ini menjadi sarana pembuktian kompetensi teknis yang relevan dengan kebutuhan industri masa kini.',
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
