@extends('layouts.admin')
@section('title', 'Persetujuan Sertifikat')

@section('content')
    <header class="mb-6 rounded-[2rem] border border-zinc-200 bg-white p-6 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-blue-600">Review bukti</p>
        <h1 class="font-display mt-3 text-4xl font-bold tracking-tight text-zinc-950 md:text-5xl">Sertifikat</h1>
        <p class="mt-2 max-w-2xl text-sm text-zinc-500">Setujui atau tolak sertifikat sebelum tampil publik.</p>
    </header>

    <form method="GET" class="mb-5 grid gap-3 rounded-[1.75rem] border border-zinc-200 bg-white p-4 shadow-sm md:grid-cols-[minmax(0,1fr)_180px]">
        <div class="relative">
            <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
            <input name="q" value="{{ $q }}" placeholder="Cari judul / siswa / penerbit..."
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
            @forelse ($certificates as $c)
                <li class="flex flex-wrap items-center gap-4 p-4 transition hover:bg-zinc-50">
                    <div class="flex h-16 w-24 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-amber-50 text-amber-700">
                        @if ($c->file && $c->isImage())
                            <img src="{{ $c->fileUrl() }}" alt="" class="h-full w-full object-cover">
                        @else
                            <x-icon name="award" class="h-7 w-7" />
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="truncate font-semibold text-zinc-900">{{ $c->title }}</p>
                            <x-badge :variant="$c->approval_status === 'approved' ? 'success' : ($c->approval_status === 'rejected' ? 'danger' : 'warning')">{{ ucfirst($c->approval_status) }}</x-badge>
                        </div>
                        <p class="text-xs text-zinc-500">
                            <a href="{{ route('portfolios.user', $c->user) }}" class="hover:text-blue-600">{{ $c->user->name }}</a>
                            @if ($c->issuer) · {{ $c->issuer }} @endif
                            @if ($c->certificate_number) · {{ $c->certificate_number }} @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route($routePrefix.'.certificates.show', $c) }}" class="inline-flex items-center gap-1.5 rounded-full border border-zinc-300 px-3 py-1.5 text-xs font-medium text-zinc-700 transition hover:bg-zinc-50">
                            Review
                        </a>
                        @if ($c->file)
                            <a href="{{ $c->fileUrl() }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-300 px-3 py-1.5 text-xs font-medium text-zinc-700 transition hover:bg-zinc-50">
                                <x-icon name="external" class="h-3 w-3" /> File
                            </a>
                        @endif
                        @if ($c->approval_status !== 'approved')
                            <form method="POST" action="{{ route($routePrefix.'.certificates.approve', $c) }}">
                                @csrf @method('PATCH')
                                <button class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-200 px-3 py-1.5 text-xs font-medium text-emerald-700 transition hover:bg-emerald-50">
                                    <x-icon name="check" class="h-3 w-3" /> Setujui
                                </button>
                            </form>
                        @endif
                        @if ($c->approval_status !== 'rejected')
                            <form method="POST" action="{{ route($routePrefix.'.certificates.reject', $c) }}">
                                @csrf @method('PATCH')
                                <button class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 transition hover:bg-red-50">
                                    Tolak
                                </button>
                            </form>
                        @endif
                    </div>
                </li>
            @empty
                <li class="p-10 text-center text-sm text-zinc-400">Tidak ada sertifikat.</li>
            @endforelse
        </ul>
    </div>

    <div class="mt-5">{{ $certificates->links() }}</div>
@endsection
