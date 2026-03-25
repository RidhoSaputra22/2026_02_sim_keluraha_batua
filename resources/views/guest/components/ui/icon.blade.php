{{--
    Icon Component

    Usage:
    <x-guest::ui.icon name="home" />
    <x-guest::ui.icon name="send" size="lg" color="text-primary" />
--}}

@props([
'name' => 'help',
'size' => 'md', // sm, md, lg, xl
'color' => 'text-primary', // text-primary, text-secondary, text-slate-500, text-inherit, etc.
])

@php

$sizeClass = match($size) {
'sm' => 'text-sm',
'md' => 'text-base',
'lg' => 'text-2xl',
'xl' => 'text-4xl',
default => 'text-base'
};
@endphp

<span class="material-icons {{ $sizeClass }} {{ $color == 'inherit' ? 'text-inherit' : $color }}"
    {{ $attributes }}>{{ $name }}</span>
