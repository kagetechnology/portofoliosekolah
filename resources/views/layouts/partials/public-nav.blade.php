@php $school = \App\Models\School::current(); @endphp

<header data-public-nav class="sticky top-0 z-40 border-b border-zinc-200 bg-white transition duration-200">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 md:px-8 md:py-4">
        <a href="{{ route('home') }}" class="group flex items-center gap-2.5">
            @if ($school && $school->logo)
                <img src="{{ asset('storage/'.$school->logo) }}" alt="{{ $school->name }}" class="h-9 w-9 rounded-lg object-cover ring-1 ring-zinc-200">
            @else
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-zinc-900 text-white">
                    <x-icon name="graduation" class="h-5 w-5" />
                </span>
            @endif
            <div class="leading-tight">
                <p class="font-display text-base font-semibold text-zinc-900 group-hover:text-blue-600 transition">{{ $school?->name ?? 'Portofolio Sekolah' }}</p>
                <p class="text-[10px] uppercase tracking-widest text-zinc-500">Student Showcase</p>
            </div>
        </a>

        <nav class="hidden items-center gap-1 md:flex">
            <a href="{{ route('home') }}"
               class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100 {{ request()->routeIs('home') ? 'bg-zinc-100 text-zinc-900' : '' }}">Beranda</a>
            <a href="{{ route('students.index') }}"
               class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100 {{ request()->routeIs('students.*') ? 'bg-zinc-100 text-zinc-900' : '' }}">Siswa</a>
            <a href="{{ route('portfolios.index') }}"
               class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100 {{ request()->routeIs('portfolios.*') ? 'bg-zinc-100 text-zinc-900' : '' }}">Portofolio</a>
            <a href="{{ route('for-companies.index') }}"
               class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100 {{ request()->routeIs('for-companies.*') ? 'bg-zinc-100 text-zinc-900' : '' }}">Untuk Perusahaan</a>
            <a href="{{ route('contact.create') }}"
               class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100 {{ request()->routeIs('contact.*') ? 'bg-zinc-100 text-zinc-900' : '' }}">Hubungi</a>
        </nav>

        <div class="hidden items-center gap-2 md:flex">
            @auth
                <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('siswa.dashboard') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100">
                    {{ auth()->user()->name }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-button type="submit" variant="primary" size="sm">
                        <x-icon name="logout" class="h-3 w-3" /> Logout
                    </x-button>
                </form>
            @else
                <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100">Login</a>
                <a href="{{ route('register') }}" class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-zinc-800 active:scale-95">
                    Daftar
                </a>
            @endauth
        </div>

        <button data-public-nav-toggle type="button" aria-label="Toggle menu"
                class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-zinc-700 hover:bg-zinc-100 md:hidden">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6h16.5M3.75 12h16.5M3.75 18h16.5"/>
            </svg>
        </button>
    </div>

    <div data-public-nav-menu class="hidden border-t border-zinc-200 bg-white md:hidden">
        <div class="flex items-center justify-between px-4 pt-3">
            <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500">Menu</p>
            <button data-public-nav-close type="button"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-zinc-500 hover:bg-zinc-100 hover:text-zinc-700"
                    aria-label="Tutup menu">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="flex flex-col gap-1 px-4 pb-4 pt-2">
            <a data-public-nav-link href="{{ route('home') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100">Beranda</a>
            <a data-public-nav-link href="{{ route('students.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100">Siswa</a>
            <a data-public-nav-link href="{{ route('portfolios.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100">Portofolio</a>
            <a data-public-nav-link href="{{ route('contact.create') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100">Hubungi</a>
            <a data-public-nav-link href="{{ route('for-companies.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100">Untuk Perusahaan</a>
            <div class="my-2 border-t border-zinc-100"></div>
            @auth
                <a data-public-nav-link href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('siswa.dashboard') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button class="w-full rounded-lg bg-zinc-900 px-3 py-2 text-left text-sm font-medium text-white">Logout</button>
                </form>
            @else
                <a data-public-nav-link href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100">Login</a>
                <a data-public-nav-link href="{{ route('register') }}" class="rounded-lg bg-zinc-900 px-3 py-2 text-sm font-medium text-white">Daftar</a>
            @endauth
        </div>
    </div>
</header>
