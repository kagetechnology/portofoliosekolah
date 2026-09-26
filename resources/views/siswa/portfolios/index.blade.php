@extends('layouts.siswa')
@section('title', 'Portofolio Saya')

@section('content')
    <header class="mb-6 rounded-[2rem] border border-zinc-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-blue-600">Karya</p>
                <h1 class="font-display mt-3 text-4xl font-bold tracking-tight text-zinc-950 md:text-5xl">Portofolio Saya</h1>
                <p class="mt-2 max-w-2xl text-sm text-zinc-500">Karya baru tampil publik setelah disetujui admin.</p>
            </div>
            <x-button href="{{ route('siswa.portfolios.create') }}" variant="primary">
                <x-icon name="plus" class="h-4 w-4" /> Tambah
            </x-button>
        </div>
    </header>

    @if ($portfolios->count())
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($portfolios as $p)
                @php $isOwner = $p->user_id === auth()->id(); @endphp
                <article class="overflow-hidden rounded-[1.75rem] border border-zinc-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                    <div class="aspect-[4/3] bg-zinc-100">
                        <img src="{{ $p->coverUrl() }}" alt="" loading="lazy" decoding="async" class="h-full w-full object-cover">
                    </div>
                    <div class="p-4">
                        @if ($p->approval_status === 'rejected' && $p->rejection_note)
                            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-left">
                                <p class="text-xs font-bold text-red-900">Perlu diperbaiki</p>
                                <p class="mt-1 text-xs leading-5 text-red-800">{{ $p->rejection_note }}</p>
                                @if ($isOwner)
                                    <a href="{{ route('siswa.portfolios.edit', $p) }}" class="mt-2 inline-flex text-xs font-semibold text-red-800 underline">Perbaiki sekarang</a>
                                @endif
                            </div>
                        @endif
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-semibold text-zinc-900 line-clamp-1">{{ $p->title }}</h3>
                            @if ($p->is_featured)
                                <x-badge variant="warning">Unggulan</x-badge>
                            @endif
                            <x-badge :variant="$p->approval_status === 'approved' ? 'success' : ($p->approval_status === 'rejected' ? 'danger' : 'warning')">{{ ucfirst($p->approval_status) }}</x-badge>
                            @unless ($isOwner)
                                <x-badge variant="info">Kontributor</x-badge>
                            @endunless
                        </div>
                        <p class="mt-1 text-xs text-zinc-500">{{ $p->category ?? 'Tanpa kategori' }}</p>
                        <p class="mt-1 text-xs font-medium text-zinc-600">{{ $p->project_type === 'team' ? 'Project Tim' : 'Project Personal' }}</p>
                        @if ($p->skills->count())
                            <div class="mt-3 flex flex-wrap gap-1">
                                @foreach ($p->skills->take(3) as $s)
                                    <span class="rounded-md bg-zinc-100 px-2 py-0.5 text-xs text-zinc-600">{{ $s->name }}</span>
                                @endforeach
                            </div>
                        @endif
                        <div class="mt-4 flex flex-wrap items-center justify-between gap-2 border-t border-zinc-100 pt-3">
                            @if ($p->isApproved())
                                <a href="{{ route('portfolios.show', $p) }}" target="_blank" class="text-xs font-medium text-blue-600 hover:underline">Lihat publik</a>
                                <button type="button" data-share-url="{{ route('portfolios.show', $p) }}" data-share-title="{{ $p->title }}" data-share-text="Lihat project {{ $p->title }}" class="text-xs font-medium text-zinc-600 hover:text-blue-600">Bagikan</button>
                            @else
                                <span class="text-xs text-zinc-400">Belum publik</span>
                                @if ($isOwner)
                                    <button type="button" data-share-url="{{ \Illuminate\Support\Facades\URL::signedRoute('portfolios.preview', $p) }}" data-share-title="{{ $p->title }}" data-share-text="Lihat preview project {{ $p->title }}" class="text-xs font-medium text-blue-600 hover:underline">Bagikan preview</button>
                                @endif
                            @endif
                            <span class="inline-flex items-center gap-1 text-xs text-zinc-400" title="{{ $p->views }} kali dilihat">
                                <x-icon name="eye" class="h-3 w-3" /> {{ $p->views ?: 0 }}
                            </span>
                            @if ($isOwner)
                                <div class="flex gap-1">
                                    <a href="{{ route('siswa.portfolios.edit', $p) }}" class="rounded-md border border-zinc-300 px-2.5 py-1 text-xs font-medium text-zinc-700 hover:bg-zinc-50">Edit</a>
                                    <form method="POST" action="{{ route('siswa.portfolios.destroy', $p) }}" onsubmit="return confirm('Hapus portofolio ini?')">
                                        @csrf @method('DELETE')
                                        <button class="rounded-md border border-red-200 px-2.5 py-1 text-xs font-medium text-red-600 hover:bg-red-50">Hapus</button>
                                    </form>
                                </div>
                            @endif
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
