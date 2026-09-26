<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Portfolio;
use App\Models\Skill;
use App\Support\ImageOptimizer;
use App\Support\RichText;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function index(Request $request): View
    {
        $q = $request->string('q')->toString();
        $status = $request->string('status')->toString();

        $portfolios = Portfolio::with('user')
            ->when($q !== '', fn ($qb) => $qb->where(function ($w) use ($q) {
                $w->where('title', 'like', "%$q%")
                    ->orWhere('description', 'like', "%$q%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%$q%"));
            }))
            ->when(in_array($status, ['pending', 'approved', 'rejected'], true), fn ($qb) => $qb->where('approval_status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $routePrefix = $request->user()->isGuru() ? 'guru' : 'admin';

        return view('admin.portfolios.index', compact('portfolios', 'q', 'status', 'routePrefix'));
    }

    public function show(Request $request, Portfolio $portfolio): View
    {
        $portfolio->load(['user', 'skills', 'contributors'])->loadAvg('ratings', 'rating')->loadCount('ratings');
        $routePrefix = $request->user()->isGuru() ? 'guru' : 'admin';
        $myRating = $request->user()->isGuru()
            ? $portfolio->ratings()->where('user_id', $request->user()->id)->value('rating')
            : null;

        return view('admin.portfolios.show', compact('portfolio', 'routePrefix', 'myRating'));
    }

    public function edit(Request $request, Portfolio $portfolio): View
    {
        $portfolio->load('skills');
        $routePrefix = $request->user()->isGuru() ? 'guru' : 'admin';

        return view('admin.portfolios.edit', [
            'portfolio' => $portfolio,
            'routePrefix' => $routePrefix,
            'categories' => Category::orderBy('name')->get(),
            'skills' => Skill::orderBy('name')->get(),
            'selectedSkills' => $portfolio->skills->pluck('id')->all(),
            'selectedLevels' => $portfolio->skills->pluck('pivot.level', 'id')->all(),
        ]);
    }

    public function update(Request $request, Portfolio $portfolio): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $rawText = html_entity_decode(strip_tags(RichText::clean($value) ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $normalized = trim(preg_replace('/\s+/u', ' ', $rawText));
                    $length = mb_strlen($normalized);
                    if ($length < 300) {
                        $fail("Deskripsi karya wajib diisi minimal 300 karakter teks asli (saat ini baru {$length} karakter). Lengkapi latar belakang, fitur, dan dokumentasi karya.");
                    }
                },
            ],
            'category' => ['nullable', 'string', 'max:100', 'exists:categories,name'],
            'project_type' => ['required', 'in:personal,team'],
            'project_url' => ['nullable', 'url', 'max:255'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'skill_ids' => ['nullable', 'array', 'max:20'],
            'skill_ids.*' => ['integer', 'distinct', 'exists:skills,id'],
            'skill_levels' => ['nullable', 'array'],
            'skill_levels.*' => ['nullable', 'integer', 'between:1,5'],
        ]);

        $data['description'] = RichText::clean($data['description']) ?? '';
        if ($request->hasFile('cover_image')) {
            if ($portfolio->cover_image) {
                Storage::disk('public')->delete($portfolio->cover_image);
            }
            $data['cover_image'] = ImageOptimizer::optimizeAndStore(
                $request->file('cover_image'),
                'portfolios',
                1600,
                1000,
                82
            );
        } else {
            unset($data['cover_image']);
        }

        $portfolio->update($data);
        $levels = (array) $request->input('skill_levels', []);
        $skills = collect((array) $request->input('skill_ids', []))->mapWithKeys(
            fn ($id) => [(int) $id => ['level' => max(1, min(5, (int) ($levels[$id] ?? 1)))]],
        );
        $portfolio->skills()->sync($skills->all());
        if ($portfolio->project_type === 'personal') {
            $portfolio->contributors()->detach();
        }

        $routePrefix = $request->user()->isGuru() ? 'guru' : 'admin';

        return redirect()->route($routePrefix.'.portfolios.show', $portfolio)
            ->with('status', 'Portofolio dan skill diperbarui.');
    }

    public function approve(Portfolio $portfolio): RedirectResponse
    {
        $portfolio->update(['approval_status' => 'approved', 'rejection_note' => null]);

        return back()->with('status', 'Portfolio "'.$portfolio->title.'" disetujui.');
    }

    public function reject(Request $request, Portfolio $portfolio): RedirectResponse
    {
        $data = $request->validate([
            'rejection_note' => ['required', 'string', 'min:10', 'max:1000'],
        ]);
        $portfolio->update([
            'approval_status' => 'rejected',
            'rejection_note' => $data['rejection_note'],
            'is_featured' => false,
        ]);

        return back()->with('status', 'Portfolio "'.$portfolio->title.'" ditolak.');
    }

    public function rate(Request $request, Portfolio $portfolio): RedirectResponse
    {
        abort_unless($request->user()->isGuru(), 403);
        $data = $request->validate(['rating' => ['required', 'integer', 'between:1,5']]);

        $portfolio->ratings()->updateOrCreate(
            ['user_id' => $request->user()->id],
            ['rating' => $data['rating']],
        );

        return back()->with('status', 'Rating proyek disimpan.');
    }

    public function toggleFeatured(Request $request, Portfolio $portfolio): RedirectResponse
    {
        abort_unless($portfolio->isApproved(), 422, 'Portfolio harus disetujui sebelum dijadikan unggulan.');

        $portfolio->update(['is_featured' => ! $portfolio->is_featured]);

        return back()->with('status', 'Portfolio "'.$portfolio->title.'" '.(! $portfolio->is_featured ? 'dihapus dari' : 'ditambahkan ke').' Unggulan.');
    }

    public function destroy(Portfolio $portfolio): RedirectResponse
    {
        $title = $portfolio->title;
        $portfolio->delete();

        return redirect()->route('admin.portfolios.index')->with('status', 'Portfolio "'.$title.'" dihapus.');
    }
}
