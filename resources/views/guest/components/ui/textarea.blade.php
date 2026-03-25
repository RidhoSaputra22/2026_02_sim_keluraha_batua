{{--
    Textarea Component

    Usage:
    <x-guest::ui.textarea
        name="message"
        label="Your Message"
        placeholder="Type your message..."
        rows="4"
        :required="true"
    />
--}}

@props([
    'name' => '',
    'label' => '',
    'placeholder' => '',
    'value' => '',
    'rows' => 3,
    'required' => false,
    'error' => null,
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

    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        class="w-full p-3 rounded-lg border {{ $error ? 'border-red-500' : 'border-slate-200' }} focus:border-primary focus:ring-1 focus:ring-primary transition-all resize-none"
        {{ $attributes }}
    >{{ $value }}</textarea>

    @if($error)
        <p class="text-sm text-red-500 flex items-center gap-1">
            <span class="material-icons text-sm">error</span>
            {{ $error }}
        </p>
    @endif
</div>
