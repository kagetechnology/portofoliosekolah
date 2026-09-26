@php
    $relatedCategories = \App\Models\Portfolio::where('user_id', '!=', $user->id)
        ->where('approval_status', 'approved')
        ->whereHas('user', fn ($q) => $q->where('school_class', $user->school_class))
        ->with('user')
        ->latest()
        ->limit(3)
        ->get();
    $topStudents = \App\Models\User::where('role', 'siswa')
        ->where('status', 'active')
        ->where('id', '!=', $user->id)
        ->withCount(['portfolios' => fn ($q) => $q->where('approval_status', 'approved')])
        ->orderByDesc('portfolios_count')
        ->limit(4)
        ->get();
@endphp

@extends('layouts.base')
@section('title', $user->name.' · Portofolio')

@section('body')
    @include('layouts.partials.public-nav')

    <main id="main" class="mx-auto max-w-7xl px-4 pb-12 pt-6 md:pt-10 lg:px-8">
        <div class="grid gap-6 lg:grid-cols-4">
            <header class="lg:col-span-3">
                <div class="rounded-3xl border border-zinc-200 bg-white p-6 md:p-10">
                    <div class="flex flex-col items-start gap-6 md:flex-row md:items-center">
                        <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}"
                             class="h-24 w-24 shrink-0 rounded-2xl object-cover ring-1 ring-zinc-200">
                        <div class="flex-1">
                            <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">{{ $user->school_class ?? 'Siswa' }}</p>
                            <h1 class="font-display mt-1 text-3xl font-bold tracking-tight text-zinc-900 md:text-4xl">{{ $user->name }}</h1>
                            @if ($user->bio)
                                <div class="rich-content mt-3 max-w-2xl text-zinc-700">{!! \App\Support\RichText::clean($user->bio) !!}</div>
                            @endif
                            <div class="mt-5 flex flex-wrap gap-3 border-t border-zinc-100 pt-5 text-sm">
                                <div class="flex items-center gap-2 text-zinc-600">
                                    <x-icon name="briefcase" class="h-4 w-4 text-zinc-400" /> {{ $portfolios->total() }} karya
                                </div>
                                <div class="flex items-center gap-2 text-zinc-600">
                                    <x-icon name="award" class="h-4 w-4 text-zinc-400" /> {{ $certificates->count() }} sertifikat
                                </div>
                                @if ($user->school_class)
                                    <div class="flex items-center gap-2 text-zinc-600">
                                        <x-icon name="building" class="h-4 w-4 text-zinc-400" /> {{ $user->school_class }}
                                    </div>
                                @endif
                                @if ($user->tahun_masuk)
                                    <div class="flex items-center gap-2 text-zinc-600">
                                        <x-icon name="award" class="h-4 w-4 text-zinc-400" /> Masuk {{ $user->tahun_masuk }}
                                    </div>
                                @endif
                                @if ($user->github_url)
                                    <a href="{{ $user->github_url }}" target="_blank" rel="noopener" class="flex items-center gap-2 text-zinc-600 hover:text-blue-600">
                                        <x-icon name="github" class="h-4 w-4" /> GitHub
                                    </a>
                                @endif
                                @if ($user->instagram_url)
                                    <a href="{{ $user->instagram_url }}" target="_blank" rel="noopener" class="flex items-center gap-2 text-zinc-600 hover:text-blue-600">
                                        <x-icon name="instagram" class="h-4 w-4" /> Instagram
                                    </a>
                                @endif
                                @if ($user->email)
                                    <a href="mailto:{{ $user->email }}" class="flex items-center gap-2 text-zinc-600 hover:text-blue-600">
                                        <x-icon name="mail" class="h-4 w-4" /> {{ $user->email }}
                                    </a>
                                @endif
                                @if ($user->linkedin_url)
                                    <a href="{{ $user->linkedin_url }}" target="_blank" rel="noopener" class="flex items-center gap-2 text-zinc-600 hover:text-blue-600">
                                        LinkedIn
                                    </a>
                                @endif
                            </div>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <a href="{{ route('contact.create') }}?subject=Penawaran untuk {{ urlencode($user->name) }}"
                                   class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700 active:scale-95">
                                    <x-icon name="mail" class="h-4 w-4" /> Hubungi Sekolah
                                </a>
                                <a href="{{ route('portfolios.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-800 transition hover:bg-zinc-50">
                                    <x-icon name="briefcase" class="h-4 w-4" /> Karya Siswa Lain
                                </a>
                                <a href="{{ route('students.cv', $user) }}" target="_blank" class="inline-flex items-center gap-2 rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-800 transition hover:bg-zinc-50">
                                    <x-icon name="download" class="h-4 w-4" /> Print CV
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <section class="mt-10">
                    <div class="mb-5 flex items-end justify-between">
                        <h2 class="font-display text-2xl font-bold tracking-tight text-zinc-900">Karya</h2>
                        <p class="text-sm text-zinc-500">{{ $portfolios->total() }} karya</p>
                    </div>
                    @if ($portfolios->count())
                        <div class="grid gap-5 sm:grid-cols-2">
                            @foreach ($portfolios as $p)
                                <x-portfolio-card :portfolio="$p" />
                            @endforeach
                        </div>
                        <div class="mt-8">{{ $portfolios->links() }}</div>
                    @else
                        <x-empty-state name="briefcase" title="Belum ada karya"
                            description="Siswa ini belum menambahkan portofolio." />
                    @endif
                </section>

                @if (isset($skillSummary) && $skillSummary->count())
                    <section class="mt-12">
                        <div class="mb-5 flex items-end justify-between border-b border-zinc-200 pb-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500">Tech Stack</p>
                                <h2 class="font-display text-2xl font-bold tracking-tight text-zinc-900">Skill &amp; Level</h2>
                            </div>
                            <p class="text-sm text-zinc-500">{{ $skillSummary->count() }} skill</p>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($skillSummary as $s)
                                @php
                                    $level = (int) round($s->avg_level);
                                    $pct = max(0, min(100, ($level / 5) * 100));
                                @endphp
                                <div class="rounded-xl border border-zinc-200 bg-white p-4">
                                    <div class="flex items-center justify-between">
                                        <h3 class="font-display text-base font-semibold text-zinc-900">{{ $s->name }}</h3>
                                        <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-700">Lv {{ $level }}</span>
                                    </div>
                                    <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-zinc-100">
                                        <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-blue-600" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <p class="mt-2 text-xs text-zinc-500">Rata-rata level dari {{ $s->used_in }} proyek</p>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if ($certificates->count())
                    <section class="mt-12">
                        <div class="mb-5 flex items-end justify-between border-b border-zinc-200 pb-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-widest text-amber-600">Kredensial</p>
                                <h2 class="font-display text-2xl font-bold tracking-tight text-zinc-900">Sertifikat</h2>
                            </div>
                            <p class="text-sm text-zinc-500">{{ $certificates->count() }} sertifikat</p>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach ($certificates as $c)
                                <a href="{{ route('certificates.show', $c) }}" class="group overflow-hidden rounded-2xl border border-zinc-200 bg-white transition hover:border-zinc-300 hover:shadow-md">
                                    @if ($c->file && $c->isImage())
                                        <div class="aspect-[16/10] bg-zinc-100">
                                            <img src="{{ $c->fileUrl() }}" alt="{{ $c->title }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                                        </div>
                                    @else
                                        <div class="flex aspect-[16/10] items-center justify-center bg-amber-50">
                                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">
                                                <x-icon name="award" class="h-6 w-6" />
                                            </span>
                                        </div>
                                    @endif
                                    <div class="p-4">
                                        <h3 class="font-semibold text-zinc-900 group-hover:text-blue-600">{{ $c->title }}</h3>
                                        @if ($c->certificate_number)
                                            <p class="mt-1 font-mono text-xs text-zinc-600">{{ $c->certificate_number }}</p>
                                        @endif
                                        <p class="mt-1 text-xs text-zinc-500">
                                            {{ $c->issuer ?: 'Tanpa penerbit' }}
                                            @if ($c->issue_date) · {{ $c->issue_date->isoFormat('MMM Y') }} @endif
                                        </p>
                                        <span class="mt-3 inline-flex items-center gap-1.5 text-xs font-medium text-blue-600">
                                            Lihat detail <x-icon name="arrow-right" class="h-3.5 w-3.5 transition group-hover:translate-x-1" />
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif
            </header>

            <aside class="space-y-5 lg:sticky lg:top-24 lg:self-start">
                @if ($topStudents->count())
                    <div class="rounded-2xl border border-zinc-200 bg-white p-5">
                        <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500">Siswa Sebaya</p>
                        <ul class="mt-3 space-y-3">
                            @foreach ($topStudents as $u)
                                <li>
                                    <a href="{{ route('portfolios.user', $u) }}" class="group flex items-center gap-3 rounded-lg p-2 transition hover:bg-zinc-50">
                                        <img src="{{ $u->avatarUrl() }}" alt="" class="h-10 w-10 rounded-full object-cover ring-1 ring-zinc-200">
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-medium text-zinc-900 group-hover:text-blue-600">{{ $u->name }}</p>
                                            <p class="truncate text-xs text-zinc-500">{{ $u->school_class }} · {{ $u->portfolios_count }} karya</p>
                                        </div>
                                        <x-icon name="arrow-right" class="h-3 w-3 text-zinc-300 transition group-hover:translate-x-1 group-hover:text-blue-600" />
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="rounded-2xl bg-zinc-900 p-5 text-white">
                    <p class="text-xs font-semibold uppercase tracking-widest text-blue-400">Untuk Perusahaan</p>
                    <h3 class="font-display mt-2 text-base font-bold">Rekrut {{ explode(' ', $user->name)[0] }}</h3>
                    <p class="mt-2 text-xs text-zinc-300">Kirim pesan ke sekolah untuk diskusi rekrutmen, magang, atau proyek.</p>
                    <a href="{{ route('contact.create') }}?subject=Penawaran untuk {{ urlencode($user->name) }}"
                       class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-blue-700 active:scale-95">
                        <x-icon name="mail" class="h-4 w-4" /> Kirim Penawaran
                    </a>
                </div>
            </aside>
        </div>
    </main>

    @include('layouts.partials.footer')
@endsection
