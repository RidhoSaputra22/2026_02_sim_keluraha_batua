{{--
    Section Header Component

    Usage:
    <x-guest::ui.section-header title="Our Services" />

    With subtitle:
    <x-guest::ui.section-header
        title="Our Services"
        subtitle="What we offer"
        :centered="true"
    />
--}}

@props([
    'title' => '',
    'subtitle' => '',
    'centered' => true,
    'size' => 'lg', // sm, md, lg
])

@php
    $alignClasses = $centered ? 'text-center' : 'text-left';

    $titleSizes = [
        'sm' => 'text-2xl md:text-3xl',
        'md' => 'text-3xl md:text-4xl',
        'lg' => 'text-4xl md:text-5xl',
    ];
@endphp

<div class="{{ $alignClasses }} mb-12 md:mb-16">
    <h2 class="{{ $titleSizes[$size] }} font-extrabold text-slate-900 mb-4">
        {{ $title }}
    </h2>

    <div class="w-24 h-1.5 bg-primary {{ $centered ? 'mx-auto' : '' }} rounded-full mb-6"></div>

    @if($subtitle)
        <p class="text-lg text-slate-600 {{ $centered ? 'max-w-2xl mx-auto' : 'max-w-2xl' }} leading-relaxed">
            {{ $subtitle }}
        </p>
    @endif

    {{ $slot }}
</div>
