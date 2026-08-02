@extends('layouts.base')
@section('title', 'Hubungi Sekolah')

@section('body')
    @include('layouts.partials.public-nav')

    <main id="main" class="mx-auto max-w-5xl px-4 pb-12 pt-6 md:pt-10">
        <header class="mb-8">
            <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">Kontak</p>
            <h1 class="font-display text-3xl font-bold tracking-tight text-zinc-900 md:text-4xl">Hubungi Sekolah</h1>
            <p class="mt-2 max-w-prose text-zinc-600">Kirim pesan untuk diskusi rekrutmen, magang, atau kerja sama. Tim sekolah akan merespon lewat email.</p>
        </header>

        @if (session('status'))
            <x-alert variant="success" class="mb-6">
                <strong>Terkirim.</strong> {{ session('status') }}
            </x-alert>
        @endif
        @if ($errors->any())
            <x-alert variant="error" class="mb-6">
                <strong>Tidak dapat mengirim.</strong> Periksa kembali field yang ditandai merah.
            </x-alert>
        @endif

        <div class="grid gap-6 md:grid-cols-5">
            <aside class="md:col-span-2">
                <div class="rounded-2xl border border-zinc-200 bg-white p-6">
                    <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500">Informasi Sekolah</p>
                    <h2 class="font-display mt-2 text-xl font-bold text-zinc-900">{{ $school?->name ?? 'Sekolah' }}</h2>
                    <ul class="mt-4 space-y-3 text-sm text-zinc-700">
                        @if ($school?->address)
                            <li class="flex items-start gap-3"><x-icon name="building" class="mt-0.5 h-4 w-4 text-zinc-400" /> <span>{{ $school->address }}</span></li>
                        @endif
                        @if ($school?->phone)
                            <li class="flex items-start gap-3"><x-icon name="phone" class="mt-0.5 h-4 w-4 text-zinc-400" /> <span>{{ $school->phone }}</span></li>
                        @endif
                        @if ($school?->email)
                            <li class="flex items-start gap-3"><x-icon name="mail" class="mt-0.5 h-4 w-4 text-zinc-400" /> <a href="mailto:{{ $school->email }}" class="hover:text-blue-600">{{ $school->email }}</a></li>
                        @endif
                    </ul>

                    @if ($school?->kepala_sekolah)
                        <div class="mt-5 border-t border-zinc-100 pt-4">
                            <p class="text-xs uppercase tracking-wider text-zinc-500">Kepala Sekolah</p>
                            <p class="mt-1 font-medium text-zinc-900">{{ $school->kepala_sekolah }}</p>
                        </div>
                    @endif
                </div>
            </aside>

            <form method="POST" action="{{ route('contact.store') }}" class="md:col-span-3 rounded-2xl border border-zinc-200 bg-white p-6 md:p-8">
                @csrf
                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-zinc-800">Nama <span class="text-red-500">*</span></label>
                        <input name="sender_name" value="{{ old('sender_name') }}" required
                               class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2.5 text-sm transition focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10 @error('sender_name') border-red-400 @enderror">
                        @error('sender_name') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-zinc-800">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="sender_email" value="{{ old('sender_email') }}" required
                               class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2.5 text-sm transition focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10 @error('sender_email') border-red-400 @enderror">
                        @error('sender_email') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-zinc-800">Perusahaan</label>
                        <input name="sender_company" value="{{ old('sender_company') }}"
                               class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2.5 text-sm transition focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-zinc-800">No. Telepon</label>
                        <input name="sender_phone" value="{{ old('sender_phone') }}"
                               class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2.5 text-sm transition focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                    </div>
                </div>
                <div class="mt-5 space-y-1.5">
                    <label class="block text-sm font-medium text-zinc-800">Subjek <span class="text-red-500">*</span></label>
                    <input name="subject" value="{{ old('subject', request('subject')) }}" required
                           class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2.5 text-sm transition focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10 @error('subject') border-red-400 @enderror">
                    @error('subject') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="mt-5 space-y-1.5">
                    <label class="block text-sm font-medium text-zinc-800">Pesan <span class="text-red-500">*</span></label>
                    <textarea name="message" rows="6" required minlength="10"
                              class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2.5 text-sm transition focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10 @error('message') border-red-400 @enderror">{{ old('message') }}</textarea>
                    @error('message') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    <p class="text-xs text-zinc-500">Minimal 10 karakter.</p>
                </div>
                <div class="mt-6 flex items-center justify-between">
                    <p class="text-xs text-zinc-500">Pesan akan dikirim ke email sekolah.</p>
                    <x-button type="submit" variant="primary">
                        <x-icon name="mail" class="h-4 w-4" /> Kirim Pesan
                    </x-button>
                </div>
            </form>
        </div>
    </main>

    @include('layouts.partials.footer')
@endsection
