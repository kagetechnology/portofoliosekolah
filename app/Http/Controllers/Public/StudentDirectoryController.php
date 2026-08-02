<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\Request;
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
            ->with(['portfolios.skills'])
            ->withCount(['portfolios' => fn ($qb) => $qb->where('approval_status', 'approved')])
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%$q%")
                        ->orWhere('bio', 'like', "%$q%")
                        ->orWhere('school_class', 'like', "%$q%");
                });
            })
            ->when($skills !== [], function ($qb) use ($skills) {
                $qb->whereHas('portfolios.skills', fn ($s) => $s->whereIn('skills.id', $skills));
            })
            ->when($kelas !== '', fn ($qb) => $qb->where('school_class', $kelas))
            ->when($tahun !== '', fn ($qb) => $qb->where('tahun_masuk', (int) $tahun))
            ->when($sort === 'portfolios', fn ($qb) => $qb->orderByDesc('portfolios_count'))
            ->when($sort === 'recent', fn ($qb) => $qb->latest())
            ->when($sort === 'name', fn ($qb) => $qb->orderBy('name'))
            ->when(! in_array($sort, ['portfolios', 'recent', 'name'], true), fn ($qb) => $qb->latest())
            ->paginate(12)
            ->withQueryString();

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
            'students', 'allSkills', 'kelasList', 'tahunList',
            'q', 'skills', 'kelas', 'tahun', 'sort',
            'totalActive', 'totalPortfolios', 'totalSkills'
        ));
    }
}
