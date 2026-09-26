@extends('layouts.siswa')
@section('title', 'Sertifikat Saya')

@section('content')
    <header class="mb-6 flex flex-wrap items-end justify-between gap-4 rounded-[2rem] border border-zinc-200 bg-white p-6 shadow-sm">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-blue-600">Kredensial</p>
            <h1 class="font-display mt-3 text-4xl font-bold tracking-tight text-zinc-950 md:text-5xl">Sertifikat</h1>
            <p class="mt-1 text-sm text-zinc-500">Sertifikat baru tampil publik setelah disetujui admin.</p>
        </div>
        <a href="{{ route('portfolios.user', auth()->user()) }}" target="_blank"
           class="inline-flex items-center gap-2 rounded-full border border-zinc-300 bg-white px-4 py-2 text-sm font-medium text-zinc-800 transition hover:bg-zinc-50">
            <x-icon name="external" class="h-4 w-4" /> Lihat di Publik
        </a>
    </header>

    <div class="grid gap-6 lg:grid-cols-3">
        <section class="rounded-[1.75rem] border border-zinc-200 bg-white p-6 shadow-sm lg:col-span-1 lg:sticky lg:top-24 lg:self-start">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wider text-zinc-500">Tambah Sertifikat</h2>
            <form method="POST" action="{{ route('siswa.certificates.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-zinc-800">Judul <span class="text-red-500">*</span></label>
                    <input name="title" value="{{ old('title') }}" required placeholder="AWS Cloud Practitioner"
                           class="w-full rounded-lg border border-zinc-200 px-3 py-2.5 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10 @error('title') border-red-400 @enderror">
                    @error('title') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-zinc-800">No. Sertifikat</label>
                    <input name="certificate_number" value="{{ old('certificate_number') }}" placeholder="AWS-2026-00123"
                           class="w-full rounded-lg border border-zinc-200 px-3 py-2.5 text-sm font-mono focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10 @error('certificate_number') border-red-400 @enderror">
                    @error('certificate_number') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-zinc-800">Penerbit</label>
                    <input name="issuer" value="{{ old('issuer') }}" placeholder="Amazon Web Services"
                           class="w-full rounded-lg border border-zinc-200 px-3 py-2.5 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                </div>
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-zinc-800">Tanggal Terbit</label>
                    <input name="issue_date" type="date" value="{{ old('issue_date') }}"
                           class="w-full rounded-lg border border-zinc-200 px-3 py-2.5 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                </div>
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-zinc-800">File</label>
                    <input type="file" name="file" accept=".pdf,image/jpeg,image/png,image/webp"
                           class="w-full text-sm file:mr-3 file:rounded-md file:border-0 file:bg-zinc-900 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-zinc-800">
                    <p class="text-xs text-zinc-500">PDF / JPG / PNG / WebP hingga 10MB.</p>
                    @error('file') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <x-button type="submit" variant="primary" class="w-full">
                    <x-icon name="plus" class="h-4 w-4" /> Simpan Sertifikat
                </x-button>
            </form>
        </section>

        <section class="lg:col-span-2">
            @if ($certificates->count())
                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach ($certificates as $c)
                        <article class="overflow-hidden rounded-[1.75rem] border border-zinc-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:border-zinc-300 hover:shadow-lg">
                            <div class="block">
                                @if ($c->file && $c->isImage())
                                    <div class="aspect-[16/10] bg-zinc-100">
                                        <img src="{{ $c->fileUrl() }}" alt="{{ $c->title }}" class="h-full w-full object-cover">
                                    </div>
                                @else
                                    <div class="flex aspect-[16/10] items-center justify-center bg-amber-50">
                                        <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">
                                            <x-icon name="award" class="h-7 w-7" />
                                        </span>
                                    </div>
                                @endif
                                <div class="p-4">
                                    <h3 class="font-semibold text-zinc-900 line-clamp-2 hover:text-blue-600">{{ $c->title }}</h3>
                                    <div class="mt-2">
                                        <x-badge :variant="$c->approval_status === 'approved' ? 'success' : ($c->approval_status === 'rejected' ? 'danger' : 'warning')">{{ ucfirst($c->approval_status) }}</x-badge>
                                    </div>
                                    @if ($c->certificate_number)
                                        <p class="mt-1 font-mono text-xs text-zinc-600">{{ $c->certificate_number }}</p>
                                    @endif
                                    <p class="mt-1 text-xs text-zinc-500">
                                        {{ $c->issuer ?: 'Tanpa penerbit' }}
                                        @if ($c->issue_date)
                                            · {{ $c->issue_date->isoFormat('MMM Y') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between border-t border-zinc-100 px-4 py-3">
                                @if ($c->isApproved())
                                    <a href="{{ route('certificates.show', $c) }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 hover:underline">
                                        <x-icon name="eye" class="h-3.5 w-3.5" /> Detail
                                    </a>
                                @else
                                    <span class="text-xs text-zinc-400">Belum publik</span>
                                @endif
                                <form method="POST" action="{{ route('siswa.certificates.destroy', $c) }}" onsubmit="return confirm('Hapus sertifikat ini?')">
                                    @csrf @method('DELETE')
                                    <button class="inline-flex items-center gap-1 rounded-md border border-red-200 px-2.5 py-1 text-xs font-medium text-red-600 hover:bg-red-50">
                                        <x-icon name="trash" class="h-3.5 w-3.5" /> Hapus
                                    </button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="mt-6">{{ $certificates->links() }}</div>
            @else
                <x-empty-state name="award" title="Belum ada sertifikat"
                    description="Tambahkan sertifikat lewat form di samping. Sertifikat tampil publik setelah disetujui admin." />
            @endif
        </section>
    </div>
@endsection
