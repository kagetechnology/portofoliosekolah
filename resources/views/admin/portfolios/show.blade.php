@extends('layouts.admin')
@section('title', 'Review Portfolio')

@section('content')
    <header class="mb-6 flex flex-wrap items-start justify-between gap-3">
        <div>
            <a href="{{ route('admin.portfolios.index') }}" class="inline-flex items-center gap-2 text-sm text-zinc-600 hover:text-blue-600">
                <x-icon name="arrow-left" class="h-4 w-4" /> Kembali
            </a>
            <div class="mt-3 flex flex-wrap items-center gap-2">
                <h1 class="font-display text-3xl font-bold tracking-tight text-zinc-900">{{ $portfolio->title }}</h1>
                <x-badge :variant="$portfolio->approval_status === 'approved' ? 'success' : ($portfolio->approval_status === 'rejected' ? 'danger' : 'warning')">{{ ucfirst($portfolio->approval_status) }}</x-badge>
            </div>
            <p class="mt-1 text-sm text-zinc-500">Review karya sebelum tampil publik.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if ($portfolio->approval_status !== 'approved')
                <form method="POST" action="{{ route('admin.portfolios.approve', $portfolio) }}">
                    @csrf @method('PATCH')
                    <x-button type="submit" variant="primary"><x-icon name="check" class="h-4 w-4" /> Setujui</x-button>
                </form>
            @endif
            @if ($portfolio->approval_status !== 'rejected')
                <form method="POST" action="{{ route('admin.portfolios.reject', $portfolio) }}">
                    @csrf @method('PATCH')
                    <button class="inline-flex items-center gap-2 rounded-lg border border-red-200 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50">Tolak</button>
                </form>
            @endif
        </div>
    </header>

    <div class="grid gap-6 lg:grid-cols-3">
        <article class="lg:col-span-2 space-y-5">
            <section class="overflow-hidden rounded-2xl border border-zinc-200 bg-white">
                <div class="aspect-video bg-zinc-100">
                    <img src="{{ $portfolio->coverUrl() }}" alt="" class="h-full w-full object-cover">
                </div>
            </section>

            <section class="rounded-2xl border border-zinc-200 bg-white p-6">
                <h2 class="mb-3 text-sm font-semibold uppercase tracking-wider text-zinc-500">Deskripsi</h2>
                <div class="rich-content text-sm leading-relaxed text-zinc-700">{!! \App\Support\RichText::clean($portfolio->description) !!}</div>
            </section>

            @if ($portfolio->skills->count())
                <section class="rounded-2xl border border-zinc-200 bg-white p-6">
                    <h2 class="mb-3 text-sm font-semibold uppercase tracking-wider text-zinc-500">Skill</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($portfolio->skills as $skill)
                            <span class="rounded-lg bg-zinc-100 px-3 py-1.5 text-sm text-zinc-700">{{ $skill->name }} · Lv {{ $skill->pivot->level }}</span>
                        @endforeach
                    </div>
                </section>
            @endif
        </article>

        <aside class="space-y-5 lg:sticky lg:top-24 lg:self-start">
            <section class="rounded-2xl border border-zinc-200 bg-white p-5">
                <h2 class="mb-3 text-sm font-semibold uppercase tracking-wider text-zinc-500">Siswa</h2>
                <div class="flex items-center gap-3">
                    <img src="{{ $portfolio->user->avatarUrl() }}" alt="" class="h-12 w-12 rounded-xl object-cover ring-1 ring-zinc-200">
                    <div>
                        <p class="font-semibold text-zinc-900">{{ $portfolio->user->name }}</p>
                        <p class="text-xs text-zinc-500">{{ $portfolio->user->school_class ?: 'Tanpa kelas' }}</p>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-zinc-200 bg-white p-5">
                <h2 class="mb-3 text-sm font-semibold uppercase tracking-wider text-zinc-500">Metadata</h2>
                <dl class="space-y-3 text-sm">
                    <div><dt class="text-xs text-zinc-500">Kategori</dt><dd class="font-medium text-zinc-900">{{ $portfolio->category ?: '—' }}</dd></div>
                    <div><dt class="text-xs text-zinc-500">Dikirim</dt><dd class="font-medium text-zinc-900">{{ $portfolio->created_at->isoFormat('D MMMM Y HH:mm') }}</dd></div>
                    <div><dt class="text-xs text-zinc-500">Diubah</dt><dd class="font-medium text-zinc-900">{{ $portfolio->updated_at->isoFormat('D MMMM Y HH:mm') }}</dd></div>
                </dl>
            </section>

            <section class="rounded-2xl border border-zinc-200 bg-white p-5">
                <h2 class="mb-3 text-sm font-semibold uppercase tracking-wider text-zinc-500">Link Validasi</h2>
                <div class="space-y-2">
                    @if ($portfolio->project_url)
                        <a href="{{ $portfolio->project_url }}" target="_blank" rel="noopener" class="flex items-center justify-between rounded-lg border border-zinc-200 px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-50">Project URL <x-icon name="external" class="h-4 w-4" /></a>
                    @endif
                    @if ($portfolio->github_url)
                        <a href="{{ $portfolio->github_url }}" target="_blank" rel="noopener" class="flex items-center justify-between rounded-lg border border-zinc-200 px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-50">GitHub <x-icon name="external" class="h-4 w-4" /></a>
                    @endif
                    @unless ($portfolio->project_url || $portfolio->github_url)
                        <p class="text-sm text-zinc-400">Tidak ada link tambahan.</p>
                    @endunless
                </div>
            </section>
        </aside>
    </div>
@endsection
