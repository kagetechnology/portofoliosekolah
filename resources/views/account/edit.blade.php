@extends($user->isSiswa() ? 'layouts.siswa' : 'layouts.admin')
@section('title', 'Pengaturan Akun')

@section('content')
    <header class="mb-6 rounded-[2rem] border border-zinc-200 bg-white p-6 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-blue-600">Keamanan</p>
        <h1 class="font-display mt-3 text-4xl font-bold tracking-tight text-zinc-950 md:text-5xl">Pengaturan Akun</h1>
        <p class="mt-2 max-w-2xl text-sm text-zinc-500">Kelola identitas login dan password akun.</p>
    </header>

    <form method="POST" action="{{ route('account.update') }}" class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_20rem]">
        @csrf @method('PUT')
        <div class="space-y-5">
            <section class="rounded-[1.75rem] border border-zinc-200 bg-white p-6 shadow-sm">
                <h2 class="font-display text-xl font-bold tracking-tight text-zinc-900">Identitas login</h2>
                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    @if ($user->isSiswa())
                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-zinc-800">NISN</label>
                            <input value="{{ $user->nisn ?: 'Belum diisi' }}" disabled class="w-full cursor-not-allowed rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-500">
                            <p class="text-xs text-zinc-500">NISN hanya dapat diperbarui admin.</p>
                        </div>
                    @endif
                    <div class="space-y-1.5 {{ ! $user->isSiswa() ? 'sm:col-span-2' : '' }}">
                        <label class="text-sm font-medium text-zinc-800">Email {{ ! $user->isSiswa() ? '*' : '' }}</label>
                        @if (! $user->isSiswa())
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                   class="w-full rounded-xl border border-zinc-200 px-3 py-2.5 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                            @error('email') <p class="text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                        @else
                            <input type="email" value="{{ $user->email }}" disabled
                                   class="w-full cursor-not-allowed rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-500">
                            <p class="text-xs text-zinc-500">Ubah email melalui halaman Edit Profil.</p>
                        @endif
                    </div>
                </div>
            </section>

            <section class="rounded-[1.75rem] border border-zinc-200 bg-white p-6 shadow-sm">
                <h2 class="font-display text-xl font-bold tracking-tight text-zinc-900">Ganti password</h2>
                <p class="mt-1 text-sm text-zinc-500">Kosongkan password baru jika tidak ingin menggantinya.</p>
                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1.5 sm:col-span-2">
                        <label class="text-sm font-medium text-zinc-800">Password saat ini *</label>
                        <input type="password" name="current_password" required autocomplete="current-password"
                               class="w-full rounded-xl border border-zinc-200 px-3 py-2.5 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                        @error('current_password') <p class="text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-zinc-800">Password baru</label>
                        <input type="password" name="password" autocomplete="new-password"
                               class="w-full rounded-xl border border-zinc-200 px-3 py-2.5 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-zinc-800">Konfirmasi password</label>
                        <input type="password" name="password_confirmation" autocomplete="new-password"
                               class="w-full rounded-xl border border-zinc-200 px-3 py-2.5 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                    </div>
                </div>
            </section>
        </div>

        <aside class="space-y-4 lg:sticky lg:top-24 lg:self-start">
            @if ($user->must_change_password)
                <div class="rounded-[1.75rem] border border-amber-200 bg-amber-50 p-5 text-sm text-amber-900">
                    <p class="font-semibold">Password awal aktif</p>
                    <p class="mt-1 leading-5">Ganti password sebelum memakai akun secara penuh.</p>
                </div>
            @endif
            <div class="rounded-[1.75rem] border border-zinc-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-semibold text-zinc-900">Konfirmasi perubahan</p>
                <p class="mt-1 text-xs leading-5 text-zinc-500">Password saat ini wajib untuk mencegah perubahan oleh pihak lain.</p>
                <x-button type="submit" variant="primary" class="mt-4 w-full"><x-icon name="check" class="h-4 w-4" /> Simpan</x-button>
            </div>
        </aside>
    </form>
@endsection
