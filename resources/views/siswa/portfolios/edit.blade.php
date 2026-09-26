@extends('layouts.siswa')
@section('title', 'Edit Portofolio')

@section('content')
    <header class="mb-6 rounded-[2rem] border border-zinc-200 bg-white p-6 shadow-sm">
        <a href="{{ route('siswa.portfolios.index') }}" class="inline-flex items-center gap-2 text-sm text-zinc-600 hover:text-blue-600">
            <x-icon name="arrow-left" class="h-4 w-4" /> Kembali ke daftar
        </a>
        <h1 class="font-display mt-3 text-4xl font-bold tracking-tight text-zinc-950 md:text-5xl">Edit Portofolio</h1>
        <p class="mt-2 text-sm text-zinc-500">Perubahan akan masuk antrian review lagi.</p>
    </header>
    @if ($portfolio->approval_status === 'rejected' && $portfolio->rejection_note)
        <x-alert variant="error" class="mb-6">
            <p class="font-semibold">Portofolio ditolak dan perlu diperbaiki</p>
            <p class="mt-1 whitespace-pre-line">{{ $portfolio->rejection_note }}</p>
            <p class="mt-2 text-xs">Setelah disimpan, portofolio otomatis dikirim kembali untuk direview.</p>
        </x-alert>
    @endif
    @include('siswa.portfolios._form')
@endsection
