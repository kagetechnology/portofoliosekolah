@props(['variant' => 'neutral'])

@php
    $variants = [
        'neutral' => 'bg-zinc-100 text-zinc-700',
        'success' => 'bg-emerald-100 text-emerald-700',
        'warning' => 'bg-amber-100 text-amber-800',
        'danger' => 'bg-red-100 text-red-700',
        'info' => 'bg-blue-100 text-blue-700',
    ];
    $cls = 'inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium '.$variants[$variant];
@endphp

<span {{ $attributes->merge(['class' => $cls]) }}>{{ $slot }}</span>
