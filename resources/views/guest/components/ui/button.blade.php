{{--
    Button Component

    Usage:
    <x-guest::ui.button>Click Me</x-guest::ui.button>
    <x-guest::ui.button variant="secondary">Secondary</x-guest::ui.button>
    <x-guest::ui.button size="lg" icon="send">Submit</x-guest::ui.button>
    <x-guest::ui.button type="submit" :loading="true">Loading...</x-guest::ui.button>
--}}

@props([
'variant' => 'primary', // primary, secondary, outline, ghost
'size' => 'md', // sm, md, lg
'type' => 'button',
'href' => null,
'icon' => null,
'iconPosition' => 'right', // left, right
'loading' => false,
])

@php
$baseClasses = 'cursor-pointer inline-flex items-center justify-center gap-2 font-semibold rounded-lg transition-all focus:outline-none
focus:ring-2 focus:ring-offset-2';

$variantClasses = [
'primary' => 'bg-primary hover:bg-primary/90 text-white shadow-lg shadow-primary/20 focus:ring-primary ',
'secondary' => 'bg-secondary hover:bg-secondary/90 text-white shadow-lg shadow-secondary/20 focus:ring-secondary',
'outline' => 'border-2 border-primary text-primary hover:bg-primary hover:text-white focus:ring-primary',
'ghost' => 'text-primary hover:bg-primary/10 focus:ring-primary',
];

$sizeClasses = [
'sm' => 'px-4 py-2 text-sm',
'md' => 'px-6 py-3 text-base',
'lg' => 'px-8 py-4 text-lg',
];

$classes = $baseClasses . ' ' . $variantClasses[$variant] . ' ' . $sizeClasses[$size];
@endphp

@if($href)
<a href="{{ $href }}" class="{{ $classes }}" {{ $attributes->merge(['class' => '']) }}>
    @if($icon && $iconPosition === 'left' && !$loading)
    <x-guest::ui.icon :name="$icon" size="sm" color="inherit" />
    @endif

    {{ $slot }}

    @if($icon && $iconPosition === 'right' && !$loading)
    <x-guest::ui.icon :name="$icon" size="sm" color="inherit" />
    @endif
</a>
@else
<button type="{{ $type }}" class="{{ $classes }}" {{ $attributes->merge(['class' => '']) }}
    {{ $loading ? 'disabled' : '' }}>
    @if($loading)
    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
        </path>
    </svg>
    @endif

    @if($icon && $iconPosition === 'left' && !$loading)
    <x-guest::ui.icon :name="$icon" size="sm" color="inherit" />
    @endif

    {{ $slot }}

    @if($icon && $iconPosition === 'right' && !$loading)
    <x-guest::ui.icon :name="$icon" size="sm" color="inherit" />
    @endif
</button>
@endif
