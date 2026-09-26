<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Models\School;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $meta = Cache::remember('public.home.v2', now()->addMinutes(5), function (): array {
            $categories = Portfolio::query()
                ->where('approval_status', 'approved')
                ->whereNotNull('category')
                ->select('category')
                ->selectRaw('COUNT(*) as total')
                ->groupBy('category')
                ->orderByDesc('total')
                ->limit(3)
                ->pluck('category')
                ->all();

            return [
                'featured_ids' => Portfolio::where('approval_status', 'approved')->where('is_featured', true)->latest()->limit(6)->pluck('id')->all(),
                'latest_ids' => Portfolio::where('approval_status', 'approved')->latest()->limit(6)->pluck('id')->all(),
                'popular_ids' => Portfolio::where('approval_status', 'approved')->where('views', '>', 0)->orderByDesc('views')->limit(4)->pluck('id')->all(),
                'stats' => [
                    'siswa' => User::where('role', 'siswa')->where('status', 'active')->count(),
                    'portfolios' => Portfolio::where('approval_status', 'approved')->count(),
                    'skills' => Skill::count(),
                ],
                'skill_ids' => Skill::query()->withCount(['portfolios'])->having('portfolios_count', '>', 0)->orderByDesc('portfolios_count')->limit(12)->pluck('id')->all(),
                'categories' => $categories,
                'top_student_ids' => User::where('role', 'siswa')->where('status', 'active')
                    ->withCount(['portfolios' => fn ($query) => $query->where('approval_status', 'approved')])
                    ->having('portfolios_count', '>', 0)
                    ->orderByDesc('portfolios_count')
                    ->limit(4)
                    ->pluck('id')
                    ->all(),
            ];
        });

        $featured = $this->orderedPortfolios($meta['featured_ids'], ['user', 'skills']);
        $latest = $this->orderedPortfolios($meta['latest_ids'], ['user']);
        $popular = $this->orderedPortfolios($meta['popular_ids'], ['user', 'skills']);
        $skillCategories = Skill::withCount('portfolios')->whereIn('id', $meta['skill_ids'])->get()
            ->sortBy(fn ($skill) => array_search($skill->id, $meta['skill_ids'], true))->values();
        $topStudents = User::withCount(['portfolios' => fn ($query) => $query->where('approval_status', 'approved')])
            ->whereIn('id', $meta['top_student_ids'])->get()
            ->sortBy(fn ($user) => array_search($user->id, $meta['top_student_ids'], true))->values();
        $categoryPreviews = collect($meta['categories'])->mapWithKeys(fn ($category) => [
            $category => Portfolio::with('user', 'skills')->where('approval_status', 'approved')
                ->where('category', $category)->latest()->limit(4)->get(),
        ])->filter->isNotEmpty();

        return view('public.home', [
            'school' => School::current(),
            'stats' => $meta['stats'],
            'featured' => $featured,
            'latest' => $latest,
            'popular' => $popular,
            'skillCategories' => $skillCategories,
            'categoryPreviews' => $categoryPreviews,
            'topStudents' => $topStudents,
        ]);
    }

    private function orderedPortfolios(array $ids, array $relations)
    {
        return Portfolio::with($relations)->whereIn('id', $ids)->get()
            ->sortBy(fn ($portfolio) => array_search($portfolio->id, $ids, true))->values();
    }
}
