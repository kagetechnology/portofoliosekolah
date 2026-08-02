<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        return view('admin.portfolios.index', compact('portfolios', 'q', 'status'));
    }

    public function show(Portfolio $portfolio): View
    {
        $portfolio->load(['user', 'skills']);

        return view('admin.portfolios.show', compact('portfolio'));
    }

    public function approve(Portfolio $portfolio): RedirectResponse
    {
        $portfolio->update(['approval_status' => 'approved']);

        return back()->with('status', 'Portfolio "'.$portfolio->title.'" disetujui.');
    }

    public function reject(Portfolio $portfolio): RedirectResponse
    {
        $portfolio->update(['approval_status' => 'rejected', 'is_featured' => false]);

        return back()->with('status', 'Portfolio "'.$portfolio->title.'" ditolak.');
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
