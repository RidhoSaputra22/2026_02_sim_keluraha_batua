{{--
    Stat Card Component

    Usage:
    <x-guest::ui.stat-card
        title="Total Penduduk"
        value="5,432"
        icon="people"
        trend="+12%"
    />
--}}

@props([
    'title' => '',
    'value' => '',
    'icon' => null,
    'trend' => null,
    'description' => '',
    'color' => 'primary', // primary, success, warning, danger, info
    'counterValue' => null,
    'counterSuffix' => '',
    'counterDecimals' => 0,
    'counterDuration' => 850,
])

@php
    $colorClasses = [
        'primary' => 'bg-primary/10 text-primary',
        'success' => 'bg-green-100 text-green-600',
        'warning' => 'bg-yellow-100 text-yellow-600',
        'danger' => 'bg-red-100 text-red-600',
        'info' => 'bg-blue-100 text-blue-600',
    ];
@endphp

<div {{ $attributes->merge(['data-aos' => 'zoom-in'])->class(['bg-white p-6 rounded-xl shadow-sm border border-slate-200 hover:shadow-lg transition-shadow']) }}>
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <p class="text-sm font-medium text-slate-500 mb-2">{{ $title }}</p>
            <p class="text-3xl font-bold text-slate-900 mb-1">
                @if (! is_null($counterValue))
                    <span data-counter data-counter-end="{{ $counterValue }}"
                        data-counter-decimals="{{ $counterDecimals }}"
                        data-counter-suffix="{{ $counterSuffix }}"
                        data-counter-duration="{{ $counterDuration }}">
                        {{ $value }}
                    </span>
                @else
                    {{ $value }}
                @endif
            </p>

            @if($description)
                <p class="text-xs text-slate-500">{{ $description }}</p>
            @endif

            @if($trend)
                <div class="mt-2 inline-flex items-center gap-1 text-sm font-semibold {{ str_starts_with($trend, '+') ? 'text-green-600' : 'text-red-600' }}">
                    <span class="material-icons text-sm">{{ str_starts_with($trend, '+') ? 'trending_up' : 'trending_down' }}</span>
                    {{ $trend }}
                </div>
            @endif
        </div>

        @if($icon)
            <div class="w-12 h-12 rounded-lg {{ $colorClasses[$color] }} flex items-center justify-center flex-shrink-0">
                <span class="material-icons">{{ $icon }}</span>
            </div>
        @endif
    </div>

    {{ $slot }}
</div>
