{{--
    Badge Component

    Usage:
    <x-guest::ui.badge>Default</x-guest::ui.badge>
    <x-guest::ui.badge variant="success">Success</x-guest::ui.badge>
    <x-guest::ui.badge variant="warning" icon="info">Warning</x-guest::ui.badge>
--}}

@props([
    'variant' => 'default', // default, primary, success, warning, danger, info
    'size' => 'md', // sm, md, lg
    'icon' => null,
])

@php
    $baseClasses = 'inline-flex items-center gap-1.5 font-semibold rounded-full';

    $variantClasses = [
        'default' => 'bg-slate-100 text-slate-700',
        'primary' => 'bg-primary/10 text-primary',
        'success' => 'bg-green-100 text-green-700',
        'warning' => 'bg-yellow-100 text-yellow-700',
        'danger' => 'bg-red-100 text-red-700',
        'info' => 'bg-blue-100 text-blue-700',
    ];

    $sizeClasses = [
        'sm' => 'px-2 py-0.5 text-xs',
        'md' => 'px-3 py-1 text-sm',
        'lg' => 'px-4 py-1.5 text-base',
    ];

    $classes = $baseClasses . ' ' . $variantClasses[$variant] . ' ' . $sizeClasses[$size];
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <span class="material-icons text-xs">{{ $icon }}</span>
    @endif
    {{ $slot }}
</span>
