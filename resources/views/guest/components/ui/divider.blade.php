{{--
    Divider Component

    Usage:
    <x-guest::ui.divider />
    <x-guest::ui.divider text="Atau" />
--}}

@props([
    'text' => null,
])

<div class="flex items-center gap-4 py-8 {{ $attributes->get('class', '') }}">
    <div class="h-px flex-grow bg-slate-200"></div>

    @if($text)
        <span class="text-slate-400 font-medium text-sm tracking-widest uppercase">{{ $text }}</span>
    @endif

    <div class="h-px flex-grow bg-slate-200"></div>
</div>
