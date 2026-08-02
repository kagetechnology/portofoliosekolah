<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Models\Skill;
use App\Support\RichText;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function index(Request $request): View
    {
        $portfolios = $request->user()
            ->portfolios()
            ->with('skills')
            ->latest()
            ->paginate(10);
        return view('siswa.portfolios.index', compact('portfolios'));
    }

    public function create(): View
    {
        return view('siswa.portfolios.create', ['skills' => Skill::orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $portfolio = new Portfolio($data);
        $portfolio->user_id = $request->user()->id;
        $portfolio->approval_status = 'pending';
        $portfolio->save();
        $this->syncSkills($portfolio, $request);
        return redirect()->route('siswa.portfolios.index')->with('status', 'Portofolio dibuat.');
    }

    public function edit(Portfolio $portfolio): View
    {
        $this->authorize($portfolio);
        return view('siswa.portfolios.edit', [
            'portfolio' => $portfolio,
            'skills' => Skill::orderBy('name')->get(),
            'selectedSkills' => $portfolio->skills->pluck('id')->all(),
            'selectedLevels' => $portfolio->skills->pluck('pivot.level', 'id')->all(),
        ]);
    }

    public function update(Request $request, Portfolio $portfolio): RedirectResponse
    {
        $this->authorize($portfolio);
        $data = $this->validated($request, $portfolio);
        $data['approval_status'] = 'pending';
        $data['is_featured'] = false;
        $portfolio->update($data);
        $this->syncSkills($portfolio, $request);
        return redirect()->route('siswa.portfolios.index')->with('status', 'Portofolio diperbarui.');
    }

    public function destroy(Request $request, Portfolio $portfolio): RedirectResponse
    {
        $this->authorize($portfolio);
        if ($portfolio->cover_image) {
            Storage::disk('public')->delete($portfolio->cover_image);
        }
        $portfolio->delete();
        return redirect()->route('siswa.portfolios.index')->with('status', 'Portofolio dihapus.');
    }

    private function validated(Request $request, ?Portfolio $portfolio = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
            'project_url' => ['nullable', 'url', 'max:255'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'skill_names' => ['nullable', 'array', 'max:20'],
            'skill_names.*' => ['nullable', 'string', 'max:50'],
            'skill_levels' => ['nullable', 'array', 'max:20'],
            'skill_levels.*' => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        $data['description'] = RichText::clean($data['description']) ?? '';

        return $data;
    }

    private function syncSkills(Portfolio $portfolio, Request $request): void
    {
        $names = (array) $request->input('skill_names', []);
        $levels = (array) $request->input('skill_levels', []);
        $skills = Skill::findOrCreateMany($names);
        $sync = [];
        foreach ($skills as $id => $skill) {
            $sync[$id] = ['level' => max(1, min(5, (int) ($levels[$id] ?? 1)))];
        }
        $portfolio->skills()->sync($sync);
    }

    private function authorize(Portfolio $portfolio): void
    {
        if ($portfolio->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
