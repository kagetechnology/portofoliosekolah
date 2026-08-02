@props(['name', 'value' => '', 'required' => false, 'rows' => 6, 'placeholder' => 'Tulis konten...'])

@php $cleanValue = \App\Support\RichText::clean((string) $value) ?? ''; @endphp

<div data-rich-editor class="overflow-hidden rounded-lg border border-zinc-200 bg-white focus-within:border-zinc-900 focus-within:ring-2 focus-within:ring-zinc-900/10">
    <div class="flex flex-wrap gap-1 border-b border-zinc-200 bg-zinc-50 p-2">
        <button type="button" data-rich-command="bold" class="rounded px-2 py-1 text-xs font-bold text-zinc-700 hover:bg-white">B</button>
        <button type="button" data-rich-command="italic" class="rounded px-2 py-1 text-xs italic text-zinc-700 hover:bg-white">I</button>
        <button type="button" data-rich-command="underline" class="rounded px-2 py-1 text-xs underline text-zinc-700 hover:bg-white">U</button>
        <button type="button" data-rich-command="insertUnorderedList" class="rounded px-2 py-1 text-xs text-zinc-700 hover:bg-white">List</button>
        <button type="button" data-rich-command="formatBlock" data-rich-value="blockquote" class="rounded px-2 py-1 text-xs text-zinc-700 hover:bg-white">Quote</button>
        <button type="button" data-rich-link class="rounded px-2 py-1 text-xs text-zinc-700 hover:bg-white">Link</button>
        <button type="button" data-rich-image class="rounded px-2 py-1 text-xs text-zinc-700 hover:bg-white">Gambar URL</button>
        <button type="button" data-rich-command="removeFormat" class="rounded px-2 py-1 text-xs text-zinc-700 hover:bg-white">Clear</button>
    </div>
    <div data-rich-content contenteditable="true" role="textbox" aria-multiline="true" data-placeholder="{{ $placeholder }}"
         class="rich-content min-h-[{{ max(4, (int) $rows) * 1.5 }}rem] w-full px-3 py-2.5 text-sm leading-relaxed outline-none empty:before:text-zinc-400 empty:before:content-[attr(data-placeholder)]">{!! $cleanValue !!}</div>
    <textarea name="{{ $name }}" class="hidden">{{ $cleanValue }}</textarea>
</div>
