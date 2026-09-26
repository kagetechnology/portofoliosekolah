@extends('layouts.admin')
@section('title', 'Manajemen Portofolio')

@section('content')
    <header class="mb-6 rounded-[2rem] border border-zinc-200 bg-white p-6 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-blue-600">Review karya</p>
        <h1 class="font-display mt-3 text-4xl font-bold tracking-tight text-zinc-950 md:text-5xl">Portofolio</h1>
        <p class="mt-2 max-w-2xl text-sm text-zinc-500">Setujui atau tolak karya sebelum tampil publik.</p>
    </header>

    <form method="GET" class="mb-5 grid gap-3 rounded-[1.75rem] border border-zinc-200 bg-white p-4 shadow-sm md:grid-cols-[minmax(0,1fr)_180px]">
        <div class="relative">
            <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
            <input name="q" value="{{ $q }}" placeholder="Cari judul / siswa..."
                   class="w-full rounded-lg border border-zinc-200 bg-zinc-50 py-2.5 pl-9 pr-3 text-sm focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
        </div>
        <select name="status" class="rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm" onchange="this.form.submit()">
            <option value="">Semua status</option>
            <option value="pending" @selected($status === 'pending')>Pending</option>
            <option value="approved" @selected($status === 'approved')>Approved</option>
            <option value="rejected" @selected($status === 'rejected')>Rejected</option>
        </select>
    </form>

    <div class="overflow-hidden rounded-[1.75rem] border border-zinc-200 bg-white shadow-sm">
        <ul class="divide-y divide-zinc-100">
            @forelse ($portfolios as $p)
                <li class="flex flex-wrap items-center gap-4 p-4 transition hover:bg-zinc-50">
                    <div class="h-16 w-24 shrink-0 overflow-hidden rounded-2xl bg-zinc-100">
                        <img src="{{ $p->coverUrl() }}" alt="" loading="lazy" decoding="async" class="h-full w-full object-cover">
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="truncate font-semibold text-zinc-900">{{ $p->title }}</p>
                            <x-badge :variant="$p->approval_status === 'approved' ? 'success' : ($p->approval_status === 'rejected' ? 'danger' : 'warning')">{{ ucfirst($p->approval_status) }}</x-badge>
                        </div>
                        <p class="text-xs text-zinc-500">
                            <a href="{{ route('portfolios.user', $p->user) }}" class="hover:text-blue-600">{{ $p->user->name }}</a>
                            @if ($p->category) · {{ $p->category }} @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 text-xs text-zinc-500" title="{{ $p->views }} kali dilihat">
                            <x-icon name="eye" class="h-3 w-3" /> {{ $p->views ?: 0 }}
                        </span>
                        <a href="{{ route($routePrefix.'.portfolios.show', $p) }}" class="inline-flex items-center gap-1.5 rounded-full border border-zinc-300 px-3 py-1.5 text-xs font-medium text-zinc-700 transition hover:bg-zinc-50">
                            Review
                        </a>
                        <a href="{{ route($routePrefix.'.portfolios.edit', $p) }}" class="inline-flex items-center gap-1.5 rounded-full border border-zinc-300 px-3 py-1.5 text-xs font-medium text-zinc-700 transition hover:bg-zinc-50">
                            <x-icon name="pencil" class="h-3 w-3" /> Edit
                        </a>
                        @if ($p->approval_status !== 'approved')
                            <form method="POST" action="{{ route($routePrefix.'.portfolios.approve', $p) }}">
                                @csrf @method('PATCH')
                                <button class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-200 px-3 py-1.5 text-xs font-medium text-emerald-700 transition hover:bg-emerald-50">
                                    <x-icon name="check" class="h-3 w-3" /> Setujui
                                </button>
                            </form>
                        @endif
                        @if ($routePrefix === 'admin' && $p->isApproved())
                            <form method="POST" action="{{ route('admin.portfolios.featured', $p) }}">
                                @csrf @method('PATCH')
                                <button class="inline-flex items-center gap-1.5 rounded-lg border {{ $p->is_featured ? 'border-amber-300 bg-amber-50 text-amber-800' : 'border-zinc-300 text-zinc-700' }} px-3 py-1.5 text-xs font-medium transition hover:bg-zinc-50">
                                    <x-icon name="star" class="h-3 w-3" />
                                    {{ $p->is_featured ? 'Unggulan' : 'Jadikan Unggulan' }}
                                </button>
                            </form>
                            <a href="{{ route('portfolios.show', $p) }}" target="_blank" class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-300 px-3 py-1.5 text-xs font-medium text-zinc-700 transition hover:bg-zinc-50">
                                <x-icon name="external" class="h-3 w-3" /> Lihat
                            </a>
                        @endif
                        @if ($routePrefix === 'admin')
                        <form method="POST" action="{{ route('admin.portfolios.destroy', $p) }}" onsubmit="return confirm('Hapus portofolio ini permanen?')">
                            @csrf @method('DELETE')
                            <button class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 transition hover:bg-red-50">
                                <x-icon name="trash" class="h-3 w-3" />
                            </button>
                        </form>
                        @endif
                    </div>
                </li>
            @empty
                <li class="p-10 text-center text-sm text-zinc-400">Tidak ada portofolio.</li>
            @endforelse
        </ul>
    </div>

    <div class="mt-5">{{ $portfolios->links() }}</div>
@endsection
