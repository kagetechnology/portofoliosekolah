@extends('layouts.siswa')
@section('title', 'Edit Portofolio')

@section('content')
    <header class="mb-6">
        <a href="{{ route('siswa.portfolios.index') }}" class="inline-flex items-center gap-2 text-sm text-zinc-600 hover:text-blue-600">
            <x-icon name="arrow-left" class="h-4 w-4" /> Kembali ke daftar
        </a>
        <h1 class="font-display mt-3 text-3xl font-bold tracking-tight text-zinc-900">Edit Portofolio</h1>
    </header>
    @include('siswa.portfolios._form')
@endsection
