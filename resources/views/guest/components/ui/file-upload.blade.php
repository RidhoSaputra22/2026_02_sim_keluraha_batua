{{--
    File Upload Component

    Usage:
    <x-guest::ui.file-upload
        name="document"
        label="Upload KTP"
        accept=".jpg,.png,.pdf"
    />
--}}

@props([
'name' => 'file',
'label' => 'Unggah File',
'accept' => '*',
'maxSize' => '2MB',
'description' => 'Format: JPG, PNG, atau PDF',
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

    <div
        class="border-2 border-dashed {{ $error ? 'border-red-500' : 'border-slate-200' }} rounded-xl p-8 flex flex-col items-center justify-center bg-slate-50 hover:border-primary/50 transition-colors group cursor-pointer">
        <input type="file" name="{{ $name }}" id="{{ $name }}" accept="{{ $accept }}" {{ $required ? 'required' : '' }}
            class="hidden" {{ $attributes }} />

        <label for="{{ $name }}" class="cursor-pointer text-center w-full">
            <div
                class="bg-white p-3 rounded-full shadow-sm mb-4 group-hover:scale-110 transition-transform inline-flex">
                <x-guest::ui.icon name="upload_file" size="2xl" color="text-primary" />
            </div>

            <p class="text-slate-600 font-medium">Klik untuk telusuri berkas</p>

            @if($description)
            <p class="text-slate-400 text-xs mt-2">
                {{ $description }} (Maksimal {{ $maxSize }})
            </p>
            @endif
        </label>
    </div>

    @if($error)
    <p class="text-sm text-red-500 flex items-center gap-1">
        <span class="material-icons text-sm">error</span>
        {{ $error }}
    </p>
    @endif
</div>