@props(['name', 'value', 'label', 'delta' => null])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-zinc-200 bg-white p-5 transition duration-200 hover:shadow-md hover:-translate-y-0.5']) }}>
    <div class="flex items-start justify-between">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-zinc-100 text-zinc-700">
            <x-icon :name="$name" class="h-5 w-5" />
        </div>
        @if ($delta !== null)
            <x-badge :variant="$delta['variant'] ?? 'neutral'">{{ $delta['value'] }}</x-badge>
        @endif
    </div>
    <p class="mt-4 text-2xl font-bold tracking-tight text-zinc-900">{{ $value }}</p>
    <p class="mt-1 text-xs uppercase tracking-wide text-zinc-500">{{ $label }}</p>
</div>
