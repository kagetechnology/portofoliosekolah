<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Portfolio;
use App\Models\School;
use App\Models\Skill;
use App\Models\User;
use Illuminate\View\View;

class ForCompaniesController extends Controller
{
    public function index(): View
    {
        $school = School::current();

        $totalStudents = User::where('role', 'siswa')->where('status', 'active')->count();
        $totalPortfolios = Portfolio::where('approval_status', 'approved')->count();
        $totalSkills = Skill::count();
        $totalHiringPartners = Contact::distinct('sender_company')->whereNotNull('sender_company')->count();

        $categories = Portfolio::where('approval_status', 'approved')->whereNotNull('category')
            ->select('category')->distinct()->pluck('category');

        $topStudents = User::where('role', 'siswa')
            ->where('status', 'active')
            ->withCount(['portfolios' => fn ($qb) => $qb->where('approval_status', 'approved')])
            ->orderByDesc('portfolios_count')
            ->limit(8)
            ->get();

        $featuredPortfolios = Portfolio::with('user', 'skills')
            ->where('approval_status', 'approved')
            ->where('is_featured', true)
            ->latest()
            ->limit(6)
            ->get();

        $steps = [
            ['icon' => 'search', 'title' => 'Eksplor Portofolio', 'desc' => 'Filter berdasarkan skill, kategori, kelas, atau siswa spesifik.', 'detail' => '100+ karya'],
            ['icon' => 'mail', 'title' => 'Kirim Pesan', 'desc' => 'Form kontak otomatis sampai ke admin sekolah untuk verifikasi.', 'detail' => '< 24 jam'],
            ['icon' => 'users', 'title' => 'Interview', 'desc' => 'Admin memfasilitasi jadwal interview sesuai kebutuhan Anda.', 'detail' => 'Sesuai jadwal'],
            ['icon' => 'check-circle', 'title' => 'Rekrut', 'desc' => 'Mulai program magang, kontrak, atau full-time langsung dengan siswa.', 'detail' => 'Tanpa biaya'],
        ];

        $benefits = [
            ['icon' => 'sparkles', 'title' => 'Talenta Tervalidasi', 'desc' => 'Setiap karya diverifikasi admin sekolah. Data skill akurat dengan level 1–5.'],
            ['icon' => 'briefcase', 'title' => 'Hemat Waktu', 'desc' => 'Tidak perlu筛选 CV. Karya jadi bukti kemampuan langsung. Lebih cepat dari proses konvensional.'],
            ['icon' => 'lock', 'title' => 'Privasi Terjaga', 'desc' => 'Data siswa hanya dibagikan ke sekolah. Anda pesan lewat admin, bukan kontak langsung.'],
            ['icon' => 'users', 'title' => 'Jaringan Sekolah', 'desc' => 'Sekolah memfasilitasi interview, surat rekomendasi, dan komunikasi awal.'],
            ['icon' => 'award', 'title' => 'Skill Standar Industri', 'desc' => 'Kurikulum berbasis proyek. Siswa terbiasa dengan workflow dan tooling profesional.'],
            ['icon' => 'mail', 'title' => 'Tanpa Biaya', 'desc' => 'Platform gratis untuk perusahaan. Tidak ada komisi rekrutmen.'],
        ];

        $stats = [
            ['label' => 'Siswa Aktif', 'value' => $totalStudents],
            ['label' => 'Portofolio Published', 'value' => $totalPortfolios],
            ['label' => 'Skill Tracked', 'value' => $totalSkills],
            ['label' => 'Perusahaan Partner', 'value' => $totalHiringPartners],
        ];

        return view('public.for-companies.index', compact(
            'school', 'categories', 'topStudents', 'featuredPortfolios',
            'steps', 'benefits', 'stats'
        ));
    }
}
