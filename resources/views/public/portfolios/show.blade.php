<?php
    $related = \App\Models\Portfolio::with('user')
        ->where('id', '!=', $portfolio->id)
        ->where('approval_status', 'approved')
        ->when($portfolio->skills->count(), fn ($q) => $q->whereHas('skills', fn ($s) => $s->whereIn('skills.id', $portfolio->skills->pluck('id'))))
        ->latest()
        ->limit(3)
        ->get();
    $topStudents = $related->isEmpty()
        ? \App\Models\User::where('role', 'siswa')->where('status', 'active')->withCount(['portfolios' => fn ($q) => $q->where('approval_status', 'approved')])->orderByDesc('portfolios_count')->limit(3)->get()
        : collect();
?>

@extends('layouts.base')
@section('title', $portfolio->title.' · '.$portfolio->user->name)

@section('body')
    @include('layouts.partials.public-nav')

    <main id="main" class="mx-auto max-w-7xl px-4 pb-12 pt-6 md:pt-10 lg:px-8">

        @if ($isPreview)
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-950">
                <div>
                    <p class="font-semibold">Mode preview</p>
                    <p class="mt-1 text-xs text-amber-800">Karya ini belum tampil untuk publik. Hanya pengguna yang login dan memiliki link ini yang dapat melihatnya.</p>
                </div>
                <x-badge variant="warning">{{ ucfirst($portfolio->approval_status) }}</x-badge>
            </div>
        @endif

        <nav class="mb-4 text-xs text-zinc-500">
            <a href="{{ route('home') }}" class="hover:text-blue-600">Beranda</a>
            <span class="mx-1">/</span>
            <a href="{{ route('portfolios.index') }}" class="hover:text-blue-600">Portofolio</a>
            <span class="mx-1">/</span>
            <span class="text-zinc-700">{{ $portfolio->title }}</span>
        </nav>

        <div class="grid gap-6 lg:grid-cols-4">
            <article class="lg:col-span-3">
                <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white">
                    <div class="aspect-video bg-zinc-100">
                        <img src="{{ $portfolio->coverUrl() }}" alt="{{ $portfolio->title }}" class="h-full w-full object-cover">
                    </div>
                </div>

                <div class="mt-6">
                    <div class="flex flex-wrap items-center gap-2">
                        <x-badge variant="info">{{ $portfolio->category ?? 'Tanpa Kategori' }}</x-badge>
                        <x-badge :variant="$portfolio->project_type === 'team' ? 'success' : 'neutral'">{{ $portfolio->project_type === 'team' ? 'Project Tim' : 'Project Personal' }}</x-badge>
                    </div>
                    <h1 class="font-display mt-3 text-2xl font-bold leading-tight tracking-tight text-zinc-900 md:text-4xl">
                        {{ $portfolio->title }}
                    </h1>
                    <div class="mt-2 flex flex-wrap items-center gap-3 text-sm text-zinc-600">
                        <a href="{{ route('portfolios.user', $portfolio->user) }}" class="flex items-center gap-2 font-medium text-zinc-800 hover:text-blue-600">
                            <img src="{{ $portfolio->user->avatarUrl() }}" alt="" class="h-7 w-7 rounded-full object-cover ring-1 ring-zinc-200">
                            {{ $portfolio->user->name }}
                        </a>
                        @if ($portfolio->user->school_class)
                            <span class="text-zinc-300">·</span>
                            <span>{{ $portfolio->user->school_class }}</span>
                        @endif
                        <span class="text-zinc-300">·</span>
                        <time>{{ $portfolio->created_at->isoFormat('D MMM Y') }}</time>
                        <span class="text-zinc-300">·</span>
                        <span class="inline-flex items-center gap-1 text-zinc-500" title="{{ $portfolio->views }} kali dilihat">
                            <x-icon name="eye" class="h-4 w-4" /> {{ $portfolio->views ?: 0 }}x dilihat
                        </span>
                        @if ($portfolio->ratings_count)
                            <span class="text-zinc-300">·</span>
                            <span class="inline-flex items-center gap-1 text-zinc-500"><x-icon name="star" class="h-4 w-4" /> {{ number_format($portfolio->ratings_avg_rating, 1) }}/5 dari {{ $portfolio->ratings_count }} guru</span>
                        @endif
                    </div>

                    <div class="rich-content mt-6 max-w-none text-base leading-relaxed text-zinc-700">
                        {!! \App\Support\RichText::clean($portfolio->description) !!}
                    </div>

                    <div class="mt-6 flex flex-wrap gap-2">
                            <button type="button" data-share-url="{{ $isPreview ? request()->fullUrl() : route('portfolios.show', $portfolio) }}" data-share-title="{{ $portfolio->title }}" data-share-text="Lihat {{ $isPreview ? 'preview ' : '' }}project {{ $portfolio->title }} karya {{ $portfolio->user->name }}"
                                    class="inline-flex items-center gap-2 rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-800 transition hover:bg-zinc-50">
                                <x-icon name="external" class="h-4 w-4" /> Bagikan
                            </button>
                        @if ($portfolio->project_url || $portfolio->github_url)
                            @if ($portfolio->project_url)
                                <a href="{{ $portfolio->project_url }}" target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-2 rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-zinc-800">
                                    <x-icon name="external" class="h-4 w-4" /> Lihat Demo
                                </a>
                            @endif
                            @if ($portfolio->github_url)
                                <a href="{{ $portfolio->github_url }}" target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-2 rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-800 transition hover:bg-zinc-50">
                                    <x-icon name="github" class="h-4 w-4" /> Repository
                                </a>
                            @endif
                        @endif
                    </div>

                    @if ($portfolio->skills->count())
                        <div class="mt-8 border-t border-zinc-100 pt-6">
                            <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Tech & Skill</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($portfolio->skills as $skill)
                                    <div class="flex items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3 py-1.5">
                                        <span class="text-sm font-medium text-zinc-800">{{ $skill->name }}</span>
                                        <span class="flex items-center gap-0.5">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <span class="block h-1.5 w-3 rounded-sm {{ $i <= $skill->pivot->level ? 'bg-blue-600' : 'bg-zinc-200' }}"></span>
                                            @endfor
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                @if ($related->count())
                    <section class="mt-12">
                        <div class="mb-5 flex items-end justify-between border-b border-zinc-200 pb-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500">Mirip dengan ini</p>
                                <h2 class="font-display text-xl font-bold tracking-tight text-zinc-900">Karya Terkait</h2>
                            </div>
                            <a href="{{ route('portfolios.index') }}" class="text-sm font-medium text-zinc-600 hover:text-blue-600">Lihat semua</a>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-3">
                            @foreach ($related as $p)
                                <x-portfolio-card :portfolio="$p" />
                            @endforeach
                        </div>
                    </section>
                @endif
            </article>

            <aside class="space-y-5 lg:sticky lg:top-24 lg:self-start">
                <div class="rounded-2xl border border-zinc-200 bg-white p-5">
                    <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500">Tentang Siswa</p>
                    <div class="mt-3 flex items-center gap-3">
                        <img src="{{ $portfolio->user->avatarUrl() }}" alt="" class="h-12 w-12 rounded-full object-cover ring-1 ring-zinc-200">
                        <div>
                            <p class="font-semibold text-zinc-900">{{ $portfolio->user->name }}</p>
                            <p class="text-xs text-zinc-500">{{ $portfolio->user->school_class }}</p>
                        </div>
                    </div>
                    @if ($portfolio->user->bio)
                        <div class="rich-content mt-3 text-sm leading-relaxed text-zinc-700">{!! \App\Support\RichText::clean($portfolio->user->bio) !!}</div>
                    @endif
                    @if ($portfolio->user->github_url || $portfolio->user->instagram_url)
                        <div class="mt-3 flex flex-wrap gap-2">
                            @if ($portfolio->user->github_url)
                                <a href="{{ $portfolio->user->github_url }}" target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-1.5 rounded-md border border-zinc-200 px-2.5 py-1 text-xs font-medium text-zinc-700 hover:bg-zinc-50">
                                    <x-icon name="github" class="h-3.5 w-3.5" /> GitHub
                                </a>
                            @endif
                            @if ($portfolio->user->instagram_url)
                                <a href="{{ $portfolio->user->instagram_url }}" target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-1.5 rounded-md border border-zinc-200 px-2.5 py-1 text-xs font-medium text-zinc-700 hover:bg-zinc-50">
                                    <x-icon name="instagram" class="h-3.5 w-3.5" /> Instagram
                                </a>
                            @endif
                        </div>
                    @endif
                    <a href="{{ route('portfolios.user', $portfolio->user) }}" class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-lg border border-zinc-300 px-3 py-2 text-sm font-medium text-zinc-800 transition hover:bg-zinc-50">
                        Lihat semua karya <x-icon name="arrow-right" class="h-4 w-4" />
                    </a>
                </div>

                @if ($portfolio->project_type === 'team' && $portfolio->acceptedContributors->count())
                    <div class="rounded-2xl border border-zinc-200 bg-white p-5">
                        <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500">Kontributor Tim</p>
                        <div class="mt-3 space-y-3">
                            @foreach ($portfolio->acceptedContributors as $contributor)
                                <a href="{{ route('portfolios.user', $contributor) }}" class="flex items-center gap-3 rounded-xl p-2 transition hover:bg-zinc-50">
                                    <img src="{{ $contributor->avatarUrl() }}" alt="" class="h-10 w-10 rounded-xl object-cover ring-1 ring-zinc-200">
                                    <span class="min-w-0">
                                        <span class="block truncate text-sm font-semibold text-zinc-900">{{ $contributor->name }}</span>
                                        <span class="block truncate text-xs text-zinc-500">{{ $contributor->school_class ?: 'Siswa' }}</span>
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="rounded-2xl border border-blue-100 bg-blue-50/60 p-5">
                    <p class="text-xs font-semibold uppercase tracking-widest text-blue-700">Untuk Perusahaan</p>
                    <p class="mt-2 text-sm text-zinc-700">Tertarik dengan karya {{ $portfolio->user->name }}? Kirim pesan ke sekolah untuk diskusi lebih lanjut.</p>
                    <a href="{{ route('contact.create') }}?subject=Penawaran untuk {{ urlencode($portfolio->user->name) }}"
                       class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-blue-700 active:scale-95">
                        <x-icon name="mail" class="h-4 w-4" /> Hubungi Sekolah
                    </a>
                </div>

                @if ($portfolio->skills->count())
                    <div class="rounded-2xl border border-zinc-200 bg-white p-5">
                        <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500">Rekomendasi</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($portfolio->skills as $skill)
                                <a href="{{ route('portfolios.index', ['skill' => $skill->id]) }}"
                                   class="rounded-md bg-zinc-100 px-2.5 py-1 text-xs font-medium text-zinc-700 transition hover:bg-zinc-900 hover:text-white">
                                    {{ $skill->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </aside>
        </div>
    </main>

    @include('layouts.partials.footer')
@endsection
