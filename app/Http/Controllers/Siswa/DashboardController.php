<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $portfolios = $user->portfolios()->latest()->limit(5)->get();
        $certificates = $user->certificates()->latest()->limit(3)->get();

        return view('siswa.dashboard', compact('user', 'portfolios', 'certificates'));
    }
}
