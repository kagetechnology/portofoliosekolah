@extends('layouts.base')
@section('title', 'Daftar Akun Siswa')

@section('body')
<main id="main" class="min-h-dvh md:grid md:grid-cols-2">
    <section class="relative hidden flex-col justify-between overflow-hidden bg-zinc-900 p-12 text-white md:flex">
        <div class="absolute inset-0 grain opacity-40"></div>
        <div class="absolute -left-32 bottom-0 h-96 w-96 rounded-full bg-blue-600/20 blur-3xl"></div>

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
                Tunjukkan karya terbaikmu.
            </h1>
            <p class="mt-4 max-w-md text-zinc-300">Buat akun siswa gratis, tambahkan portofolio, dan biarkan perusahaan menemukanmu lewat showcase resmi sekolah.</p>

            <div class="mt-10 space-y-4 text-sm text-zinc-300">
                <div class="flex items-center gap-3">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-white/10">
                        <x-icon name="check" class="h-4 w-4" />
                    </span>
                    Upload project dengan cover, deskripsi, dan tech stack
                </div>
                <div class="flex items-center gap-3">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-white/10">
                        <x-icon name="check" class="h-4 w-4" />
                    </span>
                    Tautkan GitHub dan demo URL publik
                </div>
                <div class="flex items-center gap-3">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-white/10">
                        <x-icon name="check" class="h-4 w-4" />
                    </span>
                    Akun diverifikasi oleh admin sebelum aktif
                </div>
            </div>
        </div>

        <div class="relative text-xs text-zinc-500">&copy; {{ date('Y') }} {{ \App\Models\School::current()?->name ?? 'Portofolio Sekolah' }}</div>
    </section>

    <section class="flex flex-col justify-center bg-white px-6 py-10 md:px-12">
        <div class="mx-auto w-full max-w-md">
            <header class="mb-8">
                <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">Buat akun</p>
                <h2 class="font-display mt-1 text-3xl font-bold tracking-tight text-zinc-900">Daftar sebagai siswa</h2>
                <p class="mt-1 text-sm text-zinc-600">Akun akan diaktifkan setelah disetujui admin sekolah.</p>
            </header>

            @if ($errors->any())
                <x-alert variant="error" class="mb-5">
                    @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
                </x-alert>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1.5 sm:col-span-2">
                        <label class="block text-sm font-medium text-zinc-800">Nama Lengkap</label>
                        <input name="name" value="{{ old('name') }}" required autofocus
                               class="w-full rounded-lg border border-zinc-200 px-3 py-2.5 text-sm transition focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-zinc-800">Kelas</label>
                        <input name="school_class" value="{{ old('school_class') }}" placeholder="XII RPL 1"
                               class="w-full rounded-lg border border-zinc-200 px-3 py-2.5 text-sm transition focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-zinc-800">No. WhatsApp (opsional)</label>
                        <input name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx"
                               class="w-full rounded-lg border border-zinc-200 px-3 py-2.5 text-sm transition focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                    </div>
                    <div class="space-y-1.5 sm:col-span-2">
                        <label class="block text-sm font-medium text-zinc-800">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full rounded-lg border border-zinc-200 px-3 py-2.5 text-sm transition focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-zinc-800">Password</label>
                        <input type="password" name="password" required
                               class="w-full rounded-lg border border-zinc-200 px-3 py-2.5 text-sm transition focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-zinc-800">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full rounded-lg border border-zinc-200 px-3 py-2.5 text-sm transition focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                    </div>
                </div>

                <x-button type="submit" variant="primary" size="lg" class="w-full">Daftar Sekarang</x-button>
            </form>

            <p class="mt-6 text-center text-sm text-zinc-600">
                Sudah punya akun? <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:underline">Login</a>
            </p>
        </div>
    </section>
</main>
@endsection
