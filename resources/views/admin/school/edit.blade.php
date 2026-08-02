@extends('layouts.admin')
@section('title', 'Profil Sekolah')

@section('content')
    <header class="mb-6 flex items-end justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">Admin</p>
            <h1 class="font-display text-3xl font-bold tracking-tight text-zinc-900">Profil Sekolah</h1>
            <p class="mt-1 text-sm text-zinc-500">Informasi ini tampil di halaman publik (landing).</p>
        </div>
        @php $school = \App\Models\School::current(); @endphp
        @if ($school && $school->logo)
            <img src="{{ asset('storage/'.$school->logo) }}" class="h-14 w-14 rounded-xl border object-cover">
        @endif
    </header>

    <form method="POST" action="{{ route('admin.school.update') }}" enctype="multipart/form-data" class="space-y-5 rounded-2xl border border-zinc-200 bg-white p-6 md:p-8">
        @csrf @method('PUT')

        <section class="space-y-4">
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500">Informasi Utama</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-1.5 md:col-span-2">
                    <label class="block text-sm font-medium text-zinc-800">Nama Sekolah <span class="text-red-500">*</span></label>
                    <input name="name" value="{{ old('name', $school->name ?? '') }}" required
                           class="w-full rounded-lg border border-zinc-200 px-3 py-2.5 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                </div>
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-zinc-800">NPSN</label>
                    <input name="npsn" value="{{ old('npsn', $school->npsn ?? '') }}" class="w-full rounded-lg border border-zinc-200 px-3 py-2.5 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                </div>
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-zinc-800">Kepala Sekolah</label>
                    <input name="kepala_sekolah" value="{{ old('kepala_sekolah', $school->kepala_sekolah ?? '') }}" class="w-full rounded-lg border border-zinc-200 px-3 py-2.5 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                </div>
            </div>
        </section>

        <section class="space-y-4 border-t border-zinc-100 pt-5">
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500">Kontak</h2>
            <div class="grid gap-4 md:grid-cols-3">
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-zinc-800">Telepon</label>
                    <input name="phone" value="{{ old('phone', $school->phone ?? '') }}" class="w-full rounded-lg border border-zinc-200 px-3 py-2.5 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                </div>
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-zinc-800">Email</label>
                    <input type="email" name="email" value="{{ old('email', $school->email ?? '') }}" class="w-full rounded-lg border border-zinc-200 px-3 py-2.5 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                </div>
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-zinc-800">Website</label>
                    <input name="website" value="{{ old('website', $school->website ?? '') }}" class="w-full rounded-lg border border-zinc-200 px-3 py-2.5 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                </div>
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-zinc-800">Alamat <span class="text-red-500">*</span></label>
                <textarea name="address" required rows="2" class="w-full rounded-lg border border-zinc-200 px-3 py-2.5 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">{{ old('address', $school->address ?? '') }}</textarea>
            </div>
        </section>

        <section class="space-y-4 border-t border-zinc-100 pt-5">
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500">Tentang</h2>
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-zinc-800">Deskripsi Singkat</label>
                <x-rich-editor name="description" :value="old('description', $school->description ?? '')" rows="5" />
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-zinc-800">Visi</label>
                    <x-rich-editor name="vision" :value="old('vision', $school->vision ?? '')" rows="4" />
                </div>
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-zinc-800">Misi</label>
                    <x-rich-editor name="mission" :value="old('mission', $school->mission ?? '')" rows="4" />
                </div>
            </div>
        </section>

        <section class="space-y-4 border-t border-zinc-100 pt-5">
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500">Logo</h2>
            <input type="file" name="logo" accept="image/*" class="text-sm file:mr-3 file:rounded-md file:border-0 file:bg-zinc-900 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-zinc-800">
            <p class="text-xs text-zinc-500">Format JPG, PNG atau WebP. Maks 2MB.</p>
        </section>

        <div class="flex items-center justify-end gap-3 border-t border-zinc-100 pt-5">
            <x-button href="{{ route('home') }}" target="_blank" variant="ghost">Preview Publik</x-button>
            <x-button type="submit" variant="primary">Simpan Perubahan</x-button>
        </div>
    </form>
@endsection
