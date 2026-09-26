@extends('layouts.admin')
@section('title', $contact->subject)

@section('content')
    <a href="{{ route('admin.contacts.index') }}" class="mb-4 inline-flex items-center gap-2 rounded-full border border-zinc-200 bg-white px-3 py-1.5 text-sm text-zinc-600 hover:text-blue-600">
        <x-icon name="arrow-left" class="h-4 w-4" /> Kembali ke Kotak Masuk
    </a>

    <article class="rounded-[1.75rem] border border-zinc-200 bg-white shadow-sm">
        <header class="border-b border-zinc-100 p-6">
            <div class="flex items-start gap-3">
                <span class="mt-1 flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 text-blue-700">
                    <x-icon name="mail" class="h-5 w-5" />
                </span>
                <div class="flex-1">
                    <h1 class="font-display text-3xl font-bold tracking-tight text-zinc-950">{{ $contact->subject }}</h1>
                    <p class="mt-1 text-sm text-zinc-600">Dari <strong class="text-zinc-900">{{ $contact->sender_name }}</strong>
                        @if ($contact->sender_company) — {{ $contact->sender_company }} @endif
                    </p>
                    <p class="mt-1 text-xs text-zinc-500">{{ $contact->created_at->isoFormat('dddd, D MMMM Y H:mm') }}</p>
                </div>
            </div>
        </header>

        <div class="space-y-6 p-6">
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-3">
                    <p class="text-xs uppercase tracking-wider text-zinc-500">Email</p>
                    <a href="mailto:{{ $contact->sender_email }}" class="mt-1 block truncate text-sm font-medium text-zinc-900 hover:text-blue-600">{{ $contact->sender_email }}</a>
                </div>
                @if ($contact->sender_phone)
                    <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-3">
                        <p class="text-xs uppercase tracking-wider text-zinc-500">Telepon</p>
                        <a href="tel:{{ $contact->sender_phone }}" class="mt-1 block text-sm font-medium text-zinc-900 hover:text-blue-600">{{ $contact->sender_phone }}</a>
                    </div>
                @endif
                @if ($contact->sender_company)
                    <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-3">
                        <p class="text-xs uppercase tracking-wider text-zinc-500">Perusahaan</p>
                        <p class="mt-1 text-sm font-medium text-zinc-900">{{ $contact->sender_company }}</p>
                    </div>
                @endif
            </div>

            <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-5">
                <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-zinc-500">Pesan</p>
                <p class="whitespace-pre-line text-sm leading-relaxed text-zinc-800">{{ $contact->message }}</p>
            </div>

            <div class="flex flex-wrap gap-3 border-t border-zinc-100 pt-5">
                <a href="mailto:{{ $contact->sender_email }}?subject=Re: {{ $contact->subject }}" class="inline-flex items-center gap-2 rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-zinc-800 active:scale-95">
                    <x-icon name="mail" class="h-4 w-4" /> Balas via Email
                </a>
                <form method="POST" action="{{ route('admin.contacts.destroy', $contact) }}" onsubmit="return confirm('Hapus pesan ini?')">
                    @csrf @method('DELETE')
                    <button class="inline-flex items-center gap-2 rounded-lg border border-red-200 px-4 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50">
                        <x-icon name="trash" class="h-4 w-4" /> Hapus
                    </button>
                </form>
            </div>
        </div>
    </article>
@endsection
