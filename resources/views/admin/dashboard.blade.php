@extends('layouts.admin')
@section('title', 'Dashboard Admin')

@section('content')
    @php
        $approvalTotal = $stats['pending_users'] + $stats['pending_portfolios'] + $stats['pending_certificates'];
        $adminName = explode(' ', trim(auth()->user()->name))[0] ?: auth()->user()->name;
    @endphp

    <div class="space-y-6">
        <section class="relative overflow-hidden rounded-[2rem] border border-zinc-200 bg-zinc-950 p-6 text-white shadow-sm md:p-8">
            <div class="absolute right-0 top-0 h-48 w-48 rounded-full bg-blue-500/20 blur-3xl"></div>
            <div class="absolute bottom-0 left-1/3 h-32 w-32 rounded-full bg-amber-400/10 blur-2xl"></div>
            <div class="relative grid gap-6 lg:grid-cols-[1fr_18rem] lg:items-end">
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-blue-200">Ruang validasi sekolah</p>
                    <h1 class="font-display mt-3 max-w-3xl text-4xl font-bold leading-[0.95] tracking-tight text-white md:text-6xl" style="overflow-wrap:anywhere;">
                        {{ $approvalTotal }} hal menunggu keputusan.
                    </h1>
                    <p class="mt-4 max-w-2xl text-sm leading-6 text-zinc-300 md:text-base">
                        Halo {{ $adminName }}, prioritas hari ini: aktifkan akun siswa, validasi karya, lalu balas pesan perusahaan.
                    </p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.24em] text-zinc-300">Antrian utama</p>
                    <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                        <a href="{{ route('admin.users.index') }}?status=pending" class="rounded-2xl bg-white px-3 py-4 text-zinc-950 transition hover:-translate-y-0.5 hover:bg-blue-50">
                            <span class="block text-2xl font-bold">{{ $stats['pending_users'] }}</span>
                            <span class="mt-1 block text-[10px] font-semibold uppercase tracking-wider text-zinc-500">Akun</span>
                        </a>
                        <a href="{{ route('admin.portfolios.index') }}?status=pending" class="rounded-2xl bg-white px-3 py-4 text-zinc-950 transition hover:-translate-y-0.5 hover:bg-blue-50">
                            <span class="block text-2xl font-bold">{{ $stats['pending_portfolios'] }}</span>
                            <span class="mt-1 block text-[10px] font-semibold uppercase tracking-wider text-zinc-500">Karya</span>
                        </a>
                        <a href="{{ route('admin.certificates.index') }}?status=pending" class="rounded-2xl bg-white px-3 py-4 text-zinc-950 transition hover:-translate-y-0.5 hover:bg-blue-50">
                            <span class="block text-2xl font-bold">{{ $stats['pending_certificates'] }}</span>
                            <span class="mt-1 block text-[10px] font-semibold uppercase tracking-wider text-zinc-500">Sertif</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section class="flex flex-col gap-4 rounded-[1.75rem] border border-zinc-200 bg-white p-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-display text-lg font-bold text-zinc-900">Import akun sekolah</h2>
                <p class="mt-1 text-sm text-zinc-500">Tambahkan akun massal dari file XLSX Dapodik tanpa memasukkan data satu per satu.</p>
            </div>
            <div class="flex shrink-0 flex-wrap gap-2">
                <a href="{{ route('admin.users.import') }}" class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-semibold text-zinc-700 hover:bg-zinc-50">Import Siswa</a>
                <a href="{{ route('admin.users.import-teachers') }}" class="rounded-lg bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Import Guru</a>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <a href="{{ route('admin.users.index') }}?status=pending" class="group rounded-3xl border border-zinc-200 bg-white p-5 transition hover:-translate-y-0.5 hover:border-zinc-300 hover:shadow-lg">
                <div class="flex items-center justify-between">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-50 text-blue-700"><x-icon name="users" class="h-5 w-5" /></span>
                    <span class="text-xs font-medium text-zinc-400 group-hover:text-blue-600">Tinjau</span>
                </div>
                <p class="mt-5 text-4xl font-bold tracking-tight text-zinc-900">{{ $stats['pending_users'] }}</p>
                <p class="mt-1 text-sm text-zinc-500">Akun siswa pending</p>
            </a>
            <a href="{{ route('admin.portfolios.index') }}?status=pending" class="group rounded-3xl border border-zinc-200 bg-white p-5 transition hover:-translate-y-0.5 hover:border-zinc-300 hover:shadow-lg">
                <div class="flex items-center justify-between">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-50 text-amber-700"><x-icon name="briefcase" class="h-5 w-5" /></span>
                    <span class="text-xs font-medium text-zinc-400 group-hover:text-blue-600">Review</span>
                </div>
                <p class="mt-5 text-4xl font-bold tracking-tight text-zinc-900">{{ $stats['pending_portfolios'] }}</p>
                <p class="mt-1 text-sm text-zinc-500">Portfolio menunggu validasi</p>
            </a>
            <a href="{{ route('admin.certificates.index') }}?status=pending" class="group rounded-3xl border border-zinc-200 bg-white p-5 transition hover:-translate-y-0.5 hover:border-zinc-300 hover:shadow-lg">
                <div class="flex items-center justify-between">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700"><x-icon name="award" class="h-5 w-5" /></span>
                    <span class="text-xs font-medium text-zinc-400 group-hover:text-blue-600">Review</span>
                </div>
                <p class="mt-5 text-4xl font-bold tracking-tight text-zinc-900">{{ $stats['pending_certificates'] }}</p>
                <p class="mt-1 text-sm text-zinc-500">Sertifikat menunggu validasi</p>
            </a>
            <div class="rounded-3xl border border-zinc-200 bg-white p-5">
                <div class="flex items-center justify-between">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-zinc-100 text-zinc-700"><x-icon name="dashboard" class="h-5 w-5" /></span>
                    <span class="text-xs font-medium text-zinc-400">Publik</span>
                </div>
                <p class="mt-5 text-4xl font-bold tracking-tight text-zinc-900">{{ $stats['portfolios'] }}</p>
                <p class="mt-1 text-sm text-zinc-500">Portfolio sudah tayang</p>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1.4fr)_minmax(18rem,0.9fr)]">
            <section class="overflow-hidden rounded-[1.75rem] border border-zinc-200 bg-white">
                <header class="flex flex-wrap items-center justify-between gap-3 border-b border-zinc-100 px-5 py-4">
                    <div>
                        <h2 class="font-display text-xl font-bold tracking-tight text-zinc-900">Akun pending terbaru</h2>
                        <p class="text-sm text-zinc-500">Aktivasi siswa sebelum mereka publish karya.</p>
                    </div>
                    <a href="{{ route('admin.users.index') }}?status=pending" class="rounded-full border border-zinc-300 px-3 py-1.5 text-xs font-medium text-zinc-700 transition hover:bg-zinc-50">Lihat semua</a>
                </header>
                <div class="divide-y divide-zinc-100">
                    @forelse ($pendingUsers as $u)
                        <article class="grid gap-3 px-5 py-4 transition hover:bg-zinc-50 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-center">
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-zinc-900">{{ $u->name }}</p>
                                <p class="mt-1 truncate text-sm text-zinc-500">{{ $u->email }} @if($u->school_class) · {{ $u->school_class }} @endif</p>
                            </div>
                            <a href="{{ route('admin.users.index') }}?status=pending" class="inline-flex justify-center rounded-full bg-zinc-900 px-4 py-2 text-xs font-medium text-white transition hover:bg-zinc-800">Tinjau akun</a>
                        </article>
                    @empty
                        <div class="px-5 py-12 text-center text-sm text-zinc-400">Tidak ada akun pending.</div>
                    @endforelse
                </div>
            </section>

            <section class="overflow-hidden rounded-[1.75rem] border border-zinc-200 bg-white">
                <header class="border-b border-zinc-100 px-5 py-4">
                    <h2 class="font-display text-xl font-bold tracking-tight text-zinc-900">Karya paling dilihat</h2>
                    <p class="text-sm text-zinc-500">Hanya portfolio approved.</p>
                </header>
                <div class="divide-y divide-zinc-100">
                    @forelse ($topPortfolios as $p)
                        <a href="{{ route('portfolios.show', $p) }}" target="_blank" class="grid grid-cols-[4rem_minmax(0,1fr)_auto] items-center gap-3 px-5 py-4 transition hover:bg-zinc-50">
                            <img src="{{ $p->coverUrl() }}" alt="" class="h-12 w-16 rounded-xl object-cover">
                            <span class="min-w-0">
                                <span class="block truncate text-sm font-semibold text-zinc-900">{{ $p->title }}</span>
                                <span class="block truncate text-xs text-zinc-500">{{ $p->user->name }}</span>
                            </span>
                            <span class="inline-flex items-center gap-1 rounded-full bg-zinc-100 px-2 py-1 text-xs font-medium text-zinc-700"><x-icon name="eye" class="h-3 w-3" /> {{ $p->views }}</span>
                        </a>
                    @empty
                        <div class="px-5 py-12 text-center text-sm text-zinc-400">Belum ada views.</div>
                    @endforelse
                </div>
            </section>
        </div>

        <section class="overflow-hidden rounded-[1.75rem] border border-zinc-200 bg-white">
            <header class="flex flex-wrap items-center justify-between gap-3 border-b border-zinc-100 px-5 py-4">
                <div>
                    <h2 class="font-display text-xl font-bold tracking-tight text-zinc-900">Pesan perusahaan</h2>
                    <p class="text-sm text-zinc-500">Percakapan masuk untuk peluang kerja sama.</p>
                </div>
                <a href="{{ route('admin.contacts.index') }}" class="rounded-full border border-zinc-300 px-3 py-1.5 text-xs font-medium text-zinc-700 transition hover:bg-zinc-50">Buka inbox</a>
            </header>
            <div class="divide-y divide-zinc-100">
                @forelse ($latestMessages as $m)
                    <a href="{{ route('admin.contacts.show', $m) }}" class="grid gap-2 px-5 py-4 transition hover:bg-zinc-50 md:grid-cols-[minmax(0,1fr)_14rem] md:items-center">
                        <span class="min-w-0">
                            <span class="flex min-w-0 items-center gap-2">
                                <span class="truncate font-semibold text-zinc-900">{{ $m->subject }}</span>
                                @if (! $m->is_read)
                                    <span class="shrink-0 rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-red-700">Baru</span>
                                @endif
                            </span>
                            <span class="mt-1 block truncate text-sm text-zinc-500">{{ $m->sender_name }}@if($m->sender_company) · {{ $m->sender_company }}@endif</span>
                        </span>
                        <span class="text-sm text-zinc-400 md:text-right">{{ $m->created_at->diffForHumans() }}</span>
                    </a>
                @empty
                    <div class="px-5 py-12 text-center text-sm text-zinc-400">Belum ada pesan masuk.</div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
