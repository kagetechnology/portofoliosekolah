@extends('layouts.admin')
@section('title', 'Dashboard Admin')

@section('content')
    <header class="mb-6">
        <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">Selamat datang, {{ auth()->user()->name }}</p>
        <h1 class="font-display text-3xl font-bold tracking-tight text-zinc-900">Dashboard</h1>
    </header>

    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card name="users" :value="$stats['pending_users']" label="Akun Pending" />
        <x-stat-card name="dashboard" :value="$stats['active_siswa']" label="Siswa Aktif" />
        <x-stat-card name="briefcase" :value="$stats['pending_portfolios']" label="Portfolio Pending" />
        <x-stat-card name="award" :value="$stats['pending_certificates']" label="Sertifikat Pending" />
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <section class="lg:col-span-2 rounded-2xl border border-zinc-200 bg-white">
            <header class="flex items-center justify-between border-b border-zinc-100 px-5 py-4">
                <div>
                    <h2 class="font-semibold text-zinc-900">Akun Pending Terbaru</h2>
                    <p class="text-xs text-zinc-500">Siswa menunggu aktivasi</p>
                </div>
                <a href="{{ route('admin.users.index') }}?status=pending" class="text-xs font-medium text-blue-600 hover:underline">Lihat semua</a>
            </header>
            <ul class="divide-y divide-zinc-100">
                @forelse ($pendingUsers as $u)
                    <li class="flex items-center justify-between px-5 py-3 text-sm">
                        <div>
                            <p class="font-medium text-zinc-900">{{ $u->name }}</p>
                            <p class="text-xs text-zinc-500">{{ $u->email }} · {{ $u->school_class }}</p>
                        </div>
                        <a href="{{ route('admin.users.index') }}?status=pending" class="text-xs font-medium text-blue-600 hover:underline">Tinjau</a>
                    </li>
                @empty
                    <li class="px-5 py-8 text-center text-sm text-zinc-400">Tidak ada akun pending.</li>
                @endforelse
            </ul>
        </section>

        <section class="rounded-2xl border border-zinc-200 bg-white">
            <header class="border-b border-zinc-100 px-5 py-4">
                <h2 class="font-semibold text-zinc-900">Karya Paling Dilihat</h2>
                <p class="text-xs text-zinc-500">Top engagement</p>
            </header>
            <ul class="divide-y divide-zinc-100">
                @forelse ($topPortfolios as $p)
                    <li>
                        <a href="{{ route('portfolios.show', $p) }}" target="_blank" class="flex items-center gap-3 px-5 py-3 transition hover:bg-zinc-50">
                            <div class="h-10 w-14 shrink-0 overflow-hidden rounded-lg bg-zinc-100">
                                <img src="{{ $p->coverUrl() }}" alt="" class="h-full w-full object-cover">
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-zinc-900">{{ $p->title }}</p>
                                <p class="truncate text-xs text-zinc-500">{{ $p->user->name }}</p>
                            </div>
                            <span class="inline-flex items-center gap-1 rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-700">
                                <x-icon name="eye" class="h-3 w-3" /> {{ $p->views }}
                            </span>
                        </a>
                    </li>
                @empty
                    <li class="px-5 py-8 text-center text-sm text-zinc-400">Belum ada views.</li>
                @endforelse
            </ul>
            <div class="border-t border-zinc-100 px-5 py-3 text-right">
                <a href="{{ route('admin.portfolios.index') }}" class="text-xs font-medium text-blue-600 hover:underline">Kelola portofolio →</a>
            </div>
        </section>
    </div>

    <section class="mt-5 rounded-2xl border border-zinc-200 bg-white">
        <header class="flex items-center justify-between border-b border-zinc-100 px-5 py-4">
            <div>
                <h2 class="font-semibold text-zinc-900">Pesan Terbaru</h2>
                <p class="text-xs text-zinc-500">Pesan dari perusahaan</p>
            </div>
            <a href="{{ route('admin.contacts.index') }}" class="text-xs font-medium text-blue-600 hover:underline">Lihat semua</a>
        </header>
        <ul class="divide-y divide-zinc-100">
            @forelse ($latestMessages as $m)
                <li class="px-5 py-3 text-sm hover:bg-zinc-50">
                    <a href="{{ route('admin.contacts.show', $m) }}" class="block">
                        <div class="flex items-center justify-between">
                            <p class="font-medium text-zinc-900">{{ $m->subject }}</p>
                            @if (! $m->is_read)
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-medium text-red-700">BARU</span>
                            @endif
                        </div>
                        <p class="mt-0.5 text-xs text-zinc-500">{{ $m->sender_name }}@if($m->sender_company) · {{ $m->sender_company }}@endif</p>
                    </a>
                </li>
            @empty
                <li class="px-5 py-8 text-center text-sm text-zinc-400">Belum ada pesan masuk.</li>
            @endforelse
        </ul>
    </section>
@endsection
