@php
    $publishedAt = old('published_at', isset($dokumenPublik) && $dokumenPublik->published_at ? $dokumenPublik->published_at->format('Y-m-d\TH:i') : '');
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="space-y-6">
        <x-ui.card title="Status Dokumen">
            <input type="hidden" name="is_published" value="0">
            <label class="label cursor-pointer justify-start gap-3">
                <input type="checkbox" name="is_published" value="1" class="checkbox checkbox-primary"
                    @checked((bool) old('is_published', isset($dokumenPublik) ? $dokumenPublik->is_published : true))>
                <span class="label-text">Tampilkan di website</span>
            </label>

            <div class="mt-4">
                <x-ui.input name="published_at" type="datetime-local" label="Tanggal Publikasi" value="{{ $publishedAt }}" />
            </div>
        </x-ui.card>

        <x-ui.card title="Dokumen File">
            @if (isset($dokumenPublik) && $dokumenPublik->file_path)
                <p class="text-sm mb-3">
                    File saat ini:
                    <a href="{{ asset('storage/' . $dokumenPublik->file_path) }}" class="link link-primary" target="_blank" rel="noreferrer">Unduh dokumen</a>
                </p>
            @endif
            <input type="file" name="file_dokumen" class="file-input file-input-bordered w-full">
            <p class="text-xs text-base-content/60 mt-2">PDF, Word, Excel, PowerPoint, atau gambar. Maksimal 10MB.</p>
            @error('file_dokumen')
                <p class="text-error text-xs mt-2">{{ $message }}</p>
            @enderror
        </x-ui.card>

        <x-ui.card title="Cover (Opsional)">
            @if (isset($dokumenPublik) && $dokumenPublik->cover_image)
                <div class="mb-4 rounded-lg overflow-hidden bg-base-200">
                    <img src="{{ asset('storage/' . $dokumenPublik->cover_image) }}" alt="{{ $dokumenPublik->judul }}"
                        class="w-full h-40 object-cover">
                </div>
            @endif
            <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp"
                class="file-input file-input-bordered w-full">
            @error('cover_image')
                <p class="text-error text-xs mt-2">{{ $message }}</p>
            @enderror
        </x-ui.card>
    </div>

    <div class="lg:col-span-2 space-y-6">
        <x-ui.card title="Informasi Dokumen">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <x-ui.input name="judul" label="Judul Dokumen" value="{{ old('judul', $dokumenPublik->judul ?? '') }}" required />
                </div>
                <x-ui.select name="kategori" label="Kategori" :options="\App\Models\DokumenPublik::kategoriOptions()"
                    selected="{{ old('kategori', $dokumenPublik->kategori ?? 'transparansi') }}" required />
                <x-ui.input name="slug" label="Slug (Opsional)" value="{{ old('slug', $dokumenPublik->slug ?? '') }}" />
                <div class="md:col-span-2">
                    <x-ui.textarea name="deskripsi" label="Deskripsi Dokumen" rows="5"
                        value="{{ old('deskripsi', $dokumenPublik->deskripsi ?? '') }}" />
                </div>
            </div>
        </x-ui.card>
    </div>
</div>

<div class="mt-6 flex justify-end gap-2">
    <a href="{{ route('admin.website.dokumen-publik.index') }}" class="btn btn-ghost">Batal</a>
    <x-ui.button type="primary">{{ isset($dokumenPublik) ? 'Perbarui' : 'Simpan' }}</x-ui.button>
</div>
