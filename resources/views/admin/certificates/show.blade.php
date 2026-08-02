@extends('layouts.admin')
@section('title', 'Review Sertifikat')

@section('content')
    <header class="mb-6 flex flex-wrap items-start justify-between gap-3">
        <div>
            <a href="{{ route('admin.certificates.index') }}" class="inline-flex items-center gap-2 text-sm text-zinc-600 hover:text-blue-600">
                <x-icon name="arrow-left" class="h-4 w-4" /> Kembali
            </a>
            <div class="mt-3 flex flex-wrap items-center gap-2">
                <h1 class="font-display text-3xl font-bold tracking-tight text-zinc-900">{{ $certificate->title }}</h1>
                <x-badge :variant="$certificate->approval_status === 'approved' ? 'success' : ($certificate->approval_status === 'rejected' ? 'danger' : 'warning')">{{ ucfirst($certificate->approval_status) }}</x-badge>
            </div>
            <p class="mt-1 text-sm text-zinc-500">Review sertifikat sebelum tampil publik.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if ($certificate->approval_status !== 'approved')
                <form method="POST" action="{{ route('admin.certificates.approve', $certificate) }}">
                    @csrf @method('PATCH')
                    <x-button type="submit" variant="primary"><x-icon name="check" class="h-4 w-4" /> Setujui</x-button>
                </form>
            @endif
            @if ($certificate->approval_status !== 'rejected')
                <form method="POST" action="{{ route('admin.certificates.reject', $certificate) }}">
                    @csrf @method('PATCH')
                    <button class="inline-flex items-center gap-2 rounded-lg border border-red-200 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50">Tolak</button>
                </form>
            @endif
        </div>
    </header>

    <div class="grid gap-6 lg:grid-cols-3">
        <article class="lg:col-span-2 overflow-hidden rounded-2xl border border-zinc-200 bg-white">
            @if ($certificate->file && $certificate->isImage())
                <div class="aspect-[16/10] bg-zinc-100">
                    <img src="{{ $certificate->fileUrl() }}" alt="" class="h-full w-full object-contain bg-zinc-50">
                </div>
            @elseif ($certificate->file)
                <div class="flex min-h-[28rem] flex-col items-center justify-center bg-amber-50 p-8 text-center">
                    <span class="flex h-20 w-20 items-center justify-center rounded-3xl bg-amber-100 text-amber-700">
                        <x-icon name="award" class="h-10 w-10" />
                    </span>
                    <p class="mt-4 text-sm text-zinc-600">Preview PDF tidak ditanam. Buka file untuk validasi.</p>
                    <a href="{{ $certificate->fileUrl() }}" target="_blank" rel="noopener" class="mt-4 inline-flex items-center gap-2 rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800">
                        <x-icon name="external" class="h-4 w-4" /> Buka File
                    </a>
                </div>
            @else
                <div class="flex min-h-[20rem] items-center justify-center bg-zinc-50 text-sm text-zinc-400">Tidak ada file.</div>
            @endif
        </article>

        <aside class="space-y-5 lg:sticky lg:top-24 lg:self-start">
            <section class="rounded-2xl border border-zinc-200 bg-white p-5">
                <h2 class="mb-3 text-sm font-semibold uppercase tracking-wider text-zinc-500">Siswa</h2>
                <div class="flex items-center gap-3">
                    <img src="{{ $certificate->user->avatarUrl() }}" alt="" class="h-12 w-12 rounded-xl object-cover ring-1 ring-zinc-200">
                    <div>
                        <p class="font-semibold text-zinc-900">{{ $certificate->user->name }}</p>
                        <p class="text-xs text-zinc-500">{{ $certificate->user->school_class ?: 'Tanpa kelas' }}</p>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-zinc-200 bg-white p-5">
                <h2 class="mb-3 text-sm font-semibold uppercase tracking-wider text-zinc-500">Detail Sertifikat</h2>
                <dl class="space-y-3 text-sm">
                    <div><dt class="text-xs text-zinc-500">Nomor</dt><dd class="font-mono font-medium text-zinc-900">{{ $certificate->certificate_number ?: '—' }}</dd></div>
                    <div><dt class="text-xs text-zinc-500">Penerbit</dt><dd class="font-medium text-zinc-900">{{ $certificate->issuer ?: '—' }}</dd></div>
                    <div><dt class="text-xs text-zinc-500">Tanggal Terbit</dt><dd class="font-medium text-zinc-900">{{ $certificate->issue_date?->isoFormat('D MMMM Y') ?: '—' }}</dd></div>
                    <div><dt class="text-xs text-zinc-500">Dikirim</dt><dd class="font-medium text-zinc-900">{{ $certificate->created_at->isoFormat('D MMMM Y HH:mm') }}</dd></div>
                </dl>
            </section>
        </aside>
    </div>
@endsection
