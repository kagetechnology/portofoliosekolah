@extends('layouts.siswa')
@section('title', 'Dashboard Siswa')

@section('content')
    <header class="mb-8 flex flex-wrap items-start justify-between gap-4">
        <div class="flex items-center gap-4">
            <img src="{{ $user->avatarUrl() }}" alt="" class="h-14 w-14 rounded-2xl object-cover ring-1 ring-zinc-200">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">Selamat datang</p>
                <h1 class="font-display text-3xl font-bold tracking-tight text-zinc-900">Halo, {{ explode(' ', $user->name)[0] }}</h1>
                <p class="mt-1 text-sm text-zinc-500">Profil publik: <a class="text-blue-600 hover:underline" href="{{ route('portfolios.user', $user) }}">/siswa/{{ $user->slug }}</a></p>
            </div>
        </div>
        <a href="{{ route('siswa.profile.edit') }}" class="inline-flex items-center gap-2 rounded-lg border border-zinc-300 bg-white px-4 py-2 text-sm font-medium text-zinc-800 transition hover:bg-zinc-50">
            <x-icon name="pencil" class="h-4 w-4" /> Edit Profil
        </a>
    </header>

    <div class="mb-8 grid gap-4 sm:grid-cols-3">
        <x-stat-card name="briefcase" :value="$user->portfolios()->count()" label="Portofolio" />
        <x-stat-card name="award" :value="$user->certificates()->count()" label="Sertifikat" />
        <x-stat-card name="sparkles" :value="$user->portfolios()->where('is_featured', true)->count()" label="Unggulan" />
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <section class="lg:col-span-2 rounded-2xl border border-zinc-200 bg-white">
            <header class="flex items-center justify-between border-b border-zinc-100 px-5 py-4">
                <div>
                    <h2 class="font-semibold text-zinc-900">Portofolio Terbaru</h2>
                    <p class="text-xs text-zinc-500">Karya terakhir kamu</p>
                </div>
                <a href="{{ route('siswa.portfolios.create') }}" class="rounded-lg bg-zinc-900 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-zinc-800 active:scale-95">
                    <x-icon name="plus" class="inline h-3 w-3" /> Buat Baru
                </a>
            </header>
            <ul class="divide-y divide-zinc-100">
                @forelse ($portfolios as $p)
                    <li class="flex items-center gap-4 px-5 py-3 transition hover:bg-zinc-50">
                        <div class="h-12 w-16 shrink-0 overflow-hidden rounded-lg bg-zinc-100">
                            <img src="{{ $p->coverUrl() }}" alt="" class="h-full w-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('siswa.portfolios.edit', $p) }}" class="block truncate font-medium text-zinc-900 hover:text-blue-600">{{ $p->title }}</a>
                            <p class="text-xs text-zinc-500">{{ $p->category ?? 'Tanpa kategori' }} · {{ $p->created_at->diffForHumans() }}</p>
                        </div>
                        <x-badge :variant="$p->approval_status === 'approved' ? 'success' : ($p->approval_status === 'rejected' ? 'danger' : 'warning')">{{ ucfirst($p->approval_status) }}</x-badge>
                        <span class="hidden items-center gap-1 text-xs text-zinc-400 sm:inline-flex" title="{{ $p->views }} kali dilihat">
                            <x-icon name="eye" class="h-3 w-3" /> {{ $p->views ?: 0 }}
                        </span>
                        @if ($p->is_featured)
                            <x-badge variant="warning">Unggulan</x-badge>
                        @endif
                    </li>
                @empty
                    <li class="px-5 py-10">
                        <x-empty-state name="briefcase" title="Belum ada portofolio"
                            description="Tambahkan karyamu untuk mulai membangun personal branding.">
                            <x-slot:actions>
                                <x-button href="{{ route('siswa.portfolios.create') }}" variant="primary" size="sm">
                                    <x-icon name="plus" class="h-3 w-3" /> Buat Pertama
                                </x-button>
                            </x-slot:actions>
                        </x-empty-state>
                    </li>
                @endforelse
            </ul>
            @if ($portfolios->count())
                <div class="border-t border-zinc-100 px-5 py-3 text-right">
                    <a href="{{ route('siswa.portfolios.index') }}" class="text-xs font-medium text-blue-600 hover:underline">Lihat semua portofolio →</a>
                </div>
            @endif
        </section>

        <section class="rounded-2xl border border-zinc-200 bg-white">
            <header class="flex items-center justify-between border-b border-zinc-100 px-5 py-4">
                <div>
                    <h2 class="font-semibold text-zinc-900">Sertifikat</h2>
                    <p class="text-xs text-zinc-500">{{ $user->certificates()->count() }} tersimpan</p>
                </div>
                <a href="{{ route('siswa.certificates.index') }}" class="rounded-lg bg-zinc-900 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-zinc-800 active:scale-95">
                    Kelola
                </a>
            </header>
            <ul class="divide-y divide-zinc-100">
                @forelse ($certificates as $c)
                    <li>
                        <div class="flex items-center gap-3 px-5 py-3 transition hover:bg-zinc-50">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                                <x-icon name="award" class="h-4 w-4" />
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-zinc-900 hover:text-blue-600">{{ $c->title }}</p>
                                <p class="truncate text-xs text-zinc-500">
                                    @if ($c->certificate_number)<span class="font-mono">{{ $c->certificate_number }}</span> · @endif
                                    {{ $c->issuer ?: 'Tanpa penerbit' }}
                                    @if ($c->issue_date) · {{ $c->issue_date->isoFormat('MMM Y') }} @endif
                                </p>
                            </div>
                            <x-badge :variant="$c->approval_status === 'approved' ? 'success' : ($c->approval_status === 'rejected' ? 'danger' : 'warning')">{{ ucfirst($c->approval_status) }}</x-badge>
                            <x-icon name="arrow-right" class="h-4 w-4 text-zinc-300" />
                        </div>
                    </li>
                @empty
                    <li class="px-5 py-8 text-center">
                        <p class="text-sm text-zinc-400">Belum ada sertifikat.</p>
                        <a href="{{ route('siswa.certificates.index') }}" class="mt-2 inline-block text-xs font-medium text-blue-600 hover:underline">Tambah sekarang →</a>
                    </li>
                @endforelse
            </ul>
            @if ($certificates->count())
                <div class="border-t border-zinc-100 px-5 py-3 text-right">
                    <a href="{{ route('siswa.certificates.index') }}" class="text-xs font-medium text-blue-600 hover:underline">Lihat semua →</a>
                </div>
            @endif
        </section>
    </div>
@endsection
