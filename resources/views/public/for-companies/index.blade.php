@extends('layouts.base')
@section('title', 'Untuk Perusahaan · '.\App\Models\School::current()?->name)

@section('body')
    @include('layouts.partials.public-nav')

    <main id="main">

        {{-- ASYMMETRIC HERO: full-bleed, 2-col unequal (kiri 60% centered-ish, kanan 40% offset ke atas) --}}
        <section class="relative overflow-hidden bg-zinc-900 text-white">
            <div class="absolute inset-0 grain opacity-40"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-blue-600/20 to-transparent"></div>

            <div class="relative mx-auto grid max-w-7xl items-end gap-10 px-4 pb-16 pt-16 md:grid-cols-12 md:px-8 md:pb-24 md:pt-24 lg:gap-12">
                {{-- Asymmetric: kiri = 60%, bottom-aligned --}}
                <div class="md:col-span-7">
                    <x-badge variant="info">
                        <x-icon name="briefcase" class="h-3 w-3" /> Untuk Perusahaan &amp; HRD
                    </x-badge>
                    <h1 class="font-display mt-4 text-4xl font-bold leading-[1.05] tracking-tight md:text-6xl lg:text-7xl">
                        Rekrut siswa,<br>
                        <span class="text-blue-400">bukan CV.</span>
                    </h1>
                    <p class="mt-6 max-w-xl text-lg leading-relaxed text-zinc-300 md:text-xl">
                        Karya lebih jujur dari kata-kata. Lihat apa yang sudah dibuat siswa, hubungi sekolah, dan mulai proses rekrutmen dalam hitungan hari — bukan minggu.
                    </p>

                    {{-- Asymmetric CTA cluster: primary besar + secondary text-link --}}
                    <div class="mt-10 flex flex-wrap items-center gap-x-8 gap-y-4">
                        <a href="#cara-kerja" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-7 py-3.5 text-base font-medium text-white transition hover:bg-blue-700 active:scale-95">
                            Lihat cara kerja <x-icon name="arrow-right" class="h-4 w-4" />
                        </a>
                        <a href="{{ route('contact.create') }}" class="text-sm font-medium text-zinc-300 hover:text-white">
                            atau langsung kirim pesan &rarr;
                        </a>
                    </div>
                </div>

                {{-- Asymmetric: kanan = 40%, offset naik ke atas --}}
                <div class="md:col-span-5 md:-mt-12 lg:-mt-20">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-6 backdrop-blur md:p-8">
                        <p class="text-xs font-semibold uppercase tracking-widest text-blue-400">Statistik Real-time</p>
                        <ul class="mt-6 space-y-5">
                            @foreach ($stats as $stat)
                                <li class="flex items-end justify-between gap-4 border-b border-white/10 pb-5 last:border-0 last:pb-0">
                                    <div>
                                        <p class="text-xs uppercase tracking-wider text-zinc-400">{{ $stat['label'] }}</p>
                                        <p class="font-display mt-1 text-3xl font-bold text-white md:text-4xl">{{ $stat['value'] }}</p>
                                    </div>
                                    <span class="text-xs text-zinc-500">aktif</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Asymmetric bottom strip: logo-strip style "trusted by" --}}
            <div class="relative border-t border-white/10 bg-white/[0.03]">
                <div class="mx-auto flex max-w-7xl flex-wrap items-center gap-x-8 gap-y-3 px-4 py-5 text-xs text-zinc-400 md:px-8">
                    <span class="font-semibold uppercase tracking-wider text-zinc-500">Cocok untuk:</span>
                    <span>Startup</span>
                    <span class="text-zinc-600">·</span>
                    <span>SaaS</span>
                    <span class="text-zinc-600">·</span>
                    <span>Agency Digital</span>
                    <span class="text-zinc-600">·</span>
                    <span>Konsultan IT</span>
                    <span class="text-zinc-600">·</span>
                    <span>Perusahaan Manufaktur</span>
                </div>
            </div>
        </section>

        {{-- CARA KERJA: timeline asymmetric (zig-zag) --}}
        <section id="cara-kerja" class="relative bg-white">
            <div class="mx-auto max-w-7xl px-4 py-16 md:px-8 md:py-24">
                <div class="grid items-start gap-12 md:grid-cols-12">
                    {{-- Sticky left rail --}}
                    <div class="md:col-span-4 md:sticky md:top-24">
                        <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">Cara Kerja</p>
                        <h2 class="font-display mt-2 text-3xl font-bold tracking-tight text-zinc-900 md:text-4xl">Dari eksplorasi sampai kontrak.</h2>
                        <p class="mt-3 text-sm text-zinc-600">Empat langkah, transparan, tidak ada biaya tersembunyi.</p>

                        <div class="mt-6 hidden rounded-xl border border-zinc-200 bg-zinc-50 p-4 md:block">
                            <p class="text-xs uppercase tracking-wider text-zinc-500">Butuh negosiasi khusus?</p>
                            <a href="{{ route('contact.create') }}" class="mt-2 inline-flex text-sm font-medium text-blue-600 hover:underline">Hubungi admin sekolah &rarr;</a>
                        </div>
                    </div>

                    {{-- Right column: zig-zag timeline --}}
                    <ol class="md:col-span-8">
                        @foreach ($steps as $i => $step)
                            <li class="grid grid-cols-12 gap-4 border-t border-zinc-200 py-8 first:border-t-0 first:pt-0">
                                {{-- Number: alternating left/right on larger screens --}}
                                <div class="col-span-12 {{ $i % 2 === 0 ? 'md:col-span-4 md:order-1' : 'md:col-span-4 md:order-3 md:text-right' }}">
                                    <span class="font-display text-5xl font-bold text-zinc-200 md:text-6xl">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                    <p class="mt-2 text-xs font-semibold uppercase tracking-wider text-blue-600">{{ $step['detail'] }}</p>
                                </div>

                                {{-- Content --}}
                                <div class="{{ $i % 2 === 0 ? 'md:col-span-8 md:order-2' : 'md:col-span-8 md:order-1' }}">
                                    <div class="rounded-2xl border border-zinc-200 bg-white p-6 transition hover:border-zinc-300 hover:shadow-md">
                                        <div class="flex items-start gap-4">
                                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-zinc-900 text-white">
                                                <x-icon :name="$step['icon']" class="h-5 w-5" />
                                            </span>
                                            <div>
                                                <h3 class="font-display text-xl font-bold text-zinc-900">{{ $step['title'] }}</h3>
                                                <p class="mt-1.5 text-sm leading-relaxed text-zinc-700">{{ $step['desc'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </section>

        {{-- KARYA: full-bleed dark band w/ asymmetric mosaic --}}
        @if ($featuredPortfolios->count())
            <section class="bg-zinc-50">
                <div class="mx-auto max-w-7xl px-4 py-16 md:px-8 md:py-24">
                    <div class="mb-12 flex flex-wrap items-end justify-between gap-6">
                        <div class="max-w-2xl">
                            <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">Karya Unggulan</p>
                            <h2 class="font-display mt-2 text-3xl font-bold tracking-tight text-zinc-900 md:text-5xl">
                                Karya terbaik, bukan portofolio filler.
                            </h2>
                            <p class="mt-3 text-base text-zinc-600">Setiap karya melalui kurasi admin sekolah. Klik untuk lihat detail lengkap, demo, dan source code.</p>
                        </div>
                        <a href="{{ route('portfolios.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-zinc-300 bg-white px-5 py-2.5 text-sm font-medium text-zinc-800 transition hover:bg-zinc-100">
                            Eksplor semua <x-icon name="arrow-right" class="h-4 w-4" />
                        </a>
                    </div>

                    {{-- Asymmetric mosaic: 1 large + 4 stacked --}}
                    <div class="grid gap-4 md:grid-cols-12">
                        @php $first = $featuredPortfolios->first(); @endphp
                        <a href="{{ route('portfolios.show', $first) }}" class="group relative col-span-12 row-span-2 overflow-hidden rounded-2xl border border-zinc-200 bg-white md:col-span-7 md:row-span-2">
                            <div class="aspect-[4/3] overflow-hidden bg-zinc-100 md:aspect-auto md:h-full">
                                <img src="{{ $first->coverUrl() }}" alt="{{ $first->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            </div>
                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent p-6">
                                <x-badge variant="info" class="!bg-white/20 !text-white backdrop-blur">{{ $first->category }}</x-badge>
                                <h3 class="font-display mt-2 text-2xl font-bold text-white">{{ $first->title }}</h3>
                                <p class="mt-1 text-sm text-zinc-200">{{ $first->user->name }} · {{ $first->user->school_class }}</p>
                            </div>
                        </a>

                        @foreach ($featuredPortfolios->skip(1)->take(4) as $p)
                            <a href="{{ route('portfolios.show', $p) }}" class="group relative col-span-6 overflow-hidden rounded-2xl border border-zinc-200 bg-white md:col-span-5 lg:col-span-5
                                @if ($loop->index === 0) md:col-span-5
                                @elseif ($loop->index === 1) md:col-span-3 lg:col-span-5
                                @elseif ($loop->index === 2) md:col-span-5 lg:col-span-5
                                @else md:col-span-5 @endif">
                                <div class="aspect-[4/3] overflow-hidden bg-zinc-100 md:aspect-[16/9]">
                                    <img src="{{ $p->coverUrl() }}" alt="" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                                </div>
                                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                                    <p class="font-display text-base font-bold text-white">{{ $p->title }}</p>
                                    <p class="text-xs text-zinc-200">{{ $p->user->name }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- BENEFITS: Bento grid asymmetric --}}
        <section class="bg-white">
            <div class="mx-auto max-w-7xl px-4 py-16 md:px-8 md:py-24">
                <div class="mb-12 max-w-2xl">
                    <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">Mengapa Platform Ini</p>
                    <h2 class="font-display mt-2 text-3xl font-bold tracking-tight text-zinc-900 md:text-5xl">Dibangun untuk meminimalkan friksi.</h2>
                </div>

                @php
                    $first = $benefits[0]; $rest = array_slice($benefits, 1);
                @endphp
                <div class="grid gap-4 md:grid-cols-12">
                    <div class="rounded-3xl bg-zinc-900 p-8 text-white md:col-span-7 lg:col-span-5">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10">
                            <x-icon :name="$first['icon']" class="h-5 w-5" />
                        </span>
                        <h3 class="font-display mt-5 text-2xl font-bold">{{ $first['title'] }}</h3>
                        <p class="mt-3 max-w-md text-base leading-relaxed text-zinc-300">{{ $first['desc'] }}</p>
                        <a href="#cara-kerja" class="mt-6 inline-flex items-center gap-2 text-sm font-medium text-blue-400 hover:text-blue-300">
                            Pelajari caranya <x-icon name="arrow-right" class="h-3 w-3" />
                        </a>
                    </div>

                    <div class="grid gap-4 md:col-span-5 lg:col-span-7">
                        @foreach (array_chunk($rest, 2) as $chunk)
                            <div class="grid gap-4 {{ count($chunk) === 2 ? 'md:grid-cols-2' : '' }}">
                                @foreach ($chunk as $b)
                                    <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-6 transition hover:border-zinc-900 hover:bg-white">
                                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-white text-zinc-700">
                                            <x-icon :name="$b['icon']" class="h-4 w-4" />
                                        </span>
                                        <h3 class="font-display mt-4 text-lg font-bold text-zinc-900">{{ $b['title'] }}</h3>
                                        <p class="mt-2 text-sm leading-relaxed text-zinc-700">{{ $b['desc'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- SISWA UNGGULAN: magazine layout, asymmetric --}}
        @if ($topStudents->count())
            <section class="bg-zinc-50">
                <div class="mx-auto max-w-7xl px-4 py-16 md:px-8 md:py-24">
                    <div class="mb-12 grid items-end gap-6 md:grid-cols-12">
                        <div class="md:col-span-7">
                            <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">Talenta</p>
                            <h2 class="font-display mt-2 text-3xl font-bold tracking-tight text-zinc-900 md:text-5xl">
                                Siswa paling aktif minggu ini.
                            </h2>
                        </div>
                        <div class="md:col-span-5 md:text-right">
                            <a href="{{ route('portfolios.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-zinc-700 hover:text-blue-600">
                                Lihat semua karya <x-icon name="arrow-right" class="h-4 w-4" />
                            </a>
                        </div>
                    </div>

                    @php $lead = $topStudents->first(); $others = $topStudents->slice(1); @endphp

                    <div class="grid gap-5 md:grid-cols-12">
                        <a href="{{ route('portfolios.user', $lead) }}" class="group relative overflow-hidden rounded-3xl border border-zinc-200 bg-white p-8 transition hover:border-zinc-900 md:col-span-5 lg:col-span-4">
                            <div class="flex items-start gap-4">
                                <img src="{{ $lead->avatarUrl() }}" alt="" class="h-16 w-16 rounded-2xl object-cover ring-1 ring-zinc-200">
                                <div class="flex-1">
                                    <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">Top Talent</p>
                                    <h3 class="font-display mt-1 text-2xl font-bold text-zinc-900 group-hover:text-blue-600">{{ $lead->name }}</h3>
                                    <p class="text-sm text-zinc-600">{{ $lead->school_class }}</p>
                                </div>
                            </div>
                            @if ($lead->bio)
                                <div class="rich-content mt-6 text-sm leading-relaxed text-zinc-700">{!! \App\Support\RichText::clean($lead->bio) !!}</div>
                            @endif
                            <div class="mt-6 flex items-center justify-between border-t border-zinc-100 pt-4">
                                <span class="text-sm text-zinc-500">{{ $lead->portfolios_count }} karya</span>
                                <span class="inline-flex items-center gap-2 text-sm font-medium text-blue-600">
                                    Lihat profil <x-icon name="arrow-right" class="h-4 w-4 transition group-hover:translate-x-1" />
                                </span>
                            </div>
                        </a>

                        <div class="grid grid-cols-2 gap-3 md:col-span-7 lg:col-span-8">
                            @foreach ($others as $u)
                                <a href="{{ route('portfolios.user', $u) }}" class="group rounded-2xl border border-zinc-200 bg-white p-5 transition hover:border-zinc-900">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $u->avatarUrl() }}" alt="" class="h-11 w-11 rounded-xl object-cover ring-1 ring-zinc-200">
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate font-semibold text-zinc-900 group-hover:text-blue-600">{{ $u->name }}</p>
                                            <p class="truncate text-xs text-zinc-500">{{ $u->school_class }}</p>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex items-center justify-between text-xs text-zinc-500">
                                        <span>{{ $u->portfolios_count }} karya</span>
                                        <x-icon name="arrow-right" class="h-3 w-3 transition group-hover:translate-x-1 group-hover:text-blue-600" />
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        @endif

        {{-- KATEGORI: horizontal scroll strip, NO centered grid --}}
        @if ($categories->count())
            <section class="border-y border-zinc-200 bg-white">
                <div class="mx-auto max-w-7xl px-4 py-12 md:px-8 md:py-16">
                    <div class="mb-6 flex items-end justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500">Kategori</p>
                            <h2 class="font-display mt-1 text-2xl font-bold tracking-tight text-zinc-900">Eksplor berdasarkan bidang</h2>
                        </div>
                    </div>
                    <div class="-mx-4 flex overflow-x-auto px-4 pb-2 scrollbar-thin md:-mx-8 md:px-8">
                        @foreach ($categories as $c)
                            <a href="{{ route('portfolios.index', ['category' => $c]) }}"
                               class="group inline-flex shrink-0 items-center gap-3 rounded-full border border-zinc-200 bg-white px-5 py-2.5 text-sm font-medium text-zinc-700 transition hover:border-zinc-900 hover:bg-zinc-900 hover:text-white">
                                <x-icon name="briefcase" class="h-4 w-4 text-zinc-400 group-hover:text-white" />
                                {{ $c }}
                                <x-icon name="arrow-right" class="h-3 w-3 text-zinc-300 group-hover:translate-x-0.5 group-hover:text-white" />
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- TESTIMONIAL: pull-quote asymmetric --}}
        <section class="bg-zinc-900 text-white">
            <div class="mx-auto grid max-w-7xl gap-12 px-4 py-16 md:grid-cols-12 md:px-8 md:py-24">
                <div class="md:col-span-2 md:flex md:justify-end">
                    <x-icon name="sparkles" class="h-10 w-10 text-blue-400" />
                </div>
                <blockquote class="md:col-span-7">
                    <p class="font-display text-2xl font-bold leading-tight md:text-3xl lg:text-4xl">
                        "Daripada筛选 seratus CV, saya lihat langsung portofolio siswa. Dalam dua minggu kami sudah merekrut dua orang dari sini."
                    </p>
                    <footer class="mt-6 text-sm text-zinc-400">— Contoh testimonial dari HRD perusahaan partner.</footer>
                </blockquote>
                <div class="md:col-span-3 md:text-right">
                    <p class="text-xs uppercase tracking-wider text-zinc-500">Catatan:</p>
                    <p class="mt-2 text-xs text-zinc-400">Testimoni akan diperbarui setelah program berjalan.</p>
                </div>
            </div>
        </section>

        {{-- FAQ: 2-col asymmetric (compact left, accordion right) --}}
        <section class="bg-white">
            <div class="mx-auto max-w-7xl px-4 py-16 md:px-8 md:py-24">
                <div class="grid gap-10 md:grid-cols-12">
                    <div class="md:col-span-4">
                        <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">FAQ</p>
                        <h2 class="font-display mt-2 text-3xl font-bold tracking-tight text-zinc-900 md:text-4xl">
                            Pertanyaan yang sering ditanyakan.
                        </h2>
                        <p class="mt-3 text-sm text-zinc-600">Jawaban untuk pertanyaan paling umum dari tim HR.</p>

                        <div class="mt-6 rounded-2xl border border-zinc-200 bg-zinc-50 p-5">
                            <p class="text-sm font-semibold text-zinc-900">Pertanyaan spesifik?</p>
                            <p class="mt-1 text-xs text-zinc-600">Admin sekolah merespon dalam 1–2 hari kerja.</p>
                            <a href="{{ route('contact.create') }}" class="mt-3 inline-flex items-center gap-2 text-sm font-medium text-blue-600 hover:underline">
                                Kirim pertanyaan <x-icon name="arrow-right" class="h-3 w-3" />
                            </a>
                        </div>
                    </div>

                    <div class="space-y-2 md:col-span-8">
                        @php
                            $faqs = [
                                ['q' => 'Berapa biaya untuk menggunakan platform?', 'a' => 'Gratis untuk perusahaan. Tidak ada komisi rekrutmen, tidak ada biaya iklan. Platform ini adalah inisiatif sekolah untuk membantu alumninya.'],
                                ['q' => 'Berapa lama proses rekrutmen?', 'a' => 'Rata-rata 7–14 hari dari pesan pertama sampai penawaran. Lebih cepat dari proses konvensional karena kita skip fase筛选 CV awal.'],
                                ['q' => 'Apakah data siswa bisa diakses langsung?', 'a' => 'Tidak. Semua komunikasi awal lewat admin sekolah. Privasi siswa terjaga sesuai peraturan perlindungan data.'],
                                ['q' => 'Untuk level jabatan apa?', 'a' => 'Magang, kontrak, full-time entry-level sampai junior. Beberapa siswa sudah punya 2+ tahun pengalaman freelance.'],
                                ['q' => 'Bagaimana jika siswa yang kami rekrut tidak cocok?', 'a' => 'Standar 3 bulan pertama. Jika tidak cocok, kami bantu rekrut siswa lain tanpa biaya tambahan.'],
                                ['q' => 'Apakah ada NDA atau kontrak khusus?', 'a' => 'Sepenuhnya mengikuti hukum Indonesia. Kami bisa membantu draf kontrak kerja sama dengan sekolah sesuai kebutuhan.'],
                            ];
                        @endphp
                        @foreach ($faqs as $i => $faq)
                            <details class="group rounded-xl border border-zinc-200 bg-white open:bg-zinc-50" {{ $i === 0 ? 'open' : '' }}>
                                <summary class="flex cursor-pointer items-center justify-between gap-4 px-5 py-4 text-sm font-semibold text-zinc-900">
                                    <span class="flex items-center gap-3">
                                        <span class="font-display text-base font-bold text-zinc-300">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                        {{ $faq['q'] }}
                                    </span>
                                    <x-icon name="plus" class="h-4 w-4 text-zinc-400 transition group-open:rotate-45" />
                                </summary>
                                <div class="px-5 pb-4 text-sm leading-relaxed text-zinc-700">{{ $faq['a'] }}</div>
                            </details>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- FINAL CTA: dark, asymmetric (text left, form shortcut right) --}}
        <section class="relative overflow-hidden bg-gradient-to-br from-zinc-900 via-zinc-900 to-blue-900 text-white">
            <div class="absolute right-0 top-0 h-72 w-72 rounded-full bg-blue-600/30 blur-3xl"></div>

            <div class="relative mx-auto grid max-w-7xl gap-10 px-4 py-16 md:grid-cols-12 md:px-8 md:py-24">
                <div class="md:col-span-7">
                    <p class="text-xs font-semibold uppercase tracking-widest text-blue-400">Mulai Sekarang</p>
                    <h2 class="font-display mt-2 text-3xl font-bold leading-tight tracking-tight md:text-5xl">
                        Kirim pesan hari ini.<br>
                        <span class="text-blue-400">Dapatkan kandidat minggu ini.</span>
                    </h2>
                    <p class="mt-4 max-w-xl text-lg text-zinc-300">Tidak perlu panjang-panjang. Sebutkan kebutuhan Anda — kami yang筛选 dan kirim daftar.</p>

                    <div class="mt-8 flex flex-wrap items-center gap-4">
                        <a href="{{ route('contact.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-7 py-3.5 text-base font-medium text-white transition hover:bg-blue-700 active:scale-95">
                            <x-icon name="mail" class="h-4 w-4" /> Kirim Pesan
                        </a>
                        <a href="{{ route('portfolios.index') }}" class="text-sm font-medium text-zinc-300 hover:text-white">
                            atau lihat portofolio dulu &rarr;
                        </a>
                    </div>
                </div>

                <div class="md:col-span-5">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-7 backdrop-blur">
                        <p class="text-xs uppercase tracking-widest text-zinc-400">Atau langsung telepon:</p>
                        @if ($school?->phone)
                            <a href="tel:{{ $school->phone }}" class="font-display mt-2 block text-3xl font-bold text-white md:text-4xl">{{ $school->phone }}</a>
                        @endif
                        @if ($school?->email)
                            <a href="mailto:{{ $school->email }}" class="mt-2 block text-sm text-blue-300 hover:text-blue-200">{{ $school->email }}</a>
                        @endif
                        <div class="mt-6 border-t border-white/10 pt-5 text-xs text-zinc-400">
                            <p class="font-semibold text-zinc-200">{{ $school?->name ?? 'Sekolah' }}</p>
                            <p class="mt-1">{{ $school?->address }}</p>
                            @if ($school?->kepala_sekolah)
                                <p class="mt-1">Kepala Sekolah: {{ $school->kepala_sekolah }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    @include('layouts.partials.footer')
@endsection
