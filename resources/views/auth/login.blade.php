@extends('layouts.base')
@section('title', 'Masuk')

@section('body')
<main id="main" class="min-h-dvh md:grid md:grid-cols-2">
    {{-- Brand panel --}}
    <section class="relative hidden flex-col justify-between overflow-hidden bg-zinc-900 p-12 text-white md:flex">
        <div class="absolute inset-0 grain opacity-40"></div>
        <div class="absolute -right-32 top-1/2 h-96 w-96 -translate-y-1/2 rounded-full bg-blue-600/30 blur-3xl"></div>

        <div class="relative">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-white/10 backdrop-blur">
                    <x-icon name="graduation" class="h-6 w-6" />
                </span>
                <span class="font-display text-lg font-semibold">Portofolio Sekolah</span>
            </a>
        </div>

        <div class="relative">
            <h1 class="font-display text-4xl font-bold leading-tight tracking-tight md:text-5xl text-balance">
                Showcase karya siswa untuk dunia.
            </h1>
            <p class="mt-4 max-w-md text-zinc-300">Platform resmi sekolah untuk menampilkan portofolio, skill, dan sertifikat siswa. Diperbarui langsung oleh siswa dan divalidasi admin.</p>

            <div class="mt-10 grid gap-6">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-white/10 backdrop-blur">
                        <x-icon name="briefcase" class="h-5 w-5" />
                    </span>
                    <div>
                        <p class="font-semibold">Kelola portofolio</p>
                        <p class="text-sm text-zinc-400">Upload project, tambahkan skill, lampirkan sertifikat.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-white/10 backdrop-blur">
                        <x-icon name="users" class="h-5 w-5" />
                    </span>
                    <div>
                        <p class="font-semibold">Validasi admin</p>
                        <p class="text-sm text-zinc-400">Setiap akun siswa disetujui oleh admin sekolah.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative text-xs text-zinc-500">&copy; {{ date('Y') }} {{ \App\Models\School::current()?->name ?? 'Portofolio Sekolah' }}</div>
    </section>

    {{-- Form --}}
    <section class="flex flex-col justify-center bg-white px-6 py-10 md:px-12">
        <div class="mx-auto w-full max-w-sm">
            <header class="mb-8">
                <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">Selamat datang</p>
                <h2 class="font-display mt-1 text-3xl font-bold tracking-tight text-zinc-900">Masuk akun</h2>
                <p class="mt-1 text-sm text-zinc-600">Masukkan kredensial untuk melanjutkan.</p>
            </header>

            @if ($errors->any())
                <x-alert variant="error" class="mb-5">
                    @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
                </x-alert>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-zinc-800">NISN / Email Admin</label>
                    <input type="text" name="login" value="{{ old('login') }}" required autofocus autocomplete="username"
                           placeholder="NISN siswa atau email admin"
                           class="w-full rounded-lg border border-zinc-200 px-3 py-2.5 text-sm transition focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                </div>
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-zinc-800">Password</label>
                    <input type="password" name="password" required
                           class="w-full rounded-lg border border-zinc-200 px-3 py-2.5 text-sm transition focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                </div>
                <label class="flex items-center gap-2 text-sm text-zinc-700">
                    <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900">
                    Ingat saya
                </label>
                <x-button type="submit" variant="primary" size="lg" class="w-full">Masuk</x-button>
            </form>

            <div class="my-6 flex items-center gap-3 text-xs text-zinc-400">
                <span class="h-px flex-1 bg-zinc-200"></span>
                <span>ATAU</span>
                <span class="h-px flex-1 bg-zinc-200"></span>
            </div>

            <p class="text-center text-sm text-zinc-600">Akun siswa disediakan oleh admin sekolah.</p>

            <p class="mt-8 text-center text-xs text-zinc-400 md:hidden">
                <a href="{{ route('home') }}" class="hover:text-zinc-700">&larr; Kembali ke beranda</a>
            </p>
        </div>
    </section>
</main>
@endsection
