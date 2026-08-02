@extends('layouts.admin')
@section('title', 'Pesan Masuk')

@section('content')
    <header class="mb-6 flex items-end justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">Kotak Masuk</p>
            <h1 class="font-display text-3xl font-bold tracking-tight text-zinc-900">Pesan</h1>
            <p class="mt-1 text-sm text-zinc-500">Pesan dari perusahaan dan stakeholder.</p>
        </div>
    </header>

    <div class="mb-4 inline-flex rounded-lg border border-zinc-200 bg-white p-1 text-sm">
        <a href="{{ route('admin.contacts.index') }}"
           class="rounded-md px-3 py-1.5 transition {{ $filter === '' ? 'bg-zinc-900 text-white' : 'text-zinc-700 hover:bg-zinc-50' }}">Semua</a>
        <a href="{{ route('admin.contacts.index', ['filter' => 'unread']) }}"
           class="rounded-md px-3 py-1.5 transition {{ $filter === 'unread' ? 'bg-zinc-900 text-white' : 'text-zinc-700 hover:bg-zinc-50' }}">Belum Dibaca</a>
        <a href="{{ route('admin.contacts.index', ['filter' => 'read']) }}"
           class="rounded-md px-3 py-1.5 transition {{ $filter === 'read' ? 'bg-zinc-900 text-white' : 'text-zinc-700 hover:bg-zinc-50' }}">Sudah Dibaca</a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white">
        <ul class="divide-y divide-zinc-100">
            @forelse ($contacts as $c)
                <li class="group transition hover:bg-zinc-50 {{ $c->is_read ? '' : 'bg-blue-50/40' }}">
                    <a href="{{ route('admin.contacts.show', $c) }}" class="block px-5 py-4">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <h3 class="truncate font-semibold text-zinc-900">{{ $c->subject }}</h3>
                                    @if (! $c->is_read)
                                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-medium uppercase tracking-wider text-blue-700">Baru</span>
                                    @endif
                                </div>
                                <p class="mt-0.5 text-sm text-zinc-600">{{ $c->sender_name }}@if($c->sender_company) · <span class="text-zinc-500">{{ $c->sender_company }}</span>@endif</p>
                                <p class="mt-1 line-clamp-1 text-xs text-zinc-500">{{ $c->message }}</p>
                            </div>
                            <div class="shrink-0 text-right text-xs text-zinc-500">
                                <p>{{ $c->created_at->diffForHumans() }}</p>
                                <p class="mt-1 text-zinc-400">{{ $c->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                    </a>
                </li>
            @empty
                <li class="px-5 py-16">
                    <x-empty-state name="inbox" title="Belum ada pesan"
                        description="Pesan dari perusahaan akan muncul di sini saat mereka menghubungi lewat halaman publik." />
                </li>
            @endforelse
        </ul>
    </div>

    <div class="mt-5">{{ $contacts->links() }}</div>
@endsection
