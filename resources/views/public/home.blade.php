@extends('layouts.base')
@section('title', ($school?->name ?? 'Beranda').' · Showcase Karya Siswa')

@section('body')
    @include('layouts.partials.public-nav')

    <main id="main" class="mx-auto max-w-7xl px-4 pb-12 pt-6 md:pt-10 lg:px-8">
        {{-- Asymmetric header strip: skew to right --}}
        <div class="mb-8 flex items-center gap-2 text-xs text-zinc-500">
            <a href="{{ route('home') }}" class="hover:text-blue-600">Beranda</a>
            <span>&rarr;</span>
            <span class="text-zinc-700">{{ \App\Models\School::current()?->name }}</span>
            <span class="ml-auto text-right">
                <x-badge variant="success">
                    <span class="block h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    {{ \App\Models\Portfolio::count() }} karya dipublikasi
                </x-badge>
            </span>
        </div>

        {{-- Hero --}}
        <section class="relative overflow-hidden rounded-3xl border border-zinc-200 bg-white">
            <div class="absolute inset-0 grain opacity-60"></div>
            <div class="absolute -top-20 right-[-10%] h-72 w-72 rounded-full bg-blue-200/40 blur-3xl"></div>
            <div class="absolute -bottom-16 left-[-10%] h-72 w-72 rounded-full bg-zinc-200/60 blur-3xl"></div>

            <div class="relative grid items-center gap-6 p-6 sm:p-8 md:grid-cols-5 md:gap-10 md:p-12">
                <div class="md:col-span-3">
                    <x-badge variant="info">
                        <x-icon name="sparkles" class="h-3 w-3" /> Showcase Karya Siswa
                    </x-badge>
                    <h1 class="font-display mt-3 text-3xl font-bold leading-tight tracking-tight text-zinc-900 text-balance md:text-5xl lg:text-6xl">
                        {{ $school?->name ?? 'Portofolio Siswa' }}
                    </h1>
                    <div class="rich-content mt-4 max-w-prose text-base leading-relaxed text-zinc-600 md:text-lg">
                        {!! $school?->description ? \App\Support\RichText::clean($school->description) : 'Kumpulan karya, keahlian, dan sertifikat siswa dalam satu tempat. Diperbarui langsung oleh siswa dan divalidasi oleh admin sekolah.' !!}
                    </div>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <x-button href="{{ route('portfolios.index') }}" variant="primary" size="lg">
                            Lihat Portofolio <x-icon name="arrow-right" class="h-4 w-4" />
                        </x-button>
                        <x-button href="{{ route('contact.create') }}" variant="outline" size="lg">
                            Hubungi Sekolah
                        </x-button>
                    </div>

                    <dl class="mt-8 grid grid-cols-3 gap-3 border-t border-zinc-100 pt-6 sm:gap-4">
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-zinc-500">Siswa Aktif</dt>
                            <dd class="font-display text-xl font-bold text-zinc-900 sm:text-2xl">{{ $stats['siswa'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-zinc-500">Portofolio</dt>
                            <dd class="font-display text-xl font-bold text-zinc-900 sm:text-2xl">{{ $stats['portfolios'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-zinc-500">Skill</dt>
                            <dd class="font-display text-xl font-bold text-zinc-900 sm:text-2xl">{{ $stats['skills'] }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="relative md:col-span-2">
                    <div class="grid grid-cols-2 gap-3">
                        @foreach ($latest->take(4) as $i => $p)
                            <div class="aspect-[4/5] overflow-hidden rounded-xl border border-zinc-200 bg-zinc-100 {{ $i % 2 === 1 ? 'translate-y-3' : '' }}">
                                <img src="{{ $p->coverUrl() }}" alt="{{ $p->title }}" class="h-full w-full object-cover">
                            </div>
                        @endforeach
                        @if ($latest->isEmpty())
                            @for ($i = 0; $i < 4; $i++)
                                <div class="aspect-[4/5] rounded-xl bg-gradient-to-br from-zinc-100 to-zinc-200 {{ $i % 2 === 1 ? 'translate-y-3' : '' }}"></div>
                            @endfor
                        @endif
                    </div>
                </div>
            </div>
        </section>

        {{-- Skill categories (auto-built from portfolios) --}}
        @if (isset($skillCategories) && $skillCategories->count())
            <section class="mt-10 rounded-2xl border border-zinc-200 bg-white px-6 py-8 md:px-10 md:py-10">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500">Eksplorasi</p>
                        <h2 class="font-display text-xl font-bold tracking-tight text-zinc-900 md:text-2xl">Cari berdasarkan keahlian</h2>
                    </div>
                </div>
                <div class="mt-5 flex flex-wrap gap-2">
                    @foreach ($skillCategories as $cat)
                        <a href="{{ route('portfolios.index', ['skill' => $cat->id]) }}"
                           class="group flex items-center gap-2 rounded-lg border border-zinc-200 bg-white px-4 py-2.5 text-sm font-medium text-zinc-700 transition hover:border-zinc-900 hover:bg-zinc-900 hover:text-white">
                            <x-icon name="briefcase" class="h-4 w-4 text-zinc-400 group-hover:text-white" />
                            {{ $cat->name }}
                            <span class="rounded-md bg-zinc-100 px-1.5 py-0.5 text-xs text-zinc-500 group-hover:bg-zinc-800 group-hover:text-zinc-300">{{ $cat->portfolios_count }}</span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Populer --}}
        @if (isset($popular) && $popular->count())
            <section class="mt-12">
                <div class="mb-5 flex items-end justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-amber-600">Paling Dilihat</p>
                        <h2 class="font-display text-2xl font-bold tracking-tight text-zinc-900 md:text-3xl">Karya Favorit Pembaca</h2>
                    </div>
                </div>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($popular as $i => $p)
                        <a href="{{ route('portfolios.show', $p) }}" class="group relative block overflow-hidden rounded-2xl border border-zinc-200 bg-white transition hover:border-zinc-300 hover:shadow-lg hover:-translate-y-0.5">
                            <div class="relative aspect-[4/3] overflow-hidden bg-zinc-100">
                                <img src="{{ $p->coverUrl() }}" alt="{{ $p->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                <span class="absolute left-3 top-3 flex h-7 w-7 items-center justify-center rounded-full bg-zinc-900 text-sm font-bold text-white">{{ $i + 1 }}</span>
                                <span class="absolute right-3 top-3 inline-flex items-center gap-1 rounded-full bg-white/95 px-2 py-1 text-xs font-medium text-zinc-700 backdrop-blur">
                                    <x-icon name="eye" class="h-3 w-3" /> {{ $p->views }}
                                </span>
                            </div>
                            <div class="p-4">
                                <p class="truncate text-sm font-semibold text-zinc-900 group-hover:text-blue-600">{{ $p->title }}</p>
                                <p class="truncate text-xs text-zinc-500">{{ $p->user->name }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Featured --}}
        @if ($featured->count())
            <section class="mt-12">
                <div class="mb-5 flex items-end justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">Unggulan</p>
                        <h2 class="font-display text-2xl font-bold tracking-tight text-zinc-900 md:text-3xl">Karya Pilihan</h2>
                        <p class="mt-1 text-sm text-zinc-500">Highlight karya terbaik pilihan admin sekolah.</p>
                    </div>
                    <a href="{{ route('portfolios.index') }}" class="text-sm font-medium text-zinc-600 hover:text-blue-600">
                        Semua karya <x-icon name="arrow-right" class="inline h-3 w-3" />
                    </a>
                </div>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($featured as $p)
                        <x-portfolio-card :portfolio="$p" featured />
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Latest + Sidebar (company pitch) --}}
        <section class="mt-12 grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="mb-5 flex items-end justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500">Terbaru</p>
                        <h2 class="font-display text-2xl font-bold tracking-tight text-zinc-900 md:text-3xl">Karya Siswa</h2>
                    </div>
                </div>
                @if ($latest->count())
                    <div class="grid gap-5 sm:grid-cols-2">
                        @foreach ($latest->take(4) as $p)
                            <x-portfolio-card :portfolio="$p" />
                        @endforeach
                    </div>
                    <div class="mt-6 text-center">
                        <a href="{{ route('portfolios.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-zinc-300 bg-white px-5 py-2.5 text-sm font-medium text-zinc-800 transition hover:border-zinc-900 hover:bg-zinc-50">
                            Lihat semua {{ $stats['portfolios'] }} karya <x-icon name="arrow-right" class="h-4 w-4" />
                        </a>
                    </div>
                @else
                    <x-empty-state name="briefcase" title="Belum ada portofolio"
                        description="Siswa akan menambahkan karyanya di sini. Pantau terus halaman ini.">
                        <x-slot:actions>
                            <x-button href="{{ route('register') }}" variant="primary" size="sm">
                                Daftar sebagai siswa
                            </x-button>
                        </x-slot:actions>
                    </x-empty-state>
                @endif
            </div>

            {{-- Sidebar: company-focused content --}}
            <aside class="space-y-5">
                <div id="perusahaan" class="rounded-2xl bg-zinc-900 p-6 text-white">
                    <p class="text-xs font-semibold uppercase tracking-widest text-blue-400">Untuk Perusahaan</p>
                    <h3 class="font-display mt-2 text-xl font-bold leading-tight">Rekrut siswa berbakat, tanpa biaya.</h3>
                    <p class="mt-2 text-sm text-zinc-300">Lihat portofolio, hubungi sekolah, dan mulai proses rekrutmen dalam hitungan hari — bukan minggu.</p>

                    <ol class="mt-5 space-y-3 text-sm">
                        <li class="flex gap-3">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-white text-xs font-bold text-zinc-900">1</span>
                            <div>
                                <p class="font-semibold">Eksplor karya siswa</p>
                                <p class="text-xs text-zinc-400">Filter berdasarkan skill, kategori, atau siswa spesifik.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-white text-xs font-bold text-zinc-900">2</span>
                            <div>
                                <p class="font-semibold">Kirim pesan terstruktur</p>
                                <p class="text-xs text-zinc-400">Form kontak otomatis sampai ke admin sekolah.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-white text-xs font-bold text-zinc-900">3</span>
                            <div>
                                <p class="font-semibold">Interview langsung</p>
                                <p class="text-xs text-zinc-400">Admin memfasilitasi jadwal sesuai kebutuhan Anda.</p>
                            </div>
                        </li>
                    </ol>

                    <a href="{{ route('contact.create') }}" class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-blue-700 active:scale-95">
                        <x-icon name="mail" class="h-4 w-4" /> Mulai Rekrutmen
                    </a>
                </div>

                <div class="rounded-2xl border border-zinc-200 bg-white p-5">
                    <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500">Mengapa Sekolah Kami</p>
                    <ul class="mt-4 space-y-3 text-sm text-zinc-700">
                        <li class="flex items-start gap-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                                <x-icon name="check-circle" class="h-4 w-4" />
                            </span>
                            <div>
                                <p class="font-medium text-zinc-900">Kurikulum Berbasis Proyek</p>
                                <p class="text-xs text-zinc-500">Siswa terbiasa dengan workflow profesional.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                                <x-icon name="check-circle" class="h-4 w-4" />
                            </span>
                            <div>
                                <p class="font-medium text-zinc-900">Portofolio Tervalidasi</p>
                                <p class="text-xs text-zinc-500">Setiap karya diverifikasi oleh admin.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                                <x-icon name="check-circle" class="h-4 w-4" />
                            </span>
                            <div>
                                <p class="font-medium text-zinc-900">Skill Tracking</p>
                                <p class="text-xs text-zinc-500">Level 1–5 memberi transparansi kemampuan.</p>
                            </div>
                        </li>
                    </ul>
                </div>

                @if ($school && ($school->phone || $school->email))
                    <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-5">
                        <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500">Kontak Cepat</p>
                        @if ($school->phone)
                            <a href="tel:{{ $school->phone }}" class="mt-3 flex items-center gap-3 text-sm font-medium text-zinc-800 hover:text-blue-600">
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white text-zinc-700">
                                    <x-icon name="phone" class="h-4 w-4" />
                                </span>
                                {{ $school->phone }}
                            </a>
                        @endif
                        @if ($school->email)
                            <a href="mailto:{{ $school->email }}" class="mt-2 flex items-center gap-3 text-sm font-medium text-zinc-800 hover:text-blue-600">
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white text-zinc-700">
                                    <x-icon name="mail" class="h-4 w-4" />
                                </span>
                                {{ $school->email }}
                            </a>
                        @endif
                    </div>
                @endif
            </aside>
        </section>

        {{-- Categorized views --}}
        @if (isset($categoryPreviews) && $categoryPreviews->count())
            <section class="mt-14 space-y-12">
                @foreach ($categoryPreviews as $cat => $items)
                    <div>
                        <div class="mb-5 flex items-end justify-between border-b border-zinc-200 pb-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500">{{ $cat }}</p>
                                <h2 class="font-display text-2xl font-bold tracking-tight text-zinc-900">Karya Kategori {{ $cat }}</h2>
                            </div>
                            <a href="{{ route('portfolios.index', ['category' => $cat]) }}" class="text-sm font-medium text-zinc-600 hover:text-blue-600">
                                Lihat semua <x-icon name="arrow-right" class="inline h-3 w-3" />
                            </a>
                        </div>
                        <div class="grid gap-5 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                            @foreach ($items as $p)
                                <x-portfolio-card :portfolio="$p" />
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </section>
        @endif

        {{-- Top students --}}
        @if (isset($topStudents) && $topStudents->count())
            <section class="mt-14">
                <div class="mb-5 flex items-end justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">Talenta</p>
                        <h2 class="font-display text-2xl font-bold tracking-tight text-zinc-900 md:text-3xl">Siswa Paling Aktif</h2>
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($topStudents as $u)
                        <a href="{{ route('portfolios.user', $u) }}" class="group rounded-2xl border border-zinc-200 bg-white p-5 transition hover:border-zinc-300 hover:shadow-md hover:-translate-y-0.5">
                            <div class="flex items-center gap-3">
                                <img src="{{ $u->avatarUrl() }}" alt="" class="h-12 w-12 rounded-xl object-cover ring-1 ring-zinc-200">
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-zinc-900 group-hover:text-blue-600">{{ $u->name }}</p>
                                    <p class="truncate text-xs text-zinc-500">{{ $u->school_class }}</p>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center justify-between border-t border-zinc-100 pt-3 text-xs">
                                <span class="text-zinc-500">{{ $u->portfolios_count }} karya</span>
                                <x-icon name="arrow-right" class="h-4 w-4 text-zinc-400 transition group-hover:translate-x-1 group-hover:text-blue-600" />
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- FAQ --}}
        <section class="mt-14 grid gap-6 lg:grid-cols-5" x-data="{ open: null }">
            <div class="lg:col-span-2">
                <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">FAQ</p>
                <h2 class="font-display text-2xl font-bold tracking-tight text-zinc-900 md:text-3xl">Pertanyaan Umum Perusahaan</h2>
                <p class="mt-3 text-sm text-zinc-600">Tidak menemukan jawaban yang Anda cari? Langsung hubungi sekolah.</p>
                <a href="{{ route('contact.create') }}" class="mt-4 inline-flex items-center gap-2 rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-zinc-800 active:scale-95">
                    <x-icon name="mail" class="h-4 w-4" /> Hubungi Sekolah
                </a>
            </div>
            <div class="space-y-2 lg:col-span-3">
                @php
                    $faqs = [
                        ['q' => 'Bagaimana cara menghubungi siswa tertentu?', 'a' => 'Klik portofolio yang menarik, lalu gunakan tombol "Hubungi Sekolah" di sidebar. Pesan akan diteruskan ke admin sekolah untuk difasilitasi.'],
                        ['q' => 'Apakah ada biaya untuk menggunakan platform ini?', 'a' => 'Tidak ada. Platform ini gratis untuk perusahaan. Biaya hanya jika Anda memutuskan untuk merekrut dan itu di luar platform.'],
                        ['q' => 'Bagaimana validasi karya siswa?', 'a' => 'Setiap siswa harus terdaftar dan disetujui admin sekolah. Karya yang ditampilkan sudah melewati verifikasi.'],
                        ['q' => 'Bisakah kami mengundang siswa untuk interview?', 'a' => 'Tentu. Kirim pesan dengan subjek "Penawaran Interview" dan admin sekolah akan membantu penjadwalan.'],
                        ['q' => 'Format data apa saja yang tersedia untuk siswa?', 'a' => 'Portofolio publik mencakup tech stack, demo link, GitHub repo. Data personal hanya bisa diakses melalui sekolah.'],
                    ];
                @endphp
                @foreach ($faqs as $i => $faq)
                    <details class="group rounded-xl border border-zinc-200 bg-white open:bg-zinc-50" {{ $i === 0 ? 'open' : '' }}>
                        <summary class="flex cursor-pointer items-center justify-between gap-4 px-5 py-4 text-sm font-semibold text-zinc-900">
                            <span>{{ $faq['q'] }}</span>
                            <x-icon name="plus" class="h-4 w-4 text-zinc-400 transition group-open:rotate-45" />
                        </summary>
                        <div class="px-5 pb-4 text-sm leading-relaxed text-zinc-700">{{ $faq['a'] }}</div>
                    </details>
                @endforeach
            </div>
        </section>

        {{-- Final CTA --}}
        <section class="mt-14 grid items-center gap-6 rounded-3xl bg-zinc-900 px-6 py-10 text-white md:grid-cols-2 md:px-12 md:py-14">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-blue-400">Siap merekrut?</p>
                <h3 class="font-display mt-2 text-2xl font-bold tracking-tight md:text-4xl">Temukan tim Anda berikutnya dari sini.</h3>
                <p class="mt-3 text-zinc-300">Kirim pesan hari ini — admin sekolah merespon dalam 1–2 hari kerja dengan daftar kandidat yang sesuai.</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row md:justify-end">
                <x-button href="{{ route('portfolios.index') }}" variant="outline" size="lg" class="!border-white/20 !text-white hover:!bg-white/10">
                    <x-icon name="briefcase" class="h-4 w-4" /> Eksplor Karya
                </x-button>
                <x-button href="{{ route('contact.create') }}" variant="accent" size="lg">
                    Kirim Pesan <x-icon name="mail" class="h-4 w-4" />
                </x-button>
            </div>
        </section>
    </main>

    @include('layouts.partials.footer')
@endsection
