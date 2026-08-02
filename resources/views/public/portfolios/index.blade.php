@extends('layouts.base')
@section('title', 'Portofolio Siswa')

@section('body')
    @include('layouts.partials.public-nav')

    <main id="main" class="mx-auto max-w-7xl px-4 pb-12 pt-6 md:pt-10 lg:px-8">
        <header class="mb-6">
            <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">Showcase</p>
            <h1 class="font-display text-3xl font-bold tracking-tight text-zinc-900 md:text-4xl">Semua Portofolio</h1>
            <p class="mt-2 max-w-prose text-zinc-600">{{ $portfolios->total() }} karya ditemukan. Filter berdasarkan skill atau kategori untuk menemukan karya yang relevan.</p>
        </header>

        <div class="grid gap-6 lg:grid-cols-4">
            <div class="lg:col-span-3">
                <form method="GET" x-data="{ advanced: false }" class="mb-6 rounded-2xl border border-zinc-200 bg-white p-4 md:p-5">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center">
                        <div class="relative flex-1">
                            <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
                            <input type="search" name="q" value="{{ $q }}" placeholder="Cari judul, nama siswa..."
                                   class="w-full rounded-lg border border-zinc-200 bg-zinc-50 py-2.5 pl-9 pr-3 text-sm focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                        </div>
                        <select name="skill" class="rounded-lg border border-zinc-200 bg-white px-3 py-2.5 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                            <option value="">Semua skill</option>
                            @foreach ($skills as $s)
                                <option value="{{ $s->id }}" @selected($skill == $s->id)>{{ $s->name }}</option>
                            @endforeach
                        </select>
                        <button class="rounded-lg bg-zinc-900 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-zinc-800 active:scale-95">Cari</button>
                    </div>
                    @if ($categories->count())
                        <div class="mt-3 flex flex-wrap gap-2 border-t border-zinc-100 pt-3">
                            <span class="text-xs uppercase tracking-wide text-zinc-500 self-center">Kategori:</span>
                            <a href="{{ route('portfolios.index') }}"
                               class="rounded-full px-3 py-1 text-xs transition {{ $category === '' ? 'bg-zinc-900 text-white' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200' }}">
                                Semua
                            </a>
                            @foreach ($categories as $c)
                                <a href="{{ route('portfolios.index', ['category' => $c]) }}"
                                   class="rounded-full px-3 py-1 text-xs transition {{ $category === $c ? 'bg-zinc-900 text-white' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200' }}">
                                    {{ $c }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </form>

                @if ($portfolios->count())
                    <div class="grid gap-5 sm:grid-cols-2">
                        @foreach ($portfolios as $p)
                            <x-portfolio-card :portfolio="$p" />
                        @endforeach
                    </div>
                    <div class="mt-8">{{ $portfolios->links() }}</div>
                @else
                    <x-empty-state name="search" title="Tidak ditemukan"
                        description="Coba ubah kata kunci atau filter yang berbeda. Belum ada portofolio yang cocok." />
                @endif
            </div>

            <aside class="space-y-5 lg:sticky lg:top-24 lg:self-start">
                <div class="rounded-2xl border border-zinc-200 bg-white p-5">
                    <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500">Eksplor Skill</p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach ($skills->take(20) as $s)
                            <a href="{{ route('portfolios.index', ['skill' => $s->id]) }}"
                               class="rounded-md border border-zinc-200 bg-zinc-50 px-2.5 py-1 text-xs font-medium text-zinc-700 transition hover:border-zinc-900 hover:bg-zinc-900 hover:text-white">
                                {{ $s->name }}
                            </a>
                        @endforeach
                    </div>
                    @if ($skills->count() > 20)
                        <details class="mt-3 text-xs">
                            <summary class="cursor-pointer text-blue-600">Tampilkan {{ $skills->count() - 20 }} skill lainnya</summary>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach ($skills->slice(20) as $s)
                                    <a href="{{ route('portfolios.index', ['skill' => $s->id]) }}"
                                       class="rounded-md border border-zinc-200 bg-zinc-50 px-2.5 py-1 text-xs font-medium text-zinc-700 transition hover:border-zinc-900 hover:bg-zinc-900 hover:text-white">
                                        {{ $s->name }}
                                    </a>
                                @endforeach
                            </div>
                        </details>
                    @endif
                </div>

                @if (isset($topStudents) && $topStudents->count())
                    <div class="rounded-2xl border border-zinc-200 bg-white p-5">
                        <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500">Siswa Paling Aktif</p>
                        <ul class="mt-3 space-y-3">
                            @foreach ($topStudents as $u)
                                <li>
                                    <a href="{{ route('portfolios.user', $u) }}" class="group flex items-center gap-3 rounded-lg p-2 transition hover:bg-zinc-50">
                                        <img src="{{ $u->avatarUrl() }}" alt="" class="h-10 w-10 rounded-full object-cover ring-1 ring-zinc-200">
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-medium text-zinc-900 group-hover:text-blue-600">{{ $u->name }}</p>
                                            <p class="text-xs text-zinc-500">{{ $u->portfolios_count }} karya</p>
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
                    <h3 class="font-display mt-2 text-base font-bold leading-tight">Ingin merekrut siswa tertentu?</h3>
                    <p class="mt-2 text-xs text-zinc-300">Kirim pesan ke sekolah — admin memfasilitasi interview dalam hitungan hari.</p>
                    <a href="{{ route('contact.create') }}" class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-blue-700 active:scale-95">
                        <x-icon name="mail" class="h-4 w-4" /> Hubungi Sekolah
                    </a>
                </div>
            </aside>
        </div>
    </main>

    @include('layouts.partials.footer')
@endsection
