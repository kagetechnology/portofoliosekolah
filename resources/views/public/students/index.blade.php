@extends('layouts.base')
@section('title', 'Direktori Siswa')

@section('body')
    @include('layouts.partials.public-nav')

    <main id="main" class="mx-auto max-w-7xl px-4 pb-12 pt-6 md:pt-10 lg:px-8">
        <header class="mb-6 grid gap-6 lg:grid-cols-12">
            <div class="lg:col-span-7">
                <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">Direktori</p>
                <h1 class="font-display text-3xl font-bold tracking-tight text-zinc-900 md:text-4xl">Cari Siswa</h1>
                <p class="mt-2 max-w-prose text-sm text-zinc-600">{{ $totalActive }} siswa aktif · {{ $totalPortfolios }} karya · {{ $totalSkills }} skill dilacak.</p>
            </div>
            <aside class="lg:col-span-5 lg:self-end lg:text-right">
                <p class="text-xs text-zinc-500">Untuk HRD &amp; perekrut.</p>
                <a href="{{ route('contact.create') }}" class="mt-1 inline-flex items-center gap-2 text-sm font-medium text-blue-600 hover:underline">
                    Tidak menemukan kandidat? Kirim pesan langsung <x-icon name="arrow-right" class="h-3 w-3" />
                </a>
            </aside>
        </header>

        <div class="grid gap-6 lg:grid-cols-4">
            <aside class="lg:sticky lg:top-24 lg:self-start">
                <form method="GET" class="space-y-4 rounded-2xl border border-zinc-200 bg-white p-5">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Pencarian</label>
                        <div class="relative mt-2">
                            <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
                            <input type="search" name="q" value="{{ $q }}" placeholder="Nama / bio / kelas..."
                                   class="w-full rounded-lg border border-zinc-200 bg-zinc-50 py-2.5 pl-9 pr-3 text-sm focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Skill <span class="text-zinc-400">(multi)</span></label>
                        <details class="mt-2" open>
                            <summary class="cursor-pointer text-xs text-blue-600 hover:underline">Pilih skill</summary>
                            <div class="mt-2 max-h-56 space-y-1 overflow-y-auto rounded-lg border border-zinc-200 bg-zinc-50 p-2 scrollbar-thin">
                                @foreach ($allSkills as $s)
                                    @php
                                        $selected = in_array($s->id, $skills, true);
                                        $count = $s->portfolios_count ?? 0;
                                    @endphp
                                    <label class="flex items-center justify-between gap-2 rounded px-2 py-1 text-sm hover:bg-white">
                                        <span class="flex items-center gap-2">
                                            <input type="checkbox" name="skills[]" value="{{ $s->id }}" @checked($selected)
                                                   class="h-4 w-4 rounded border-zinc-300 text-blue-600 focus:ring-blue-600">
                                            {{ $s->name }}
                                        </span>
                                        <span class="rounded bg-zinc-200 px-1.5 py-0.5 text-xs text-zinc-600">{{ $count }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </details>
                    </div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Kelas</label>
                        <select name="kelas" class="mt-2 w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                            <option value="">Semua kelas</option>
                            @foreach ($kelasList as $k)
                                <option value="{{ $k }}" @selected($kelas === $k)>{{ $k }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Tahun Masuk</label>
                        <select name="tahun" class="mt-2 w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                            <option value="">Semua tahun</option>
                            @foreach ($tahunList as $t)
                                <option value="{{ $t }}" @selected($tahun == $t)>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Urutkan</label>
                        <select name="sort" class="mt-2 w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                            <option value="recent" @selected($sort === 'recent' || $sort === '')>Terbaru</option>
                            <option value="portfolios" @selected($sort === 'portfolios')>Portofolio Terbanyak</option>
                            <option value="name" @selected($sort === 'name')>Nama A–Z</option>
                        </select>
                    </div>

                    <div class="flex flex-col gap-2 border-t border-zinc-100 pt-3">
                        <button class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-zinc-800 active:scale-95">Terapkan Filter</button>
                        <a href="{{ route('students.index') }}" class="rounded-lg border border-zinc-300 px-4 py-2 text-center text-sm font-medium text-zinc-700 hover:bg-zinc-50">Reset</a>
                    </div>
                </form>
            </aside>

            <div class="lg:col-span-3">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2 text-sm">
                    <p class="text-zinc-600">{{ $students->total() }} siswa ditemukan</p>
                    @if ($q || $skills || $kelas || $tahun)
                        <div class="flex flex-wrap gap-2">
                            @if ($q)
                                <span class="rounded-full bg-blue-50 px-2.5 py-0.5 text-xs text-blue-700">"{{ $q }}"</span>
                            @endif
                            @if ($kelas)
                                <span class="rounded-full bg-blue-50 px-2.5 py-0.5 text-xs text-blue-700">{{ $kelas }}</span>
                            @endif
                            @if ($tahun)
                                <span class="rounded-full bg-blue-50 px-2.5 py-0.5 text-xs text-blue-700">{{ $tahun }}</span>
                            @endif
                            @foreach ($skills as $sid)
                                @php $sn = $allSkills->firstWhere('id', $sid)?->name; @endphp
                                @if ($sn)<span class="rounded-full bg-blue-50 px-2.5 py-0.5 text-xs text-blue-700">{{ $sn }}</span>@endif
                            @endforeach
                        </div>
                    @endif
                </div>

                @if ($students->count())
                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach ($students as $u)
                            @php
                                $skillsList = $u->skillSummary();
                            @endphp
                            <a href="{{ route('portfolios.user', $u) }}" class="group flex flex-col rounded-2xl border border-zinc-200 bg-white p-5 transition hover:border-zinc-300 hover:shadow-md hover:-translate-y-0.5">
                                <div class="flex items-start gap-4">
                                    <img src="{{ $u->avatarUrl() }}" alt="" class="h-14 w-14 rounded-2xl object-cover ring-1 ring-zinc-200">
                                    <div class="min-w-0 flex-1">
                                        <h3 class="font-display text-base font-bold text-zinc-900 group-hover:text-blue-600">{{ $u->name }}</h3>
                                        <p class="text-xs text-zinc-500">
                                            {{ $u->school_class ?: 'Siswa' }}
                                            @if ($u->tahun_masuk) · {{ $u->tahun_masuk }} @endif
                                        </p>
                                    </div>
                                </div>

                                @if ($u->bio)
                                    <div class="rich-content mt-3 line-clamp-2 text-sm text-zinc-700">{!! \App\Support\RichText::clean($u->bio) !!}</div>
                                @endif

                                @if ($skillsList->count())
                                    <div class="mt-4 flex flex-wrap gap-1.5">
                                        @foreach ($skillsList->take(5) as $s)
                                            <span class="inline-flex items-center gap-1 rounded-md bg-zinc-100 px-2 py-0.5 text-xs text-zinc-700">
                                                {{ $s->name }}
                                                <span class="text-xs text-zinc-500">Lv {{ round($s->avg_level, 1) }}</span>
                                            </span>
                                        @endforeach
                                        @if ($skillsList->count() > 5)
                                            <span class="rounded-md bg-zinc-50 px-2 py-0.5 text-xs text-zinc-500">+{{ $skillsList->count() - 5 }}</span>
                                        @endif
                                    </div>
                                @endif

                                <div class="mt-4 flex items-center justify-between border-t border-zinc-100 pt-3 text-xs">
                                    <span class="text-zinc-500">{{ $u->portfolios_count }} karya</span>
                                    <x-icon name="arrow-right" class="h-4 w-4 text-zinc-400 transition group-hover:translate-x-1 group-hover:text-blue-600" />
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <div class="mt-6">{{ $students->links() }}</div>
                @else
                    <x-empty-state name="users" title="Tidak ada siswa"
                        description="Coba longgarkan filter atau kata kunci pencarian." />
                @endif
            </div>
        </div>
    </main>

    @include('layouts.partials.footer')
@endsection
