<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function index(Request $request): View
    {
        $q = $request->string('q')->toString();
        $skill = $request->string('skill')->toString();
        $category = $request->string('category')->toString();

        $portfolios = Portfolio::with(['user', 'skills'])
            ->where('approval_status', 'approved')
            ->when($q !== '', fn ($qb) => $qb->where(function ($w) use ($q) {
                $w->where('title', 'like', "%$q%")
                    ->orWhere('description', 'like', "%$q%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%$q%"));
            }))
            ->when($skill !== '', fn ($qb) => $qb->whereHas('skills', fn ($s) => $s->where('skills.id', $skill)))
            ->when($category !== '', fn ($qb) => $qb->where('category', $category))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $skills = Skill::withCount(['portfolios' => fn ($qb) => $qb->where('approval_status', 'approved')])->orderBy('name')->get();
        $categories = Portfolio::where('approval_status', 'approved')->whereNotNull('category')->distinct()->pluck('category');

        $topStudents = User::where('role', 'siswa')
            ->where('status', 'active')
            ->withCount(['portfolios' => fn ($qb) => $qb->where('approval_status', 'approved')])
            ->having('portfolios_count', '>', 0)
            ->orderByDesc('portfolios_count')
            ->limit(5)
            ->get();

        return view('public.portfolios.index', compact('portfolios', 'skills', 'categories', 'q', 'skill', 'category', 'topStudents'));
    }

    public function show(Portfolio $portfolio): View
    {
        $portfolio->load(['user.portfolios', 'skills', 'acceptedContributors'])->loadAvg('ratings', 'rating')->loadCount('ratings');
        abort_unless($portfolio->isApproved(), 404);
        $portfolio->increment('views');

        return view('public.portfolios.show', ['portfolio' => $portfolio, 'isPreview' => false]);
    }

    public function preview(Portfolio $portfolio): View
    {
        $portfolio->load(['user.portfolios', 'skills', 'acceptedContributors'])->loadAvg('ratings', 'rating')->loadCount('ratings');

        return view('public.portfolios.show', ['portfolio' => $portfolio, 'isPreview' => true]);
    }

    public function byUser(User $user): View
    {
        abort_unless($user->isSiswa() && $user->isActive(), 404);
        $portfolioQuery = $user->visiblePortfolios()->where('approval_status', 'approved');
        $portfolioIds = (clone $portfolioQuery)->pluck('portfolios.id');
        $portfolios = $portfolioQuery
            ->with('skills')
            ->latest()
            ->paginate(12);
        $certificates = $user->certificates()->where('approval_status', 'approved')->latest()->get();
        $skillSummary = DB::table('skills')
            ->join('portfolio_skill', 'skills.id', '=', 'portfolio_skill.skill_id')
            ->whereIn('portfolio_skill.portfolio_id', $portfolioIds)
            ->groupBy('skills.id', 'skills.name')
            ->selectRaw('skills.id, skills.name, AVG(portfolio_skill.level) as avg_level, COUNT(DISTINCT portfolio_skill.portfolio_id) as used_in')
            ->orderByDesc('avg_level')
            ->get();

        return view('public.portfolios.user', compact('user', 'portfolios', 'certificates', 'skillSummary'));
    }
}
