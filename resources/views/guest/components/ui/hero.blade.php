{{--
    Hero Section Component

    Usage:
    <x-guest::ui.hero
        title="Welcome to Our Site"
        subtitle="This is a great place"
        :centered="true"
    />

    With custom content:
    <x-guest::ui.hero>
        <x-slot:title>Custom <span class="text-primary">Title</span></x-slot:title>
        <x-slot:subtitle>Custom subtitle text</x-slot:subtitle>
        <x-slot:actions>
            <x-guest::ui.button>Get Started</x-guest::ui.button>
        </x-slot:actions>
    </x-guest::ui.hero>
--}}

@props([
    'title' => '',
    'subtitle' => '',
    'centered' => true,
    'background' => 'white', // white, gradient, transparent
    'size' => 'lg', // sm, md, lg, xl
])

@php
    $bgClasses = [
        'white' => 'bg-white',
        'gradient' => 'relative bg-white',
        'transparent' => 'bg-transparent',
    ];

    $sizeClasses = [
        'sm' => 'py-8 md:py-12',
        'md' => 'py-12 md:py-16',
        'lg' => 'py-12 md:py-20',
        'xl' => 'py-20 md:py-32',
    ];

    $alignClasses = $centered ? 'text-center' : 'text-left';
@endphp

<header class="{{ $bgClasses[$background] }} {{ $sizeClasses[$size] }} {{ $attributes->get('class', '') }}">
    @if($background === 'gradient')
        <div class="absolute inset-0 bg-primary/5 -z-10"></div>
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-primary/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-primary/10 rounded-full blur-3xl"></div>
    @endif

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 {{ $alignClasses }}">
        @if(isset($badge))
            <div class="mb-6 {{ $centered ? 'flex justify-center' : '' }}">
                {{ $badge }}
            </div>
        @endif

        <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-slate-900 mb-4 md:mb-6 leading-tight">
            @isset($title)
                {!! $title !!}
            @else
                {{ $title }}
            @endisset
        </h1>

        @if($subtitle || isset($subtitle))
            <div class="w-24 h-1.5 bg-primary {{ $centered ? 'mx-auto' : '' }} rounded-full mb-6"></div>
            <p class="text-lg md:text-xl text-slate-600 {{ $centered ? 'max-w-3xl mx-auto' : 'max-w-2xl' }} leading-relaxed">
                @isset($subtitle)
                    {{ $subtitle }}
                @else
                    {{ $subtitle }}
                @endisset
            </p>
        @endif

        @isset($actions)
            <div class="mt-8 md:mt-10 {{ $centered ? 'flex justify-center gap-4' : 'flex gap-4' }}">
                {{ $actions }}
            </div>
        @endisset

        {{ $slot }}
    </div>
</header>
