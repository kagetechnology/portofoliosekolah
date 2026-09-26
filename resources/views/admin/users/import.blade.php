@extends('layouts.admin')
@section('title', 'Import Data Siswa')

@section('content')
    <header class="mb-6 rounded-[2rem] border border-zinc-200 bg-white p-6 shadow-sm">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-sm text-zinc-600 hover:text-blue-600"><x-icon name="arrow-left" class="h-4 w-4" /> Kembali</a>
        <p class="mt-5 text-xs font-semibold uppercase tracking-[0.28em] text-blue-600">Data sekolah</p>
        <h1 class="font-display mt-3 text-4xl font-bold tracking-tight text-zinc-950 md:text-5xl">Import Siswa</h1>
        <p class="mt-2 max-w-2xl text-sm text-zinc-500">Import file XLSX Dapodik. Sistem hanya membaca Nama, NISN, dan Rombel Saat Ini.</p>
    </header>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_22rem]">
        <form method="POST" action="{{ route('admin.users.import.store') }}" enctype="multipart/form-data" class="rounded-[1.75rem] border border-zinc-200 bg-white p-6 shadow-sm">
            @csrf
            <h2 class="font-display text-xl font-bold tracking-tight text-zinc-900">Pilih file</h2>
            <input type="file" name="file" required accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                   class="mt-5 block w-full rounded-xl border border-zinc-200 p-3 text-sm file:mr-3 file:rounded-full file:border-0 file:bg-zinc-950 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white">
            @error('file') <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p> @enderror
                <x-button type="submit" variant="primary" class="mt-5"><x-icon name="users" class="h-4 w-4" /> Import Siswa</x-button>
        </form>

        <aside class="rounded-[1.75rem] border border-amber-200 bg-amber-50 p-5 text-sm text-amber-950">
            <p class="font-semibold">Perlindungan data</p>
            <ul class="mt-3 space-y-2 leading-5">
                <li>NIK, KK, alamat, data orang tua, rekening, dan data sensitif lain diabaikan.</li>
                <li>Password awal: <code class="font-mono font-semibold">{{ $defaultPassword }}</code>.</li>
                <li>Email dibuat otomatis: <code class="font-mono">nisn@smkn1mas.sch.id</code>.</li>
                <li>Email dari file XLSX tidak dibaca.</li>
                <li>Siswa wajib mengganti password awal.</li>
                <li>Hasil import ditampilkan setelah proses selesai.</li>
            </ul>
        </aside>
    </div>
@endsection
