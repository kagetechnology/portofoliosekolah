<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StudentDirectoryController extends Controller
{
    public function index(Request $request): View
    {
        $q = $request->string('q')->toString();
        $skillsParam = $request->input('skills', []);
        $skills = is_array($skillsParam) ? array_filter(array_map('intval', $skillsParam)) : [];
        $kelas = $request->string('kelas')->toString();
        $tahun = $request->string('tahun')->toString();
        $sort = $request->string('sort')->toString();

        $students = User::where('role', 'siswa')
            ->where('status', 'active')
            ->withCount([
                'portfolios as owned_portfolios_count' => fn ($qb) => $qb->where('approval_status', 'approved'),
                'acceptedContributedPortfolios as contributed_portfolios_count' => fn ($qb) => $qb->where('approval_status', 'approved'),
            ])
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%$q%")
                        ->orWhere('bio', 'like', "%$q%")
                        ->orWhere('school_class', 'like', "%$q%");
                });
            })
            ->when($skills !== [], function ($qb) use ($skills) {
                $qb->where(function ($query) use ($skills) {
                    $query->whereHas('portfolios', fn ($portfolios) => $portfolios
                        ->where('approval_status', 'approved')
                        ->whereHas('skills', fn ($skill) => $skill->whereIn('skills.id', $skills)))
                        ->orWhereHas('acceptedContributedPortfolios', fn ($portfolios) => $portfolios
                            ->where('approval_status', 'approved')
                            ->whereHas('skills', fn ($skill) => $skill->whereIn('skills.id', $skills)));
                });
            })
            ->when($kelas !== '', fn ($qb) => $qb->where('school_class', $kelas))
            ->when($tahun !== '', fn ($qb) => $qb->where('tahun_masuk', (int) $tahun))
            ->when($sort === 'portfolios', fn ($qb) => $qb
                ->orderByRaw('(owned_portfolios_count + contributed_portfolios_count) DESC'))
            ->when($sort === 'recent', fn ($qb) => $qb->latest())
            ->when($sort === 'name', fn ($qb) => $qb->orderBy('name'))
            ->when(! in_array($sort, ['portfolios', 'recent', 'name'], true), fn ($qb) => $qb->latest())
            ->paginate(12)
            ->withQueryString();

        $studentIds = $students->getCollection()->pluck('id');
        $portfolioOwners = DB::table('portfolios')
            ->where('approval_status', 'approved')
            ->whereIn('user_id', $studentIds)
            ->get(['id as portfolio_id', 'user_id']);
        $portfolioContributors = DB::table('portfolios')
            ->join('portfolio_contributors', 'portfolios.id', '=', 'portfolio_contributors.portfolio_id')
            ->where('portfolios.approval_status', 'approved')
            ->where('portfolio_contributors.status', 'accepted')
            ->whereIn('portfolio_contributors.user_id', $studentIds)
            ->get(['portfolios.id as portfolio_id', 'portfolio_contributors.user_id']);
        $portfolioIdsByStudent = $portfolioOwners
            ->concat($portfolioContributors)
            ->groupBy('user_id')
            ->map(fn ($rows) => $rows->pluck('portfolio_id')->unique());
        $skillsByPortfolio = DB::table('skills')
            ->join('portfolio_skill', 'skills.id', '=', 'portfolio_skill.skill_id')
            ->whereIn('portfolio_skill.portfolio_id', $portfolioIdsByStudent->flatten()->unique())
            ->get(['skills.id', 'skills.name', 'portfolio_skill.portfolio_id', 'portfolio_skill.level'])
            ->groupBy('portfolio_id');
        $skillSummaries = $studentIds->mapWithKeys(function ($studentId) use ($portfolioIdsByStudent, $skillsByPortfolio) {
            $summary = collect($portfolioIdsByStudent->get($studentId, []))
                ->flatMap(fn ($portfolioId) => $skillsByPortfolio->get($portfolioId, collect()))
                ->groupBy('id')
                ->map(fn ($items) => (object) [
                    'name' => $items->first()->name,
                    'avg_level' => $items->avg('level'),
                    'used_in' => $items->pluck('portfolio_id')->unique()->count(),
                ])
                ->sortByDesc('avg_level')
                ->values();

            return [$studentId => $summary];
        });

        $allSkills = Skill::orderBy('name')->get();
        $kelasList = User::where('role', 'siswa')
            ->whereNotNull('school_class')
            ->where('school_class', '!=', '')
            ->distinct()
            ->orderBy('school_class')
            ->pluck('school_class');
        $tahunList = User::where('role', 'siswa')
            ->whereNotNull('tahun_masuk')
            ->distinct()
            ->orderByDesc('tahun_masuk')
            ->pluck('tahun_masuk');

        $totalActive = User::where('role', 'siswa')->where('status', 'active')->count();
        $totalPortfolios = Portfolio::where('approval_status', 'approved')->count();
        $totalSkills = Skill::count();

        return view('public.students.index', compact(
            'students', 'skillSummaries', 'allSkills', 'kelasList', 'tahunList',
            'q', 'skills', 'kelas', 'tahun', 'sort',
            'totalActive', 'totalPortfolios', 'totalSkills'
        ));
    }
}
