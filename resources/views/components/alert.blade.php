@props(['variant' => 'success'])

@php
    $variants = [
        'success' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
        'error' => 'bg-red-50 text-red-800 border-red-200',
        'warning' => 'bg-amber-50 text-amber-800 border-amber-200',
        'info' => 'bg-blue-50 text-blue-800 border-blue-200',
    ];
    $icons = [
        'success' => 'check-circle',
        'error' => 'info',
        'warning' => 'info',
        'info' => 'info',
    ];
    $cls = 'flex items-start gap-3 rounded-lg border px-4 py-3 text-sm '.$variants[$variant];
@endphp

<div role="alert" {{ $attributes->merge(['class' => $cls]) }}>
    <x-icon :name="$icons[$variant]" class="h-5 w-5 shrink-0" />
    <div class="flex-1">{{ $slot }}</div>
</div>
