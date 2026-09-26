@extends('layouts.base')

@section('body')
<div data-sidebar-root class="flex min-h-dvh bg-[radial-gradient(circle_at_top_right,#dbeafe_0,transparent_28rem),#f4f4f5]">

    <div data-sidebar-backdrop
         class="fixed inset-0 z-40 bg-zinc-900/60 backdrop-blur-sm opacity-0 pointer-events-none lg:hidden backdrop-fade"
         aria-hidden="true"></div>

    <aside data-sidebar
           class="fixed inset-y-0 left-0 z-50 m-0 flex w-72 shrink-0 flex-col border-r border-zinc-200 bg-white sidebar-transition -translate-x-full lg:sticky lg:top-3 lg:m-3 lg:h-[calc(100dvh-1.5rem)] lg:rounded-[1.75rem] lg:border lg:shadow-xl lg:shadow-zinc-900/5 lg:translate-x-0">

        <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-5">
            <a href="{{ route('siswa.dashboard') }}" class="flex items-center gap-2.5">
                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-zinc-950 text-white">
                    <x-icon name="graduation" class="h-5 w-5" />
                </span>
                <div>
                    <p class="font-display text-sm font-semibold text-zinc-900">Studio Siswa</p>
                    <p class="text-[10px] uppercase tracking-[0.24em] text-zinc-500">Portofolio</p>
                </div>
            </a>
            <button data-sidebar-close type="button"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-zinc-500 hover:bg-zinc-100 hover:text-zinc-700 lg:hidden"
                    aria-label="Tutup menu">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <nav data-sidebar-nav class="flex-1 space-y-1 p-3 text-sm">
            @php
                $items = [
                    ['route' => 'siswa.dashboard', 'href' => route('siswa.dashboard'), 'icon' => 'dashboard', 'label' => 'Dashboard'],
                    ['route' => 'siswa.profile.*', 'href' => route('siswa.profile.edit'), 'icon' => 'user', 'label' => 'Edit Profil'],
                    ['route' => 'siswa.portfolios.*', 'href' => route('siswa.portfolios.index'), 'icon' => 'briefcase', 'label' => 'Portofolio'],
                    ['route' => 'siswa.certificates.*', 'href' => route('siswa.certificates.index'), 'icon' => 'award', 'label' => 'Sertifikat'],
                    ['route' => 'students.cv', 'href' => route('students.cv', auth()->user()), 'icon' => 'download', 'label' => 'CV Saya'],
                    ['route' => 'account.*', 'href' => route('account.edit'), 'icon' => 'lock', 'label' => 'Pengaturan Akun'],
                ];
            @endphp
            @foreach ($items as $item)
                <a href="{{ $item['href'] }}"
                   class="group flex items-center gap-3 rounded-2xl px-3 py-2.5 text-zinc-700 transition hover:bg-zinc-100 {{ request()->routeIs($item['route']) ? 'bg-zinc-950 !text-white hover:!bg-zinc-900' : '' }}">
                    <x-icon :name="$item['icon']" class="h-5 w-5" />
                    <span class="font-medium">{{ $item['label'] }}</span>
                </a>
            @endforeach

            <a href="{{ route('portfolios.user', auth()->user()) }}" target="_blank"
               class="group mt-2 flex items-center gap-3 rounded-2xl border border-dashed border-zinc-300 px-3 py-2.5 text-zinc-600 transition hover:bg-zinc-50">
                <x-icon name="external" class="h-5 w-5" />
                <span class="text-sm font-medium">Lihat Profil Publik</span>
            </a>
        </nav>

        <div class="border-t border-zinc-200 p-3">
            <a href="{{ route('siswa.profile.edit') }}" class="flex items-center gap-3 rounded-2xl bg-zinc-50 p-2 transition hover:bg-zinc-100">
                <img src="{{ auth()->user()->avatarUrl() }}" alt="" class="h-9 w-9 rounded-full object-cover ring-1 ring-zinc-200">
                <div class="flex-1 truncate">
                    <p class="truncate text-sm font-medium">{{ auth()->user()->name }}</p>
                    <p class="truncate text-xs text-zinc-500">{{ auth()->user()->school_class ?: 'Edit profil' }}</p>
                </div>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">@csrf
                <button class="flex w-full items-center gap-3 rounded-2xl px-3 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50">
                    <x-icon name="logout" class="h-5 w-5" /> Logout
                </button>
            </form>
        </div>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-30 border-b border-zinc-200/70 bg-white/70 backdrop-blur-xl">
            <div class="flex items-center justify-between gap-2 px-4 py-3 lg:px-8">
                <button data-sidebar-toggle type="button"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-zinc-600 hover:bg-zinc-100 lg:hidden"
                        aria-label="Buka menu"
                        aria-controls="siswa-sidebar"
                        aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6h16.5M3.75 12h16.5M3.75 18h16.5"/>
                    </svg>
                </button>
                <div class="ml-auto flex items-center gap-2">
                    <x-badge variant="success">
                        <span class="block h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        Akun Aktif
                    </x-badge>
                </div>
            </div>
        </header>

        <main class="flex-1 px-4 py-6 lg:px-8 lg:py-8">
            @if (session('status'))
                <x-alert variant="success" class="mb-6">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <span>{{ session('status') }}</span>
                        @if (session('share_url'))
                            <button type="button" data-share-url="{{ session('share_url') }}" data-share-title="{{ session('share_title') }}" data-share-text="{{ session('share_text') }}"
                                    class="inline-flex shrink-0 items-center gap-2 rounded-lg bg-emerald-700 px-3 py-2 text-xs font-semibold text-white transition hover:bg-emerald-800">
                                <x-icon name="external" class="h-4 w-4" /> Bagikan preview
                            </button>
                        @endif
                    </div>
                    @if (session('share_url'))
                        <p class="mt-2 text-xs text-emerald-700">Preview hanya dapat dibuka oleh pengguna yang sudah login.</p>
                    @endif
                </x-alert>
            @endif
            @if ($errors->any())
                <x-alert variant="error" class="mb-6">
                    <ul class="space-y-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </x-alert>
            @endif
            @yield('content')
        </main>
    </div>
</div>
@endsection
