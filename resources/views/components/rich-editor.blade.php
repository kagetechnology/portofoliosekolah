@props([
    'name',
    'value' => '',
    'required' => false,
    'rows' => 6,
    'placeholder' => 'Tulis konten...',
    'minlength' => null,
    'showGuidance' => false,
    'helperText' => null,
])

@php $cleanValue = \App\Support\RichText::clean((string) $value) ?? ''; @endphp

<div class="space-y-3" data-rich-editor-wrapper data-min-length="{{ $minlength ?? 0 }}">
    @if ($showGuidance)
        <div class="rounded-2xl border border-blue-200 bg-blue-50/70 p-4 text-xs text-blue-950">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-blue-200/80 pb-2.5">
                <span class="inline-flex items-center gap-1.5 font-bold text-blue-900">
                    <x-icon name="sparkles" class="h-4 w-4 text-blue-600" />
                    Panduan Deskripsi &amp; Multimedia
                </span>
                <button type="button" data-insert-template
                        class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700 active:scale-95">
                    <x-icon name="plus" class="h-3.5 w-3.5" /> Gunakan Kerangka Struktur
                </button>
            </div>

            <div class="mt-3 grid gap-3 text-blue-950 sm:grid-cols-2">
                <div class="rounded-xl bg-white/70 p-3 ring-1 ring-blue-100">
                    <p class="font-semibold text-blue-900">1. Sisipkan Gambar &amp; Screenshot</p>
                    <p class="mt-1 leading-relaxed text-zinc-600">
                        Klik ikon <strong class="font-semibold text-zinc-800">Gambar</strong> di toolbar untuk mengunggah screenshot aplikasi, wireframe, atau foto hasil karya.
                    </p>
                </div>
                <div class="rounded-xl bg-white/70 p-3 ring-1 ring-blue-100">
                    <p class="font-semibold text-blue-900">2. Sematkan Video Demo (YouTube/Vimeo)</p>
                    <p class="mt-1 leading-relaxed text-zinc-600">
                        Klik ikon <strong class="font-semibold text-zinc-800">Media</strong> di toolbar, lalu tempel tautan video YouTube/Vimeo agar player interaktif langsung tayang.
                    </p>
                </div>
            </div>

            <div class="mt-3 rounded-xl border border-blue-200/60 bg-white/80 p-3 leading-relaxed text-zinc-700">
                <span class="font-semibold text-blue-950">Kerangka standar industri (Minimal 300 karakter):</span>
                <ol class="mt-1.5 list-decimal space-y-0.5 pl-4 text-zinc-600">
                    <li><strong>Masalah &amp; Latar Belakang:</strong> Apa persoalan nyata yang ingin diselesaikan?</li>
                    <li><strong>Solusi &amp; Fitur Unggulan:</strong> Bagaimana sistem/karya ini bekerja?</li>
                    <li><strong>Peran Pribadi / Tim:</strong> Tanggung jawab teknis apa yang Anda selesaikan?</li>
                    <li><strong>Tantangan &amp; Hasil:</strong> Kendala yang dipecahkan dan hasil akhir karya.</li>
                </ol>
            </div>
        </div>
    @endif

    <div class="relative">
        <textarea name="{{ $name }}" rows="{{ $rows }}" data-ckeditor data-placeholder="{{ $placeholder }}"
                  class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2.5 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">{{ $cleanValue }}</textarea>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-2 text-xs">
        @if ($helperText)
            <p class="text-zinc-500">{{ $helperText }}</p>
        @else
            <p class="text-zinc-500">Mendukung format heading, list, tabel, upload gambar langsung, dan sematan video.</p>
        @endif

        @if ($minlength)
            <div class="flex items-center gap-2" data-char-meter>
                <span class="font-medium text-zinc-500">
                    <strong class="font-bold text-zinc-900" data-char-count>0</strong> / {{ $minlength }} karakter min.
                </span>
                <span data-counter-status class="font-medium text-amber-700">Menghitung...</span>
            </div>
        @endif
    </div>
</div>
