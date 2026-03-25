{{--
    Card Component

    Usage:
    <x-guest::ui.card>
        <x-slot:title>Card Title</x-slot:title>
        <x-slot:icon>description</x-slot:icon>
        Card content here
    </x-guest::ui.card>
--}}

@props([
'variant' => 'default', // default, bordered, elevated
'padding' => 'md', // sm, md, lg
])

@php
$baseClasses = 'bg-white rounded-xl';

$variantClasses = [
'none' => '',
'default' => 'shadow-sm',
'bordered' => 'border border-primary/10',
'elevated' => 'shadow-xl',
];


$paddingClasses = [
'none' => 'p-0',
'sm' => 'p-4',
'md' => 'p-6',
'lg' => 'p-8',
];


$classes = $baseClasses . ' ' . $variantClasses[$variant] . ' ' . $paddingClasses[$padding];

@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    @if(isset($icon) || isset($title))
    <div class="flex items-center gap-3 mb-6">
        @isset($icon)
        <x-guest::ui.icon :name="$icon" size="xl" color="text-primary" />
        @endisset

        @isset($title)
        <h3 class="text-2xl font-bold text-slate-800">{{ $title }}</h3>
        @endisset
    </div>
    @endif

    <div class="text-slate-600">
        {{ $slot }}
    </div>

    @isset($footer)
    <div class="mt-6 pt-6 border-t border-slate-100">
        {{ $footer }}
    </div>
    @endisset
</div>