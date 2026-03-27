{{--
    Search Bar Component

    Usage:
    <x-guest::ui.search-bar
        placeholder="Cari sesuatu..."
        action="/search"
    />

    With icon and custom button:
    <x-guest::ui.search-bar
        icon="search"
        button-text="Cari"
        size="lg"
    />
--}}

@props([
'placeholder' => 'Cari...',
'action' => '#',
'method' => 'GET',
'name' => 'q',
'icon' => 'search',
'buttonText' => 'Cari',
'size' => 'md', // sm, md, lg
'shadow' => true,
])

@php
$sizeClasses = [
'sm' => 'py-2 px-4',
'md' => 'py-4 px-4',
'lg' => 'py-5 px-5',
];

$containerClasses = $shadow ? 'shadow-xl shadow-primary/10' : 'shadow-sm';
@endphp

@php
    $httpMethod = strtoupper($method);
    $formMethod = in_array($httpMethod, ['GET', 'POST'], true) ? $httpMethod : 'POST';
@endphp

<form action="{{ $action }}" method="{{ $formMethod }}" class="relative w-full mx-auto">
    @if ($httpMethod !== 'GET')
        @csrf
    @endif

    @if (! in_array($httpMethod, ['GET', 'POST'], true))
        @method($httpMethod)
    @endif

    <div class="flex items-center bg-white p-2 rounded-xl border border-slate-200 {{ $containerClasses }}">
        @if($icon)
        <div class="pl-3 pr-2 flex items-center pointer-events-none">
            <span class="material-icons text-slate-400">{{ $icon }}</span>
        </div>
        @endif

        <input type="text" name="{{ $name }}" placeholder="{{ $placeholder }}"
            class="w-full bg-transparent border-none focus:outline-none text-slate-700 {{ $sizeClasses[$size] }} text-base md:text-lg placeholder:text-slate-400"
            {{ $attributes }} />

        <button type="submit"
            class="cursor-pointer bg-primary text-white px-6 md:px-8 {{ $sizeClasses[$size] }} rounded-lg font-semibold hover:bg-primary/90 transition-all whitespace-nowrap">
            {{ $buttonText }}
        </button>
    </div>

    {{ $slot }}
</form>
