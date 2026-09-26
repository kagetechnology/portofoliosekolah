@extends('layouts.base')

@section('body')
<div data-sidebar-root class="flex min-h-dvh bg-[radial-gradient(circle_at_top_left,#dbeafe_0,transparent_28rem),#f4f4f5]">

    <div data-sidebar-backdrop
         class="fixed inset-0 z-40 bg-zinc-900/60 backdrop-blur-sm opacity-0 pointer-events-none lg:hidden backdrop-fade"
         aria-hidden="true"></div>

    <aside data-sidebar
           class="fixed inset-y-0 left-0 z-50 m-0 flex w-72 shrink-0 flex-col border-r border-zinc-200 bg-white sidebar-transition -translate-x-full lg:sticky lg:top-3 lg:m-3 lg:h-[calc(100dvh-1.5rem)] lg:rounded-[1.75rem] lg:border lg:shadow-xl lg:shadow-zinc-900/5 lg:translate-x-0">

        <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-5">
            <a href="{{ route(auth()->user()->isGuru() ? 'guru.dashboard' : 'admin.dashboard') }}" class="flex items-center gap-2.5">
                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-zinc-950 text-white">
                    <x-icon name="graduation" class="h-5 w-5" />
                </span>
                <div>
                    <p class="font-display text-sm font-semibold text-zinc-900">{{ auth()->user()->isGuru() ? 'Guru Desk' : 'Admin Desk' }}</p>
                    <p class="text-[10px] uppercase tracking-[0.24em] text-zinc-500">Validasi sekolah</p>
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
                $items = auth()->user()->isGuru() ? [
                    ['route' => 'guru.dashboard', 'href' => route('guru.dashboard'), 'icon' => 'dashboard', 'label' => 'Dashboard'],
                    ['route' => 'guru.portfolios.*', 'href' => route('guru.portfolios.index'), 'icon' => 'briefcase', 'label' => 'Portofolio'],
                    ['route' => 'guru.certificates.*', 'href' => route('guru.certificates.index'), 'icon' => 'award', 'label' => 'Sertifikat'],
                    ['route' => 'account.*', 'href' => route('account.edit'), 'icon' => 'lock', 'label' => 'Pengaturan Akun'],
                ] : [
                    ['route' => 'admin.dashboard', 'href' => route('admin.dashboard'), 'icon' => 'dashboard', 'label' => 'Dashboard'],
                    ['route' => 'admin.users.*', 'href' => route('admin.users.index'), 'icon' => 'users', 'label' => 'Pengguna'],
                    ['route' => 'admin.portfolios.*', 'href' => route('admin.portfolios.index'), 'icon' => 'briefcase', 'label' => 'Portofolio'],
                    ['route' => 'admin.certificates.*', 'href' => route('admin.certificates.index'), 'icon' => 'award', 'label' => 'Sertifikat'],
                    ['route' => 'admin.skills.*', 'href' => route('admin.skills.index'), 'icon' => 'sparkles', 'label' => 'Skills'],
                    ['route' => 'admin.categories.*', 'href' => route('admin.categories.index'), 'icon' => 'filter', 'label' => 'Kategori'],
                    ['route' => 'admin.contacts.*', 'href' => route('admin.contacts.index'), 'icon' => 'inbox', 'label' => 'Pesan Masuk'],
                    ['route' => 'admin.school.*', 'href' => route('admin.school.edit'), 'icon' => 'building', 'label' => 'Profil Sekolah'],
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
        </nav>

        <div class="border-t border-zinc-200 p-3">
            <div class="flex items-center gap-3 rounded-2xl bg-zinc-50 p-2">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-zinc-950 text-sm font-semibold text-white">
                    {{ mb_substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="flex-1 truncate">
                    <p class="truncate text-sm font-medium text-zinc-900">{{ auth()->user()->name }}</p>
                    <p class="truncate text-xs text-zinc-500">{{ auth()->user()->email }}</p>
                </div>
            </div>
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
                        aria-controls="admin-sidebar"
                        aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6h16.5M3.75 12h16.5M3.75 18h16.5"/>
                    </svg>
                </button>
                <div class="ml-auto flex items-center gap-2">
                    <a href="{{ route('home') }}" target="_blank" class="hidden items-center gap-2 rounded-lg border border-zinc-300 px-3 py-1.5 text-xs font-medium text-zinc-700 transition hover:bg-zinc-50 sm:inline-flex">
                        <x-icon name="external" class="h-3 w-3" /> Lihat Publik
                    </a>
                </div>
            </div>
        </header>

        <main class="flex-1 px-4 py-6 lg:px-8 lg:py-8">
            @if (session('status'))
                <x-alert variant="success" class="mb-6">{{ session('status') }}</x-alert>
            @endif
            @if ($errors->any() && ! request()->routeIs(['login','register']))
                <x-alert variant="error" class="mb-6">
                    <ul class="space-y-1">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </x-alert>
            @endif
            @yield('content')
        </main>
    </div>
</div>
@endsection
