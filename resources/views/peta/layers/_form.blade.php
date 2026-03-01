{{-- Shared form fields for PetaLayer create/edit --}}
@php
    $layer = $petaLayer ?? null;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    {{-- Nama --}}
    <x-ui.input
        name="nama"
        label="Nama Layer"
        placeholder="Contoh: Daerah Rawan Banjir"
        :value="$layer->nama ?? ''"
        :error="$errors->first('nama')"
        :required="true"
    />

    {{-- Warna --}}
    <div class="form-control w-full">
        <label class="label" for="warna">
            <span class="label-text">Warna Layer <span class="text-error">*</span></span>
        </label>
        <div class="flex items-center gap-3">
            <input type="color" id="warna" name="warna"
                value="{{ old('warna', $layer->warna ?? '#3b82f6') }}"
                class="w-12 h-10 rounded cursor-pointer border border-base-300">
            <input type="text" id="warna-hex" readonly
                value="{{ old('warna', $layer->warna ?? '#3b82f6') }}"
                class="input input-bordered input-sm w-28 font-mono">
        </div>
        @error('warna')
        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
        @enderror
    </div>

    {{-- Pattern Type --}}
    <x-ui.select
        name="pattern_type"
        label="Pola Arsir"
        :options="$patternTypes"
        :selected="old('pattern_type', $layer->pattern_type ?? 'solid')"
        :required="true"
        :searchable="false"
    />

    {{-- Fill Opacity --}}
    <div class="form-control w-full">
        <label class="label" for="fill_opacity">
            <span class="label-text">Opacity Isi (0 - 1) <span class="text-error">*</span></span>
        </label>
        <input type="range" name="fill_opacity" id="fill_opacity" min="0" max="1" step="0.05"
            value="{{ old('fill_opacity', $layer->fill_opacity ?? 0.30) }}"
            class="range range-primary range-sm"
            oninput="document.getElementById('opacity-val').textContent = this.value">
        <div class="flex justify-between text-xs text-base-content/50 mt-1">
            <span>Transparan</span>
            <span id="opacity-val">{{ old('fill_opacity', $layer->fill_opacity ?? 0.30) }}</span>
            <span>Solid</span>
        </div>
        @error('fill_opacity')
        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
        @enderror
    </div>

    {{-- Stroke Width --}}
    <x-ui.input
        name="stroke_width"
        label="Ketebalan Garis (px)"
        type="number"
        placeholder="2.0"
        :value="$layer->stroke_width ?? '2.0'"
        :error="$errors->first('stroke_width')"
        :required="true"
        min="0.5"
        max="10"
        step="0.5"
    />

    {{-- Sort Order --}}
    <x-ui.input
        name="sort_order"
        label="Urutan Tampil"
        type="number"
        placeholder="0"
        :value="$layer->sort_order ?? '0'"
        :error="$errors->first('sort_order')"
        min="0"
    />
</div>

{{-- Deskripsi --}}
<div class="mt-4">
    <x-ui.textarea
        name="deskripsi"
        label="Deskripsi"
        placeholder="Deskripsi singkat tentang layer ini..."
        :value="$layer->deskripsi ?? ''"
        :error="$errors->first('deskripsi')"
        :rows="3"
    />
</div>

{{-- Active toggle --}}
<div class="form-control mt-4">
    <label class="label cursor-pointer justify-start gap-3">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1"
            class="toggle toggle-primary"
            {{ old('is_active', $layer->is_active ?? true) ? 'checked' : '' }}>
        <span class="label-text">Layer aktif (ditampilkan di peta utama)</span>
    </label>
</div>

{{-- Preview card --}}
<div class="mt-6 p-4 border border-base-200 rounded-xl bg-base-200/50">
    <p class="text-xs font-semibold text-base-content/50 uppercase tracking-wider mb-2">Preview Warna</p>
    <div class="flex items-center gap-4">
        <div id="preview-swatch" class="w-20 h-12 rounded-lg border border-base-300 shadow-inner"
            style="background-color: {{ old('warna', $layer->warna ?? '#3b82f6') }}; opacity: {{ old('fill_opacity', $layer->fill_opacity ?? 0.30) + 0.3 }};">
        </div>
        <div class="text-sm text-base-content/60">
            Tampilan layer di peta akan menyerupai warna dan opacity yang dipilih.
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const colorInput = document.getElementById('warna');
    const hexDisplay = document.getElementById('warna-hex');
    const previewSwatch = document.getElementById('preview-swatch');
    const opacityInput = document.getElementById('fill_opacity');

    if (colorInput) {
        colorInput.addEventListener('input', function() {
            hexDisplay.value = this.value;
            if (previewSwatch) {
                previewSwatch.style.backgroundColor = this.value;
            }
        });
    }

    if (opacityInput && previewSwatch) {
        opacityInput.addEventListener('input', function() {
            previewSwatch.style.opacity = parseFloat(this.value) + 0.3;
        });
    }
});
</script>
