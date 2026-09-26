<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SkillController extends Controller
{
    public function index(Request $request): View
    {
        $q = $request->string('q')->toString();
        $skills = Skill::withCount('portfolios')
            ->when($q !== '', fn ($query) => $query->where('name', 'like', "%$q%"))
            ->orderBy('name')
            ->paginate(30)
            ->withQueryString();

        return view('admin.skills.index', compact('skills', 'q'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['name' => trim($request->string('name')->toString())]);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:skills,name'],
        ]);

        Skill::create(['name' => trim($data['name'])]);

        return back()->with('status', 'Skill ditambahkan.');
    }

    public function update(Request $request, Skill $skill): RedirectResponse
    {
        $request->merge(['name' => trim($request->string('name')->toString())]);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', Rule::unique('skills', 'name')->ignore($skill)],
        ]);

        $skill->update(['name' => trim($data['name'])]);

        return back()->with('status', 'Skill diperbarui.');
    }

    public function destroy(Skill $skill): RedirectResponse
    {
        if ($skill->portfolios()->exists()) {
            return back()->withErrors(['skill' => 'Skill masih digunakan portfolio dan tidak dapat dihapus.']);
        }

        $skill->delete();

        return back()->with('status', 'Skill dihapus.');
    }
}
