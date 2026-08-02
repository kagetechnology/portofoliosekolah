<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoleChangeLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $q = $request->string('q')->toString();
        $role = $request->string('role')->toString();
        $status = $request->string('status')->toString();

        $users = User::query()
            ->when($q !== '', fn ($qb) => $qb->where(function ($w) use ($q) {
                $w->where('name', 'like', "%$q%")->orWhere('email', 'like', "%$q%");
            }))
            ->when($role !== '', fn ($qb) => $qb->where('role', $role))
            ->when($status !== '', fn ($qb) => $qb->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'q', 'role', 'status'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'school_class' => ['nullable', 'string', 'max:50'],
            'tahun_masuk' => ['nullable', 'integer', 'min:2000', 'max:2099'],
            'role' => ['required', 'in:admin,siswa'],
            'status' => ['required', 'in:pending,active,rejected'],
        ]);

        if ($user->id === $request->user()->id && $data['role'] !== 'admin') {
            return back()->withErrors(['role' => 'Tidak dapat menurunkan role akun sendiri.']);
        }

        $oldRole = $user->role;
        $oldName = $user->name;
        $user->update($data);

        // Regenerate slug if name changed (so /siswa/{slug} always reflects current name)
        if ($oldName !== $user->name) {
            $user->slug = null; // force uniqueSlug() to regenerate
            $user->save();
        }

        if ($oldRole !== $user->role) {
            RoleChangeLog::create([
                'user_id' => $user->id,
                'old_role' => $oldRole,
                'new_role' => $user->role,
                'changed_by' => $request->user()->id,
            ]);
        }

        return back()->with('status', 'User diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['user' => 'Tidak dapat menghapus akun sendiri.']);
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('status', 'User dihapus.');
    }

    public function toggleFeatured(Request $request, User $user): RedirectResponse
    {
        $portfolioId = $request->integer('portfolio_id');
        $portfolio = $user->portfolios()->where('id', $portfolioId)->first();
        if (! $portfolio) {
            return back()->withErrors(['user' => 'Portfolio tidak ditemukan.']);
        }
        $portfolio->update(['is_featured' => ! $portfolio->is_featured]);
        return back()->with('status', 'Portfolio '.$portfolio->title.' status unggulan diperbarui.');
    }
}
