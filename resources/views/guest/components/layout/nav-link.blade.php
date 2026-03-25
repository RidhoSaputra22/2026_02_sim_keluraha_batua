{{--
    Navigation Link Component

    Usage:
    <x-guest::layout.nav-link href="/home" :active="true">Home</x-guest::layout.nav-link>
--}}

@props([
    'href' => '#',
    'active' => false,
])

<a
    href="{{ $href }}"
    class="text-sm font-medium transition-colors {{ $active ? 'text-primary' : 'hover:text-primary' }}"
    {{ $attributes }}
>
    {{ $slot }}
</a>
