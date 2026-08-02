<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Models\School;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $school = School::current();
        $featured = Portfolio::with('user', 'skills')->where('approval_status', 'approved')->where('is_featured', true)->latest()->limit(6)->get();
        $latest = Portfolio::with('user')->where('approval_status', 'approved')->latest()->limit(6)->get();

        $stats = [
            'siswa' => User::where('role', 'siswa')->where('status', 'active')->count(),
            'portfolios' => Portfolio::where('approval_status', 'approved')->count(),
            'skills' => Skill::count(),
        ];

        $skillCategories = Skill::query()
            ->withCount(['portfolios'])
            ->having('portfolios_count', '>', 0)
            ->orderByDesc('portfolios_count')
            ->limit(12)
            ->get();

        $categoryGroups = Portfolio::query()
            ->where('approval_status', 'approved')
            ->whereNotNull('category')
            ->select('category')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(3)
            ->pluck('category');

        $categoryPreviews = collect();
        foreach ($categoryGroups as $cat) {
            $categoryPreviews[$cat] = Portfolio::with('user', 'skills')
                ->where('approval_status', 'approved')
                ->where('category', $cat)
                ->latest()
                ->limit(4)
                ->get();
        }
        $categoryPreviews = $categoryPreviews->filter(fn ($g) => $g->count() > 0);

        $topStudents = User::where('role', 'siswa')
            ->where('status', 'active')
            ->withCount(['portfolios' => fn ($qb) => $qb->where('approval_status', 'approved')])
            ->having('portfolios_count', '>', 0)
            ->orderByDesc('portfolios_count')
            ->limit(4)
            ->get();

        $popular = Portfolio::with('user', 'skills')
            ->where('approval_status', 'approved')
            ->where('views', '>', 0)
            ->orderByDesc('views')
            ->limit(4)
            ->get();

        return view('public.home', compact(
            'school', 'featured', 'latest', 'popular', 'stats',
            'skillCategories', 'categoryPreviews', 'topStudents'
        ));
    }
}
