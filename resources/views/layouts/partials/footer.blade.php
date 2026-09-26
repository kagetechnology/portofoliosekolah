@php $school = \App\Models\School::current(); @endphp

<footer class="mt-20 border-t border-zinc-900/10 bg-zinc-900 text-zinc-300">
    <div class="border-b border-white/10">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 py-12 md:grid-cols-2 md:px-8">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-blue-400">Newsletter</p>
                <h3 class="font-display mt-2 text-2xl font-bold text-white md:text-3xl">Update karya siswa, langsung ke inbox Anda.</h3>
            </div>
            <form class="flex flex-col gap-3 sm:flex-row sm:self-end" onsubmit="event.preventDefault(); alert('Terima kasih!');">
                <input type="email" required placeholder="email@perusahaan.com" class="flex-1 rounded-lg border border-white/20 bg-white/5 px-4 py-2.5 text-sm text-white placeholder:text-zinc-500 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <button class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-blue-700 active:scale-95">Subscribe</button>
            </form>
        </div>
    </div>

    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 md:grid-cols-12 md:px-8">
        <div class="md:col-span-4">
            <div class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-white text-zinc-900">
                    <x-icon name="graduation" class="h-5 w-5" />
                </span>
                <p class="font-display text-lg font-semibold text-white">{{ $school?->name ?? config('app.name') }}</p>
            </div>
            @if ($school?->description)
                <div class="rich-content mt-3 max-w-md text-sm leading-relaxed text-zinc-400">{!! \App\Support\RichText::clean($school->description) !!}</div>
            @endif
        </div>

        <nav class="md:col-span-2">
            <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500">Eksplor</p>
            <ul class="mt-3 space-y-2 text-sm">
                <li><a href="{{ route('home') }}" class="hover:text-blue-400">Beranda</a></li>
                <li><a href="{{ route('students.index') }}" class="hover:text-blue-400">Siswa</a></li>
                <li><a href="{{ route('portfolios.index') }}" class="hover:text-blue-400">Portofolio</a></li>
                <li><a href="{{ route('for-companies.index') }}" class="hover:text-blue-400">Untuk Perusahaan</a></li>
                <li><a href="{{ route('contact.create') }}" class="hover:text-blue-400">Kontak</a></li>
            </ul>
        </nav>

        <nav class="md:col-span-3">
            <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500">Untuk Siswa</p>
            <ul class="mt-3 space-y-2 text-sm">
                <li><a href="{{ route('login') }}" class="hover:text-blue-400">Akun Siswa</a></li>
                <li><a href="{{ route('login') }}" class="hover:text-blue-400">Login</a></li>
            </ul>
        </nav>

        <div class="md:col-span-3">
            <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500">Kontak</p>
            <ul class="mt-3 space-y-2 text-sm">
                @if ($school?->address) <li class="flex items-start gap-2"><x-icon name="building" class="mt-0.5 h-4 w-4 text-zinc-500" /> <span class="text-zinc-300">{{ $school->address }}</span></li> @endif
                @if ($school?->phone) <li class="flex items-start gap-2"><x-icon name="phone" class="mt-0.5 h-4 w-4 text-zinc-500" /> <a href="tel:{{ $school->phone }}" class="hover:text-blue-400">{{ $school->phone }}</a></li> @endif
                @if ($school?->email) <li class="flex items-start gap-2"><x-icon name="mail" class="mt-0.5 h-4 w-4 text-zinc-500" /> <a href="mailto:{{ $school->email }}" class="hover:text-blue-400">{{ $school->email }}</a></li> @endif
            </ul>
        </div>
    </div>

    <div class="border-t border-white/10 bg-black/30">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-2 px-4 py-4 text-xs text-zinc-500 md:px-8">
            <p>&copy; {{ date('Y') }} {{ $school?->name ?? config('app.name') }}. All rights reserved.</p>
            <p>Student Showcase Platform · v{{ config('app.version') }}</p>
        </div>
    </div>
</footer>
