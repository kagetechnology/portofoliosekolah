@props(['name' => 'inbox', 'title' => 'Belum ada data', 'description' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center rounded-2xl border border-dashed border-zinc-300 bg-white px-6 py-12 text-center']) }}>
    <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 text-zinc-500">
        <x-icon :name="$name" class="h-6 w-6" />
    </div>
    <p class="text-base font-semibold text-zinc-800">{{ $title }}</p>
    @if ($description)
        <p class="mt-1 max-w-sm text-sm text-zinc-500">{{ $description }}</p>
    @endif
    <div class="mt-4">{{ $actions ?? '' }}</div>
</div>
