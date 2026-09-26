<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use Illuminate\View\View;

class StudentCvController extends Controller
{
    public function show(User $user): View
    {
        abort_unless($user->isSiswa() && $user->isActive(), 404);

        $portfolios = $user->visiblePortfolios()
            ->where('approval_status', 'approved')
            ->with('skills')
            ->latest()
            ->get();

        $skills = $portfolios->flatMap->skills
            ->groupBy('id')
            ->map(fn ($items) => (object) [
                'name' => $items->first()->name,
                'level' => round($items->avg(fn ($skill) => $skill->pivot->level)),
                'projects' => $items->count(),
            ])
            ->sortByDesc('level')
            ->values();

        $certificates = $user->certificates()
            ->where('approval_status', 'approved')
            ->latest('issue_date')
            ->get();

        return view('public.students.cv', [
            'user' => $user,
            'school' => School::current(),
            'portfolios' => $portfolios,
            'skills' => $skills,
            'certificates' => $certificates,
        ]);
    }
}
