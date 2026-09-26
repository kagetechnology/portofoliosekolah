<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Portfolio;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'pending_portfolios' => Portfolio::where('approval_status', 'pending')->count(),
            'pending_certificates' => Certificate::where('approval_status', 'pending')->count(),
            'total_views' => Portfolio::sum('views'),
            'rated' => auth()->user()->portfolioRatings()->count(),
        ];
        $portfolios = Portfolio::with('user')->withAvg('ratings', 'rating')->latest()->limit(6)->get();
        $certificates = Certificate::with('user')->latest()->limit(6)->get();

        return view('guru.dashboard', compact('stats', 'portfolios', 'certificates'));
    }
}
