<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $portfolios = $user->visiblePortfolios()
            ->latest()
            ->limit(5)
            ->get();
        $teamInvitations = $user->contributedPortfolios()
            ->wherePivot('status', 'pending')
            ->with('user')
            ->latest('portfolio_contributors.created_at')
            ->get();
        $certificates = $user->certificates()->latest()->limit(3)->get();

        return view('siswa.dashboard', compact('user', 'portfolios', 'teamInvitations', 'certificates'));
    }
}
