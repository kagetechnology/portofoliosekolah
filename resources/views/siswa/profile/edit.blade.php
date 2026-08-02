@extends('layouts.siswa')
@section('title', 'Edit Profil')

@section('content')
    <header class="mb-6">
        <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">Akun</p>
        <h1 class="font-display text-3xl font-bold tracking-tight text-zinc-900">Edit Profil</h1>
        <p class="mt-1 text-sm text-zinc-500">
            Profil publik:
            <a href="{{ route('portfolios.user', $user) }}" target="_blank" class="text-blue-600 hover:underline">/siswa/{{ $user->slug }}</a>
        </p>
    </header>

    <form method="POST" action="{{ route('siswa.profile.update') }}" enctype="multipart/form-data"
          class="grid gap-5 lg:grid-cols-3">
        @csrf @method('PUT')

        <div class="space-y-5 lg:col-span-2">
            <section class="rounded-2xl border border-zinc-200 bg-white p-6">
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-wider text-zinc-500">Identitas</h2>
                <p class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-800">
                    Nama, kelas, dan no. telepon hanya bisa diubah oleh admin sekolah.
                </p>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1.5 sm:col-span-2">
                        <label class="block text-sm font-medium text-zinc-800">Nama Lengkap</label>
                        <input type="text" value="{{ $user->name }}" disabled
                               class="w-full cursor-not-allowed rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-500">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-zinc-800">Kelas</label>
                        <input type="text" value="{{ $user->school_class ?: '—' }}" disabled
                               class="w-full cursor-not-allowed rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-500">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-zinc-800">No. Telepon / WhatsApp</label>
                        <input type="text" value="{{ $user->phone ?: '—' }}" disabled
                               class="w-full cursor-not-allowed rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-500">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-zinc-800">Tahun Masuk</label>
                        <input type="text" value="{{ $user->tahun_masuk ?: '—' }}" disabled
                               class="w-full cursor-not-allowed rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-500">
                    </div>
                    <div class="space-y-1.5 sm:col-span-2">
                        <label class="block text-sm font-medium text-zinc-800">Email</label>
                        <input type="email" value="{{ $user->email }}" disabled
                               class="w-full cursor-not-allowed rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-500">
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-zinc-200 bg-white p-6">
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-wider text-zinc-500">Deskripsi</h2>
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-zinc-800">Bio / Deskripsi Diri</label>
                    <x-rich-editor name="bio" :value="old('bio', $user->bio)" rows="6" placeholder="Ceritakan keahlian, minat, dan proyek yang sedang dikerjakan..." />
                    @error('bio') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    <p class="text-xs text-zinc-500">Maksimal 1000 karakter. Tampil di profil publik.</p>
                </div>
            </section>

            <section class="rounded-2xl border border-zinc-200 bg-white p-6">
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-wider text-zinc-500">Tautan Sosial</h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1.5">
                        <label class="flex items-center gap-2 text-sm font-medium text-zinc-800">
                            <x-icon name="github" class="h-4 w-4" /> GitHub
                        </label>
                        <input name="github_url" type="url" value="{{ old('github_url', $user->github_url) }}"
                               placeholder="https://github.com/username"
                               class="w-full rounded-lg border border-zinc-200 px-3 py-2.5 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10 @error('github_url') border-red-400 @enderror">
                        @error('github_url') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="flex items-center gap-2 text-sm font-medium text-zinc-800">
                            <x-icon name="instagram" class="h-4 w-4" /> Instagram
                        </label>
                        <input name="instagram_url" type="url" value="{{ old('instagram_url', $user->instagram_url) }}"
                               placeholder="https://instagram.com/username"
                               class="w-full rounded-lg border border-zinc-200 px-3 py-2.5 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10 @error('instagram_url') border-red-400 @enderror">
                        @error('instagram_url') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>
        </div>

        <div class="space-y-5">
            <section class="rounded-2xl border border-zinc-200 bg-white p-6">
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-wider text-zinc-500">Foto Profil</h2>
                <div class="flex flex-col items-center text-center">
                    <div class="relative">
                        <img id="avatar-preview" src="{{ $user->avatarUrl() }}"
                              alt="Avatar"
                              class="h-32 w-32 rounded-2xl border border-zinc-200 object-cover shadow-sm">
                        <label class="absolute -bottom-2 -right-2 flex h-10 w-10 cursor-pointer items-center justify-center rounded-full bg-zinc-900 text-white shadow-md transition hover:bg-zinc-800">
                            <x-icon name="camera" class="h-4 w-4" />
                            <input type="file" name="avatar" accept="image/*" data-image-preview-input="#avatar-preview" class="sr-only">
                        </label>
                    </div>
                    <p class="mt-4 text-xs text-zinc-500">JPG/PNG/WebP, maks 2MB.</p>
                    @error('avatar') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

                    @if ($user->avatar)
                        <label class="mt-3 flex items-center gap-2 text-xs text-red-600">
                            <input type="checkbox" name="remove_avatar" value="1" class="rounded border-zinc-300">
                            Hapus foto saat ini
                        </label>
                    @endif
                </div>
            </section>

            <div class="flex flex-col gap-2">
                <x-button type="submit" variant="primary" size="lg">
                    <x-icon name="check" class="h-4 w-4" /> Simpan Profil
                </x-button>
                <a href="{{ route('portfolios.user', $user) }}" target="_blank"
                   class="rounded-lg border border-zinc-300 px-4 py-2.5 text-center text-sm font-medium text-zinc-700 hover:bg-zinc-50">
                    Lihat Profil Publik
                </a>
            </div>
        </div>
    </form>
@endsection
