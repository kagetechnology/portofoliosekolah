<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Contact;
use App\Models\Portfolio;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'pending_users' => User::where('status', 'pending')->count(),
            'active_siswa' => User::where('role', 'siswa')->where('status', 'active')->count(),
            'pending_portfolios' => Portfolio::where('approval_status', 'pending')->count(),
            'pending_certificates' => Certificate::where('approval_status', 'pending')->count(),
            'portfolios' => Portfolio::where('approval_status', 'approved')->count(),
            'unread_messages' => Contact::where('is_read', false)->count(),
            'total_views' => (int) Portfolio::sum('views'),
        ];

        $pendingUsers = User::where('status', 'pending')->latest()->limit(5)->get();
        $latestMessages = Contact::latest()->limit(5)->get();
        $topPortfolios = Portfolio::with('user')
            ->where('approval_status', 'approved')
            ->where('views', '>', 0)
            ->orderByDesc('views')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'pendingUsers', 'latestMessages', 'topPortfolios'));
    }
}
