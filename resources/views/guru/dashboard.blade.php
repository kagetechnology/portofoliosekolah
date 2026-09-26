@extends('layouts.admin')
@section('title', 'Dashboard Guru')

@section('content')
    <header class="mb-6 rounded-[2rem] bg-zinc-950 p-6 text-white md:p-8">
        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-blue-300">Ruang validasi guru</p>
        <h1 class="font-display mt-3 text-4xl font-bold tracking-tight md:text-5xl">Review karya siswa.</h1>
        <p class="mt-3 text-sm text-zinc-300">Setujui atau tolak proyek dan sertifikat. Berikan rating 1-5 pada proyek.</p>
    </header>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <a href="{{ route('guru.portfolios.index', ['status' => 'pending']) }}" class="rounded-3xl border border-zinc-200 bg-white p-5">
            <p class="text-sm text-zinc-500">Proyek pending</p><p class="mt-3 text-4xl font-bold">{{ $stats['pending_portfolios'] }}</p>
        </a>
        <a href="{{ route('guru.certificates.index', ['status' => 'pending']) }}" class="rounded-3xl border border-zinc-200 bg-white p-5">
            <p class="text-sm text-zinc-500">Sertifikat pending</p><p class="mt-3 text-4xl font-bold">{{ $stats['pending_certificates'] }}</p>
        </a>
        <div class="rounded-3xl border border-zinc-200 bg-white p-5"><p class="text-sm text-zinc-500">Total view proyek</p><p class="mt-3 text-4xl font-bold">{{ $stats['total_views'] }}</p></div>
        <div class="rounded-3xl border border-zinc-200 bg-white p-5"><p class="text-sm text-zinc-500">Proyek Anda rating</p><p class="mt-3 text-4xl font-bold">{{ $stats['rated'] }}</p></div>
    </section>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <section class="overflow-hidden rounded-[1.75rem] border border-zinc-200 bg-white">
            <header class="flex items-center justify-between border-b border-zinc-100 p-5"><h2 class="font-display text-xl font-bold">Proyek terbaru</h2><a href="{{ route('guru.portfolios.index') }}" class="text-sm text-blue-600">Semua</a></header>
            <div class="divide-y divide-zinc-100">
                @forelse ($portfolios as $portfolio)
                    <a href="{{ route('guru.portfolios.show', $portfolio) }}" class="flex items-center gap-3 p-4 hover:bg-zinc-50">
                        <img src="{{ $portfolio->coverUrl() }}" alt="" class="h-12 w-16 rounded-xl object-cover">
                        <span class="min-w-0 flex-1"><span class="block truncate font-semibold">{{ $portfolio->title }}</span><span class="text-xs text-zinc-500">{{ $portfolio->user->name }} · {{ $portfolio->views }} view · rating {{ $portfolio->ratings_avg_rating ? number_format($portfolio->ratings_avg_rating, 1) : '—' }}</span></span>
                        <x-badge :variant="$portfolio->approval_status === 'approved' ? 'success' : ($portfolio->approval_status === 'rejected' ? 'danger' : 'warning')">{{ $portfolio->approval_status }}</x-badge>
                    </a>
                @empty <p class="p-8 text-center text-sm text-zinc-400">Belum ada proyek.</p> @endforelse
            </div>
        </section>
        <section class="overflow-hidden rounded-[1.75rem] border border-zinc-200 bg-white">
            <header class="flex items-center justify-between border-b border-zinc-100 p-5"><h2 class="font-display text-xl font-bold">Sertifikat terbaru</h2><a href="{{ route('guru.certificates.index') }}" class="text-sm text-blue-600">Semua</a></header>
            <div class="divide-y divide-zinc-100">
                @forelse ($certificates as $certificate)
                    <a href="{{ route('guru.certificates.show', $certificate) }}" class="flex items-center gap-3 p-4 hover:bg-zinc-50">
                        <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-700"><x-icon name="award" /></span>
                        <span class="min-w-0 flex-1"><span class="block truncate font-semibold">{{ $certificate->title }}</span><span class="text-xs text-zinc-500">{{ $certificate->user->name }} · {{ $certificate->issuer }}</span></span>
                        <x-badge :variant="$certificate->approval_status === 'approved' ? 'success' : ($certificate->approval_status === 'rejected' ? 'danger' : 'warning')">{{ $certificate->approval_status }}</x-badge>
                    </a>
                @empty <p class="p-8 text-center text-sm text-zinc-400">Belum ada sertifikat.</p> @endforelse
            </div>
        </section>
    </div>
@endsection
