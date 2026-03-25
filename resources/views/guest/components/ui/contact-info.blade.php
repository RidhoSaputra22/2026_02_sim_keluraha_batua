{{--
    Contact Info Component

    Usage:
    <x-guest::ui.contact-info
        icon="place"
        title="Alamat"
        content="Jl. Merdeka No. 123"
    />
--}}

@props([
    'icon' => 'info',
    'title' => '',
    'content' => '',
    'link' => null,
])

<div class="flex items-start gap-4 group">
    <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0 group-hover:bg-primary group-hover:scale-110 transition-all">
        <span class="material-icons text-primary group-hover:text-white transition-colors">{{ $icon }}</span>
    </div>

    <div class="flex-1">
        @if($title)
            <h4 class="font-semibold text-slate-900 mb-1">{{ $title }}</h4>
        @endif

        @if($link)
            <a href="{{ $link }}" class="text-slate-600 hover:text-primary transition-colors">
                {{ $content }}
            </a>
        @else
            <p class="text-slate-600">{{ $content }}</p>
        @endif

        {{ $slot }}
    </div>
</div>
