@extends('layouts.siswa')
@section('title', 'Portofolio Saya')

@section('content')
    <header class="mb-6 flex items-end justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">Karya</p>
            <h1 class="font-display text-3xl font-bold tracking-tight text-zinc-900">Portofolio Saya</h1>
            <p class="mt-1 text-sm text-zinc-500">Karya baru tampil publik setelah disetujui admin.</p>
        </div>
        <x-button href="{{ route('siswa.portfolios.create') }}" variant="primary">
            <x-icon name="plus" class="h-4 w-4" /> Tambah Portofolio
        </x-button>
    </header>

    @if ($portfolios->count())
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($portfolios as $p)
                <article class="overflow-hidden rounded-2xl border border-zinc-200 bg-white">
                    <div class="aspect-[4/3] bg-zinc-100">
                        <img src="{{ $p->coverUrl() }}" alt="" class="h-full w-full object-cover">
                    </div>
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-semibold text-zinc-900 line-clamp-1">{{ $p->title }}</h3>
                            @if ($p->is_featured)
                                <x-badge variant="warning">Unggulan</x-badge>
                            @endif
                            <x-badge :variant="$p->approval_status === 'approved' ? 'success' : ($p->approval_status === 'rejected' ? 'danger' : 'warning')">{{ ucfirst($p->approval_status) }}</x-badge>
                        </div>
                        <p class="mt-1 text-xs text-zinc-500">{{ $p->category ?? 'Tanpa kategori' }}</p>
                        @if ($p->skills->count())
                            <div class="mt-3 flex flex-wrap gap-1">
                                @foreach ($p->skills->take(3) as $s)
                                    <span class="rounded-md bg-zinc-100 px-2 py-0.5 text-xs text-zinc-600">{{ $s->name }}</span>
                                @endforeach
                            </div>
                        @endif
                        <div class="mt-4 flex items-center justify-between border-t border-zinc-100 pt-3">
                            @if ($p->isApproved())
                                <a href="{{ route('portfolios.show', $p) }}" target="_blank" class="text-xs font-medium text-blue-600 hover:underline">Lihat publik</a>
                            @else
                                <span class="text-xs text-zinc-400">Belum publik</span>
                            @endif
                            <span class="inline-flex items-center gap-1 text-xs text-zinc-400" title="{{ $p->views }} kali dilihat">
                                <x-icon name="eye" class="h-3 w-3" /> {{ $p->views ?: 0 }}
                            </span>
                            <div class="flex gap-1">
                                <a href="{{ route('siswa.portfolios.edit', $p) }}" class="rounded-md border border-zinc-300 px-2.5 py-1 text-xs font-medium text-zinc-700 hover:bg-zinc-50">Edit</a>
                                <form method="POST" action="{{ route('siswa.portfolios.destroy', $p) }}" onsubmit="return confirm('Hapus portofolio ini?')">
                                    @csrf @method('DELETE')
                                    <button class="rounded-md border border-red-200 px-2.5 py-1 text-xs font-medium text-red-600 hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="mt-6">{{ $portfolios->links() }}</div>
    @else
        <x-empty-state name="briefcase" title="Belum ada portofolio"
            description="Tambahkan karya pertamamu untuk dilihat perusahaan dan rekruiter.">
            <x-slot:actions>
                <x-button href="{{ route('siswa.portfolios.create') }}" variant="primary">
                    <x-icon name="plus" class="h-4 w-4" /> Buat Portofolio
                </x-button>
            </x-slot:actions>
        </x-empty-state>
    @endif
@endsection
