@extends('layouts.base')
@section('title', $certificate->title.' · '.$certificate->user->name)

@section('body')
    @include('layouts.partials.public-nav')

    <main id="main" class="mx-auto max-w-7xl px-4 pb-12 pt-6 md:pt-10 lg:px-8">
        <nav class="mb-4 text-xs text-zinc-500">
            <a href="{{ route('home') }}" class="hover:text-blue-600">Beranda</a>
            <span class="mx-1">/</span>
            <a href="{{ route('portfolios.user', $certificate->user) }}" class="hover:text-blue-600">{{ $certificate->user->name }}</a>
            <span class="mx-1">/</span>
            <span class="text-zinc-700">Sertifikat</span>
        </nav>

        <div class="grid gap-6 lg:grid-cols-3">
            <article class="lg:col-span-2">
                <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white">
                    @if ($certificate->file && $certificate->isImage())
                        <div class="aspect-[16/10] bg-zinc-100">
                            <img src="{{ $certificate->fileUrl() }}" alt="{{ $certificate->title }}" class="h-full w-full object-contain bg-zinc-50">
                        </div>
                    @else
                        <div class="flex aspect-[16/10] items-center justify-center bg-gradient-to-br from-amber-50 to-zinc-100">
                            <div class="text-center">
                                <span class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-amber-100 text-amber-700">
                                    <x-icon name="award" class="h-10 w-10" />
                                </span>
                                <p class="mt-4 text-sm font-medium text-zinc-500">Sertifikat</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mt-6">
                    <x-badge variant="warning">Sertifikat</x-badge>
                    <h1 class="font-display mt-3 text-2xl font-bold leading-tight tracking-tight text-zinc-900 md:text-4xl">
                        {{ $certificate->title }}
                    </h1>

                    <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                        @if ($certificate->certificate_number)
                            <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-4">
                                <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-500">No. Sertifikat</dt>
                                <dd class="mt-1 font-mono text-sm font-semibold text-zinc-900">{{ $certificate->certificate_number }}</dd>
                            </div>
                        @endif
                        <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-4">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Penerbit</dt>
                            <dd class="mt-1 text-sm font-semibold text-zinc-900">{{ $certificate->issuer ?: '—' }}</dd>
                        </div>
                        <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-4">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Tanggal Terbit</dt>
                            <dd class="mt-1 text-sm font-semibold text-zinc-900">
                                {{ $certificate->issue_date?->isoFormat('D MMMM Y') ?: '—' }}
                            </dd>
                        </div>
                        <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-4">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Pemilik</dt>
                            <dd class="mt-1 text-sm font-semibold text-zinc-900">
                                <a href="{{ route('portfolios.user', $certificate->user) }}" class="hover:text-blue-600">{{ $certificate->user->name }}</a>
                            </dd>
                        </div>
                    </dl>

                    @if ($certificate->fileUrl())
                        <div class="mt-6">
                            <a href="{{ $certificate->fileUrl() }}" target="_blank" rel="noopener"
                               class="inline-flex items-center gap-2 rounded-lg bg-zinc-900 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-zinc-800">
                                <x-icon name="download" class="h-4 w-4" /> Unduh / Lihat File
                            </a>
                        </div>
                    @endif
                </div>
            </article>

            <aside class="space-y-5 lg:sticky lg:top-24 lg:self-start">
                <div class="rounded-2xl border border-zinc-200 bg-white p-5">
                    <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500">Tentang Siswa</p>
                    <div class="mt-3 flex items-center gap-3">
                        <img src="{{ $certificate->user->avatarUrl() }}" alt="" class="h-12 w-12 rounded-full object-cover ring-1 ring-zinc-200">
                        <div>
                            <p class="font-semibold text-zinc-900">{{ $certificate->user->name }}</p>
                            <p class="text-xs text-zinc-500">{{ $certificate->user->school_class }}</p>
                        </div>
                    </div>
                    <a href="{{ route('portfolios.user', $certificate->user) }}"
                       class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-lg border border-zinc-300 px-3 py-2 text-sm font-medium text-zinc-800 transition hover:bg-zinc-50">
                        Lihat profil lengkap <x-icon name="arrow-right" class="h-4 w-4" />
                    </a>
                </div>

                <div class="rounded-2xl border border-blue-100 bg-blue-50/60 p-5">
                    <p class="text-xs font-semibold uppercase tracking-widest text-blue-700">Untuk Perusahaan</p>
                    <p class="mt-2 text-sm text-zinc-700">Tertarik dengan kredensial {{ $certificate->user->name }}? Hubungi sekolah.</p>
                    <a href="{{ route('contact.create') }}?subject=Penawaran untuk {{ urlencode($certificate->user->name) }}"
                       class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-blue-700">
                        <x-icon name="mail" class="h-4 w-4" /> Hubungi Sekolah
                    </a>
                </div>
            </aside>
        </div>

        @if ($related->count())
            <section class="mt-12">
                <div class="mb-5 border-b border-zinc-200 pb-3">
                    <h2 class="font-display text-xl font-bold tracking-tight text-zinc-900">Sertifikat Lain</h2>
                </div>
                <div class="grid gap-4 sm:grid-cols-3">
                    @foreach ($related as $c)
                        <a href="{{ route('certificates.show', $c) }}" class="group overflow-hidden rounded-2xl border border-zinc-200 bg-white transition hover:border-zinc-300 hover:shadow-md">
                            <div class="flex aspect-[16/10] items-center justify-center bg-amber-50">
                                <x-icon name="award" class="h-8 w-8 text-amber-600" />
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-zinc-900 group-hover:text-blue-600 line-clamp-2">{{ $c->title }}</h3>
                                @if ($c->certificate_number)
                                    <p class="mt-1 font-mono text-xs text-zinc-500">{{ $c->certificate_number }}</p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </main>

    @include('layouts.partials.footer')
@endsection
