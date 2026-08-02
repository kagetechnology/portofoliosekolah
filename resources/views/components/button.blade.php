@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
])

@php
    $base = 'inline-flex items-center justify-center gap-2 font-medium rounded-lg transition-all duration-200 ease-out focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-zinc-900 disabled:opacity-50 disabled:cursor-not-allowed select-none';
    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-5 py-2.5 text-base',
    ];
    $variants = [
        'primary' => 'bg-zinc-900 text-white hover:bg-zinc-800 active:scale-[0.98] shadow-sm shadow-zinc-900/10',
        'secondary' => 'bg-zinc-100 text-zinc-900 hover:bg-zinc-200 active:scale-[0.98]',
        'outline' => 'border border-zinc-300 text-zinc-800 hover:bg-zinc-50 active:scale-[0.98]',
        'ghost' => 'text-zinc-700 hover:bg-zinc-100 active:scale-[0.98]',
        'accent' => 'bg-blue-600 text-white hover:bg-blue-700 active:scale-[0.98] shadow-sm shadow-blue-600/20',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 active:scale-[0.98]',
    ];
    $cls = $base.' '.$sizes[$size].' '.$variants[$variant];
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $cls]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $cls]) }}>{{ $slot }}</button>
@endif
