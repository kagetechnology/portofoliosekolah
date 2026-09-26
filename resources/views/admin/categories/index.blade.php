@extends('layouts.admin')
@section('title', 'Master Kategori')

@section('content')
    <header class="mb-6 rounded-[2rem] border border-zinc-200 bg-white p-6 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-blue-600">Master data</p>
        <div class="mt-3 flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="font-display text-4xl font-bold tracking-tight text-zinc-950 md:text-5xl">Kategori</h1>
                <p class="mt-2 max-w-2xl text-sm text-zinc-500">Admin menentukan kategori project. Siswa hanya dapat memilih kategori yang tersedia.</p>
            </div>
            <x-badge variant="info">{{ $categories->total() }} kategori</x-badge>
        </div>
    </header>

    <div class="grid gap-6 lg:grid-cols-[20rem_minmax(0,1fr)]">
        <section class="rounded-[1.75rem] border border-zinc-200 bg-white p-5 shadow-sm lg:sticky lg:top-24 lg:self-start">
            <h2 class="font-display text-xl font-bold tracking-tight text-zinc-900">Tambah kategori</h2>
            <p class="mt-1 text-sm text-zinc-500">Nama harus unik, maksimal 100 karakter.</p>
            <form method="POST" action="{{ route('admin.categories.store') }}" class="mt-5 space-y-3">
                @csrf
                <div>
                    <label class="text-sm font-medium text-zinc-800">Nama kategori</label>
                    <input name="name" value="{{ old('name') }}" required maxlength="100" placeholder="Contoh: Web Development"
                           class="mt-1.5 w-full rounded-xl border border-zinc-200 px-3 py-2.5 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                </div>
                <x-button type="submit" variant="primary" class="w-full"><x-icon name="plus" class="h-4 w-4" /> Tambah</x-button>
            </form>
        </section>

        <section class="space-y-4">
            <form method="GET" class="rounded-[1.75rem] border border-zinc-200 bg-white p-4 shadow-sm">
                <div class="relative">
                    <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
                    <input name="q" value="{{ $q }}" placeholder="Cari kategori..."
                           class="w-full rounded-xl border border-zinc-200 bg-zinc-50 py-2.5 pl-9 pr-3 text-sm focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                </div>
            </form>

            <div class="overflow-hidden rounded-[1.75rem] border border-zinc-200 bg-white shadow-sm">
                <div class="divide-y divide-zinc-100">
                    @forelse ($categories as $category)
                        @php $count = (int) ($usage[$category->name] ?? 0); @endphp
                        <article class="grid gap-3 px-5 py-4 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-center">
                            <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="flex min-w-0 items-center gap-3">
                                @csrf @method('PATCH')
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-700"><x-icon name="filter" class="h-4 w-4" /></span>
                                <input name="name" value="{{ $category->name }}" required maxlength="100"
                                       class="min-w-0 flex-1 rounded-xl border border-transparent bg-transparent px-2 py-2 text-sm font-semibold text-zinc-900 hover:border-zinc-200 focus:border-zinc-900 focus:bg-white focus:outline-none">
                                <button class="rounded-full border border-zinc-300 px-3 py-1.5 text-xs font-medium text-zinc-700 hover:bg-zinc-50">Simpan</button>
                            </form>
                            <div class="flex items-center justify-end gap-3">
                                <span class="text-xs text-zinc-400">{{ $count }} portfolio</span>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf @method('DELETE')
                                    <button @disabled($count > 0) class="rounded-full border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-40">Hapus</button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <div class="px-5 py-14 text-center text-sm text-zinc-400">Kategori tidak ditemukan.</div>
                    @endforelse
                </div>
            </div>

            {{ $categories->links() }}
        </section>
    </div>
@endsection
