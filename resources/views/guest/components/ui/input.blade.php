{{--
    Input Component

    Usage:
    <x-guest::ui.input
        name="email"
        type="email"
        label="Email Address"
        placeholder="Enter your email"
        :required="true"
    />
--}}

@props([
    'type' => 'text',
    'name' => '',
    'label' => '',
    'placeholder' => '',
    'value' => '',
    'required' => false,
    'error' => null,
    'icon' => null,
])

<div class="space-y-2">
    @if($label)
        <label for="{{ $name }}" class="text-sm font-semibold text-slate-700">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="material-icons text-slate-400">{{ $icon }}</span>
            </div>
        @endif

        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            class="w-full {{ $icon ? 'pl-10' : 'pl-4' }} pr-4 py-3 rounded-lg border {{ $error ? 'border-red-500' : 'border-slate-200' }} focus:border-primary focus:ring-1 focus:ring-primary transition-all"
            {{ $attributes }}
        />
    </div>

    @if($error)
        <p class="text-sm text-red-500 flex items-center gap-1">
            <span class="material-icons text-sm">error</span>
            {{ $error }}
        </p>
    @endif
</div>
