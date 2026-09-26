@extends('layouts.admin')
@section('title', 'Review Portfolio')

@section('content')
    <header class="mb-6 flex flex-wrap items-start justify-between gap-3 rounded-[2rem] border border-zinc-200 bg-white p-6 shadow-sm">
        <div>
            <a href="{{ route($routePrefix.'.portfolios.index') }}" class="inline-flex items-center gap-2 text-sm text-zinc-600 hover:text-blue-600">
                <x-icon name="arrow-left" class="h-4 w-4" /> Kembali
            </a>
            <div class="mt-3 flex flex-wrap items-center gap-2">
                <h1 class="font-display text-4xl font-bold tracking-tight text-zinc-950 md:text-5xl">{{ $portfolio->title }}</h1>
                <x-badge :variant="$portfolio->approval_status === 'approved' ? 'success' : ($portfolio->approval_status === 'rejected' ? 'danger' : 'warning')">{{ ucfirst($portfolio->approval_status) }}</x-badge>
            </div>
            <p class="mt-1 text-sm text-zinc-500">Review karya sebelum tampil publik.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route($routePrefix.'.portfolios.edit', $portfolio) }}" class="inline-flex items-center gap-2 rounded-lg border border-zinc-300 bg-white px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50">
                <x-icon name="pencil" class="h-4 w-4" /> Edit & Skill
            </a>
            @if ($portfolio->approval_status !== 'approved')
                <form method="POST" action="{{ route($routePrefix.'.portfolios.approve', $portfolio) }}">
                    @csrf @method('PATCH')
                    <x-button type="submit" variant="primary"><x-icon name="check" class="h-4 w-4" /> Setujui</x-button>
                </form>
            @endif
        </div>
    </header>

    @if ($portfolio->approval_status === 'rejected' && $portfolio->rejection_note)
        <x-alert variant="error" class="mb-6">
            <p class="font-semibold">Catatan penolakan</p>
            <p class="mt-1 whitespace-pre-line">{{ $portfolio->rejection_note }}</p>
        </x-alert>
    @endif

    @if ($portfolio->approval_status !== 'rejected')
        <section class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-5">
            <h2 class="text-sm font-bold text-red-900">Tolak dan minta perbaikan</h2>
            <p class="mt-1 text-xs leading-5 text-red-700">Tuliskan alasan spesifik agar siswa mengetahui bagian yang harus diperbaiki.</p>
            <form method="POST" action="{{ route($routePrefix.'.portfolios.reject', $portfolio) }}" class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-end">
                @csrf @method('PATCH')
                <div class="min-w-0 flex-1">
                    <label for="rejection_note" class="sr-only">Catatan penolakan</label>
                    <textarea id="rejection_note" name="rejection_note" required minlength="10" maxlength="1000" rows="3" placeholder="Contoh: Cover belum jelas dan link demo tidak dapat dibuka. Mohon perbaiki lalu kirim ulang."
                              class="w-full rounded-lg border border-red-200 bg-white px-3 py-2.5 text-sm placeholder:text-zinc-500 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/15">{{ old('rejection_note') }}</textarea>
                    @error('rejection_note') <p class="mt-1 text-xs font-medium text-red-700">{{ $message }}</p> @enderror
                </div>
                <button class="shrink-0 rounded-lg bg-red-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-800">Tolak Portofolio</button>
            </form>
        </section>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        <article class="lg:col-span-2 space-y-5">
            <section class="overflow-hidden rounded-[1.75rem] border border-zinc-200 bg-white shadow-sm">
                <div class="aspect-video bg-zinc-100">
                    <img src="{{ $portfolio->coverUrl() }}" alt="" class="h-full w-full object-cover">
                </div>
            </section>

            <section class="rounded-[1.75rem] border border-zinc-200 bg-white p-6 shadow-sm">
                <h2 class="mb-3 text-sm font-semibold uppercase tracking-wider text-zinc-500">Deskripsi</h2>
                <div class="rich-content text-sm leading-relaxed text-zinc-700">{!! \App\Support\RichText::clean($portfolio->description) !!}</div>
            </section>

            @if ($portfolio->skills->count())
                <section class="rounded-[1.75rem] border border-zinc-200 bg-white p-6 shadow-sm">
                    <h2 class="mb-3 text-sm font-semibold uppercase tracking-wider text-zinc-500">Skill</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($portfolio->skills as $skill)
                            <span class="rounded-lg bg-zinc-100 px-3 py-1.5 text-sm text-zinc-700">{{ $skill->name }} · Lv {{ $skill->pivot->level }}</span>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($portfolio->project_type === 'team' && $portfolio->contributors->count())
                <section class="rounded-[1.75rem] border border-zinc-200 bg-white p-6 shadow-sm">
                    <h2 class="mb-3 text-sm font-semibold uppercase tracking-wider text-zinc-500">Kontributor Tim</h2>
                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach ($portfolio->contributors as $contributor)
                            <div class="flex items-center gap-3 rounded-2xl bg-zinc-50 p-3">
                                <img src="{{ $contributor->avatarUrl() }}" alt="" class="h-10 w-10 rounded-xl object-cover">
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-zinc-900">{{ $contributor->name }}</p>
                                    <p class="truncate text-xs text-zinc-500">{{ $contributor->school_class }}</p>
                                </div>
                                <x-badge :variant="match ($contributor->pivot->status) { 'accepted' => 'success', 'rejected' => 'danger', default => 'warning' }">
                                    {{ match ($contributor->pivot->status) { 'accepted' => 'Diterima', 'rejected' => 'Ditolak', default => 'Menunggu' } }}
                                </x-badge>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </article>

        <aside class="space-y-5 lg:sticky lg:top-24 lg:self-start">
            <section class="rounded-[1.75rem] border border-zinc-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500">Rating Guru</h2>
                <p class="mt-2 text-2xl font-bold text-zinc-900">{{ $portfolio->ratings_avg_rating ? number_format($portfolio->ratings_avg_rating, 1) : '—' }} <span class="text-sm font-normal text-zinc-500">/ 5 ({{ $portfolio->ratings_count }})</span></p>
                @if ($routePrefix === 'guru')
                    <form method="POST" action="{{ route('guru.portfolios.rate', $portfolio) }}" class="mt-3 flex gap-2">
                        @csrf @method('PUT')
                        <select name="rating" required class="min-w-0 flex-1 rounded-lg border border-zinc-200 px-3 py-2 text-sm">
                            <option value="">Pilih nilai</option>
                            @for ($rating = 1; $rating <= 5; $rating++)
                                <option value="{{ $rating }}" @selected((int) $myRating === $rating)>{{ $rating }}</option>
                            @endfor
                        </select>
                        <button class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white">Simpan</button>
                    </form>
                @endif
            </section>
            <section class="rounded-[1.75rem] border border-zinc-200 bg-white p-5 shadow-sm">
                <h2 class="mb-3 text-sm font-semibold uppercase tracking-wider text-zinc-500">Siswa</h2>
                <div class="flex items-center gap-3">
                    <img src="{{ $portfolio->user->avatarUrl() }}" alt="" class="h-12 w-12 rounded-xl object-cover ring-1 ring-zinc-200">
                    <div>
                        <p class="font-semibold text-zinc-900">{{ $portfolio->user->name }}</p>
                        <p class="text-xs text-zinc-500">{{ $portfolio->user->school_class ?: 'Tanpa kelas' }}</p>
                    </div>
                </div>
            </section>

            <section class="rounded-[1.75rem] border border-zinc-200 bg-white p-5 shadow-sm">
                <h2 class="mb-3 text-sm font-semibold uppercase tracking-wider text-zinc-500">Metadata</h2>
                <dl class="space-y-3 text-sm">
                    <div><dt class="text-xs text-zinc-500">Kategori</dt><dd class="font-medium text-zinc-900">{{ $portfolio->category ?: '—' }}</dd></div>
                    <div><dt class="text-xs text-zinc-500">Tipe Project</dt><dd class="font-medium text-zinc-900">{{ $portfolio->project_type === 'team' ? 'Tim' : 'Personal' }}</dd></div>
                    <div><dt class="text-xs text-zinc-500">Dikirim</dt><dd class="font-medium text-zinc-900">{{ $portfolio->created_at->isoFormat('D MMMM Y HH:mm') }}</dd></div>
                    <div><dt class="text-xs text-zinc-500">Diubah</dt><dd class="font-medium text-zinc-900">{{ $portfolio->updated_at->isoFormat('D MMMM Y HH:mm') }}</dd></div>
                </dl>
            </section>

            <section class="rounded-[1.75rem] border border-zinc-200 bg-white p-5 shadow-sm">
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
