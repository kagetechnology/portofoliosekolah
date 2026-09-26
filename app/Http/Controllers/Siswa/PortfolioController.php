<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Portfolio;
use App\Models\User;
use App\Support\ImageOptimizer;
use App\Support\RichText;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $portfolios = $user->visiblePortfolios()
            ->with('skills')
            ->latest()
            ->paginate(10);

        return view('siswa.portfolios.index', compact('portfolios'));
    }

    public function create(Request $request): View
    {
        $selectedIds = collect((array) $request->old('contributor_ids', []))->map(fn ($id) => (int) $id);

        return view('siswa.portfolios.create', [
            'categories' => Category::orderBy('name')->get(),
            'selectedStudents' => User::whereIn('id', $selectedIds)->get(['id', 'name', 'school_class']),
        ]);
    }

    public function searchContributors(Request $request): JsonResponse
    {
        $query = trim($request->string('q')->toString());
        if (mb_strlen($query) < 2) {
            return response()->json([]);
        }

        $students = User::query()
            ->where('role', 'siswa')
            ->where('status', 'active')
            ->whereKeyNot($request->user()->id)
            ->where(fn ($builder) => $builder
                ->where('name', 'like', "%{$query}%")
                ->orWhere('school_class', 'like', "%{$query}%"))
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name', 'school_class']);

        return response()->json($students);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $portfolio = new Portfolio($data);
        $portfolio->user_id = $request->user()->id;
        $portfolio->approval_status = 'pending';
        $portfolio->save();
        $this->syncContributors($portfolio, $request);

        return redirect()->route('siswa.portfolios.index')->with([
            'status' => 'Portofolio dibuat dan sedang menunggu persetujuan.',
            'share_url' => URL::signedRoute('portfolios.preview', $portfolio),
            'share_title' => $portfolio->title,
            'share_text' => "Lihat preview project {$portfolio->title}",
        ]);
    }

    public function edit(Portfolio $portfolio): View
    {
        $this->authorize($portfolio);
        $selectedIds = collect((array) old('contributor_ids', $portfolio->contributors()
            ->wherePivotIn('status', ['pending', 'accepted'])
            ->pluck('users.id')->all()))->map(fn ($id) => (int) $id);

        return view('siswa.portfolios.edit', [
            'portfolio' => $portfolio,
            'categories' => Category::orderBy('name')->get(),
            'selectedStudents' => User::whereIn('id', $selectedIds)->get(['id', 'name', 'school_class']),
        ]);
    }

    public function update(Request $request, Portfolio $portfolio): RedirectResponse
    {
        $this->authorize($portfolio);
        $data = $this->validated($request, $portfolio);
        $data['approval_status'] = 'pending';
        $data['rejection_note'] = null;
        $data['is_featured'] = false;
        $portfolio->update($data);
        $this->syncContributors($portfolio, $request);

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
            'contributor_ids' => ['nullable', 'array', 'max:20'],
            'contributor_ids.*' => [
                'integer',
                'distinct',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'siswa')->where('status', 'active')),
            ],
            'project_url' => ['nullable', 'url', 'max:255'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'cover_image' => [$portfolio ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);

        $data['description'] = RichText::clean($data['description']) ?? '';

        if ($request->hasFile('cover_image')) {
            if ($portfolio?->cover_image) {
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

        return $data;
    }

    private function syncContributors(Portfolio $portfolio, Request $request): void
    {
        $ids = $portfolio->project_type === 'team'
            ? collect((array) $request->input('contributor_ids', []))
                ->map(fn ($id) => (int) $id)
                ->reject(fn ($id) => $id === $portfolio->user_id)
                ->unique()
                ->take(20)
                ->all()
            : [];

        $existing = $portfolio->contributors()
            ->wherePivotIn('status', ['pending', 'accepted'])
            ->get()
            ->keyBy('id');

        $sync = collect($ids)->mapWithKeys(function (int $id) use ($existing): array {
            $contributor = $existing->get($id);

            return [$id => [
                'status' => $contributor?->pivot->status ?? 'pending',
                'responded_at' => $contributor?->pivot->responded_at,
            ]];
        })->all();

        $portfolio->contributors()->sync($sync);
    }

    private function authorize(Portfolio $portfolio): void
    {
        if ($portfolio->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
