@extends('layouts.siswa')
@section('title', 'Dashboard Siswa')

@section('content')
    @php
        $firstName = explode(' ', trim($user->name))[0] ?: $user->name;
        $portfolioTotal = $user->visiblePortfolios()->count();
        $certificateTotal = $user->certificates()->count();
        $featuredTotal = $user->portfolios()->where('is_featured', true)->count();
        $pendingWork = $user->portfolios()->where('approval_status', 'pending')->count() + $user->certificates()->where('approval_status', 'pending')->count();
    @endphp

    <div class="space-y-6">
        <section class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_22rem]">
            <div class="relative overflow-hidden rounded-[2rem] border border-zinc-200 bg-white p-6 shadow-sm md:p-8">
                <div class="absolute -right-10 -top-12 h-48 w-48 rounded-full bg-blue-100 blur-3xl"></div>
                <div class="relative flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-blue-600">Studio siswa</p>
                        <h1 class="font-display mt-3 max-w-3xl text-4xl font-bold leading-[0.98] tracking-tight text-zinc-950 md:text-6xl" style="overflow-wrap:anywhere;">
                            Halo, {{ $firstName }}. Rapikan karya terbaikmu.
                        </h1>
                        <p class="mt-4 max-w-2xl text-sm leading-6 text-zinc-600 md:text-base">
                            Upload project, lengkapi sertifikat, lalu tunggu validasi admin sebelum tampil di profil publik.
                        </p>
                    </div>
                    <div class="flex shrink-0 flex-wrap gap-2">
                        <a href="{{ route('siswa.portfolios.create') }}" class="inline-flex items-center justify-center gap-2 rounded-full bg-zinc-950 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-zinc-800">
                            <x-icon name="plus" class="h-4 w-4" /> Project
                        </a>
                        <a href="{{ route('siswa.certificates.index') }}" class="inline-flex items-center justify-center gap-2 rounded-full border border-zinc-300 bg-white px-4 py-2.5 text-sm font-medium text-zinc-800 transition hover:bg-zinc-50">
                            <x-icon name="award" class="h-4 w-4" /> Sertifikat
                        </a>
                        <a href="{{ route('students.cv', $user) }}" target="_blank" class="inline-flex items-center justify-center gap-2 rounded-full border border-zinc-300 bg-white px-4 py-2.5 text-sm font-medium text-zinc-800 transition hover:bg-zinc-50">
                            <x-icon name="download" class="h-4 w-4" /> Print CV
                        </a>
                    </div>
                </div>
            </div>

            <aside class="rounded-[2rem] border border-zinc-200 bg-zinc-950 p-5 text-white shadow-sm">
                <div class="flex items-center gap-4">
                    <img src="{{ $user->avatarUrl() }}" alt="" class="h-16 w-16 rounded-2xl object-cover ring-1 ring-white/20">
                    <div class="min-w-0">
                        <p class="truncate font-semibold text-white">{{ $user->name }}</p>
                        <p class="truncate text-sm text-zinc-400">{{ $user->school_class ?: 'Lengkapi kelas' }}</p>
                    </div>
                </div>
                <div class="mt-5 rounded-2xl border border-white/10 bg-white/10 p-4">
                    <p class="text-xs uppercase tracking-[0.22em] text-zinc-400">Status publik</p>
                    <p class="mt-2 text-2xl font-bold">{{ $pendingWork }} pending</p>
                    <p class="mt-1 text-sm text-zinc-400">Item menunggu admin.</p>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-2">
                    <a href="{{ route('portfolios.user', $user) }}" target="_blank" class="rounded-2xl bg-white px-3 py-3 text-center text-xs font-semibold text-zinc-950 transition hover:bg-blue-50">Profil publik</a>
                    <a href="{{ route('siswa.profile.edit') }}" class="rounded-2xl border border-white/15 px-3 py-3 text-center text-xs font-semibold text-white transition hover:bg-white/10">Edit profil</a>
                </div>
            </aside>
        </section>

        @if ($teamInvitations->count())
            <section class="overflow-hidden rounded-2xl border border-blue-200 bg-blue-50">
                <header class="flex items-center justify-between gap-3 border-b border-blue-200 px-5 py-4">
                    <div>
                        <h2 class="text-base font-bold text-blue-950">Undangan project tim</h2>
                        <p class="mt-1 text-sm text-blue-800">Konfirmasi apakah project berikut memang karya Anda.</p>
                    </div>
                    <x-badge variant="info">{{ $teamInvitations->count() }} menunggu</x-badge>
                </header>
                <div class="divide-y divide-blue-200">
                    @foreach ($teamInvitations as $invitation)
                        <article class="flex flex-col gap-4 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <p class="font-semibold text-zinc-950">{{ $invitation->title }}</p>
                                <p class="mt-1 text-sm text-zinc-600">Diundang oleh {{ $invitation->user->name }} · {{ $invitation->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex shrink-0 gap-2">
                                <form method="POST" action="{{ route('siswa.team-invitations.accept', $invitation) }}">
                                    @csrf @method('PATCH')
                                    <button class="rounded-lg bg-blue-700 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-800">Ya, karya saya</button>
                                </form>
                                <form method="POST" action="{{ route('siswa.team-invitations.reject', $invitation) }}">
                                    @csrf @method('PATCH')
                                    <button class="rounded-lg border border-blue-300 bg-white px-4 py-2 text-xs font-semibold text-zinc-700 hover:bg-blue-100">Bukan karya saya</button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="grid gap-4 md:grid-cols-3">
            <div class="rounded-3xl border border-zinc-200 bg-white p-5">
                <div class="flex items-center justify-between">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-50 text-blue-700"><x-icon name="briefcase" class="h-5 w-5" /></span>
                    <span class="text-xs font-medium text-zinc-400">Total</span>
                </div>
                <p class="mt-5 text-4xl font-bold tracking-tight text-zinc-900">{{ $portfolioTotal }}</p>
                <p class="mt-1 text-sm text-zinc-500">Portfolio tersimpan</p>
            </div>
            <div class="rounded-3xl border border-zinc-200 bg-white p-5">
                <div class="flex items-center justify-between">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-50 text-amber-700"><x-icon name="award" class="h-5 w-5" /></span>
                    <span class="text-xs font-medium text-zinc-400">Bukti</span>
                </div>
                <p class="mt-5 text-4xl font-bold tracking-tight text-zinc-900">{{ $certificateTotal }}</p>
                <p class="mt-1 text-sm text-zinc-500">Sertifikat tersimpan</p>
            </div>
            <div class="rounded-3xl border border-zinc-200 bg-white p-5">
                <div class="flex items-center justify-between">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700"><x-icon name="sparkles" class="h-5 w-5" /></span>
                    <span class="text-xs font-medium text-zinc-400">Pilihan</span>
                </div>
                <p class="mt-5 text-4xl font-bold tracking-tight text-zinc-900">{{ $featuredTotal }}</p>
                <p class="mt-1 text-sm text-zinc-500">Karya unggulan</p>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1.35fr)_minmax(18rem,0.85fr)]">
            <section class="overflow-hidden rounded-[1.75rem] border border-zinc-200 bg-white">
                <header class="flex flex-wrap items-center justify-between gap-3 border-b border-zinc-100 px-5 py-4">
                    <div>
                        <h2 class="font-display text-xl font-bold tracking-tight text-zinc-900">Portfolio terakhir</h2>
                        <p class="text-sm text-zinc-500">Draft, pending, approved, semua terlihat di sini.</p>
                    </div>
                    <a href="{{ route('siswa.portfolios.index') }}" class="rounded-full border border-zinc-300 px-3 py-1.5 text-xs font-medium text-zinc-700 transition hover:bg-zinc-50">Kelola semua</a>
                </header>
                <div class="divide-y divide-zinc-100">
                    @forelse ($portfolios as $p)
                        @php $isOwner = $p->user_id === $user->id; @endphp
                        <article class="grid gap-3 px-5 py-4 transition hover:bg-zinc-50 sm:grid-cols-[4.5rem_minmax(0,1fr)_auto] sm:items-center">
                            <img src="{{ $p->coverUrl() }}" alt="" class="h-16 w-full rounded-2xl object-cover sm:w-18">
                            <div class="min-w-0">
                                @if ($isOwner)
                                    <a href="{{ route('siswa.portfolios.edit', $p) }}" class="block truncate font-semibold text-zinc-900 hover:text-blue-600">{{ $p->title }}</a>
                                @else
                                    <p class="truncate font-semibold text-zinc-900">{{ $p->title }}</p>
                                @endif
                                <p class="mt-1 truncate text-sm text-zinc-500">{{ $p->category ?? 'Tanpa kategori' }} · {{ $p->created_at->diffForHumans() }}</p>
                                @if ($p->approval_status === 'rejected' && $p->rejection_note)
                                    <p class="mt-2 line-clamp-2 text-xs leading-5 text-red-700">Ditolak: {{ $p->rejection_note }}</p>
                                @endif
                            </div>
                            <div class="flex flex-wrap items-center gap-2 sm:justify-end">
                                <x-badge :variant="$p->approval_status === 'approved' ? 'success' : ($p->approval_status === 'rejected' ? 'danger' : 'warning')">{{ ucfirst($p->approval_status) }}</x-badge>
                                @if ($p->is_featured)
                                    <x-badge variant="warning">Unggulan</x-badge>
                                @endif
                                @unless ($isOwner)
                                    <x-badge variant="info">Kontributor</x-badge>
                                @endunless
                                @if ($p->isApproved())
                                    <button type="button" data-share-url="{{ route('portfolios.show', $p) }}" data-share-title="{{ $p->title }}" data-share-text="Lihat project {{ $p->title }}" class="text-xs font-semibold text-blue-700 hover:underline">Bagikan</button>
                                @elseif ($isOwner)
                                    <button type="button" data-share-url="{{ \Illuminate\Support\Facades\URL::signedRoute('portfolios.preview', $p) }}" data-share-title="{{ $p->title }}" data-share-text="Lihat preview project {{ $p->title }}" class="text-xs font-semibold text-blue-700 hover:underline">Bagikan preview</button>
                                @endif
                                <span class="inline-flex items-center gap-1 text-xs text-zinc-400"><x-icon name="eye" class="h-3 w-3" /> {{ $p->views ?: 0 }}</span>
                            </div>
                        </article>
                    @empty
                        <div class="px-5 py-12 text-center">
                            <p class="text-sm text-zinc-400">Belum ada portofolio.</p>
                            <a href="{{ route('siswa.portfolios.create') }}" class="mt-3 inline-flex rounded-full bg-zinc-900 px-4 py-2 text-xs font-medium text-white">Buat pertama</a>
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="overflow-hidden rounded-[1.75rem] border border-zinc-200 bg-white">
                <header class="flex items-center justify-between border-b border-zinc-100 px-5 py-4">
                    <div>
                        <h2 class="font-display text-xl font-bold tracking-tight text-zinc-900">Sertifikat</h2>
                        <p class="text-sm text-zinc-500">{{ $certificateTotal }} tersimpan.</p>
                    </div>
                    <a href="{{ route('siswa.certificates.index') }}" class="rounded-full bg-zinc-900 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-zinc-800">Kelola</a>
                </header>
                <div class="divide-y divide-zinc-100">
                    @forelse ($certificates as $c)
                        <article class="grid grid-cols-[2.75rem_minmax(0,1fr)] gap-3 px-5 py-4 transition hover:bg-zinc-50">
                            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-50 text-amber-700"><x-icon name="award" class="h-5 w-5" /></span>
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="truncate text-sm font-semibold text-zinc-900">{{ $c->title }}</p>
                                    <x-badge :variant="$c->approval_status === 'approved' ? 'success' : ($c->approval_status === 'rejected' ? 'danger' : 'warning')">{{ ucfirst($c->approval_status) }}</x-badge>
                                </div>
                                <p class="mt-1 truncate text-xs text-zinc-500">
                                    @if ($c->certificate_number)<span class="font-mono">{{ $c->certificate_number }}</span> · @endif
                                    {{ $c->issuer ?: 'Tanpa penerbit' }}
                                    @if ($c->issue_date) · {{ $c->issue_date->isoFormat('MMM Y') }} @endif
                                </p>
                            </div>
                        </article>
                    @empty
                        <div class="px-5 py-12 text-center">
                            <p class="text-sm text-zinc-400">Belum ada sertifikat.</p>
                            <a href="{{ route('siswa.certificates.index') }}" class="mt-3 inline-flex rounded-full border border-zinc-300 px-4 py-2 text-xs font-medium text-zinc-700">Tambah sekarang</a>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
@endsection
