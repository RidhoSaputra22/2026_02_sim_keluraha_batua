{{--
    Select Component

    Usage:
    <x-guest::ui.select
        name="category"
        label="Category"
        :options="[
            ['value' => '1', 'label' => 'Option 1'],
            ['value' => '2', 'label' => 'Option 2'],
        ]"
        :required="true"
    />
--}}

@props([
    'name' => '',
    'label' => '',
    'options' => [],
    'placeholder' => 'Pilih Opsi',
    'selected' => '',
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

    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $required ? 'required' : '' }}
        class="w-full p-3 rounded-lg border {{ $error ? 'border-red-500' : 'border-slate-200' }} focus:border-primary focus:ring-1 focus:ring-primary transition-all appearance-none"
        {{ $attributes }}
    >
        <option value="" disabled {{ !$selected ? 'selected' : '' }}>{{ $placeholder }}</option>

        @foreach($options as $option)
            <option
                value="{{ $option['value'] }}"
                {{ $selected == $option['value'] ? 'selected' : '' }}
            >
                {{ $option['label'] }}
            </option>
        @endforeach
    </select>

    @if($error)
        <p class="text-sm text-red-500 flex items-center gap-1">
            <span class="material-icons text-sm">error</span>
            {{ $error }}
        </p>
    @endif
</div>
