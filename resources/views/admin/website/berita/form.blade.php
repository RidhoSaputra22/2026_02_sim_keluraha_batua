@php
    $publishedAt = old('published_at', isset($berita) && $berita->published_at ? $berita->published_at->format('Y-m-d\TH:i') : '');
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="space-y-6">
        <x-ui.card title="Status Publikasi">
            <div class="space-y-4">
                <div>
                    <input type="hidden" name="is_published" value="0">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" name="is_published" value="1" class="checkbox checkbox-primary"
                            @checked((bool) old('is_published', isset($berita) ? $berita->is_published : true))>
                        <span class="label-text">Tampilkan di website</span>
                    </label>
                </div>

                <div>
                    <input type="hidden" name="is_featured" value="0">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" name="is_featured" value="1" class="checkbox checkbox-primary"
                            @checked((bool) old('is_featured', isset($berita) ? $berita->is_featured : false))>
                        <span class="label-text">Tandai sebagai unggulan</span>
                    </label>
                </div>

                <x-ui.input name="published_at" type="datetime-local" label="Tanggal Tayang" value="{{ $publishedAt }}" />
            </div>
        </x-ui.card>

        <x-ui.card title="Gambar Utama">
            @if (isset($berita) && $berita->gambar)
                <div class="mb-4 rounded-lg overflow-hidden bg-base-200">
                    <img src="{{ asset('storage/' . $berita->gambar) }}" alt="{{ $berita->judul }}"
                        class="w-full h-48 object-cover">
                </div>
            @endif

            <input type="file" name="gambar" accept="image/jpeg,image/png,image/webp"
                class="file-input file-input-bordered w-full">
            <p class="text-xs text-base-content/60 mt-2">Format JPG, PNG, atau WebP. Maksimal 2MB.</p>
            @error('gambar')
                <p class="text-error text-xs mt-2">{{ $message }}</p>
            @enderror
        </x-ui.card>
    </div>

    <div class="lg:col-span-2 space-y-6">
        <x-ui.card title="Informasi Berita">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <x-ui.input name="judul" label="Judul Berita" value="{{ old('judul', $berita->judul ?? '') }}" required />
                </div>
                <x-ui.select name="kategori" label="Kategori" :options="\App\Models\Berita::kategoriOptions()"
                    selected="{{ old('kategori', $berita->kategori ?? 'berita') }}" required />
                <x-ui.input name="slug" label="Slug (Opsional)" value="{{ old('slug', $berita->slug ?? '') }}"
                    helpText="Kosongkan untuk generate otomatis" />
                <div class="md:col-span-2">
                    <x-ui.textarea name="ringkasan" label="Ringkasan" rows="3"
                        value="{{ old('ringkasan', $berita->ringkasan ?? '') }}" />
                </div>
            </div>
        </x-ui.card>

        <x-ui.card title="Isi Berita">
            <x-ui.textarea name="isi" label="Konten Lengkap" rows="16"
                value="{{ old('isi', $berita->isi ?? '') }}" required />
        </x-ui.card>
    </div>
</div>

<div class="mt-6 flex justify-end gap-2">
    <a href="{{ route('admin.website.berita.index') }}" class="btn btn-ghost">Batal</a>
    <x-ui.button type="primary">{{ isset($berita) ? 'Perbarui' : 'Simpan' }}</x-ui.button>
</div>
