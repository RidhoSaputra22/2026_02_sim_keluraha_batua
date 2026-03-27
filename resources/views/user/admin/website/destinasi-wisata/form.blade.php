<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="space-y-6">
        <x-ui.card title="Status Destinasi">
            <div class="space-y-4">
                <div>
                    <input type="hidden" name="is_published" value="0">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" name="is_published" value="1" class="checkbox checkbox-primary"
                            @checked((bool) old('is_published', isset($destinasiWisata) ? $destinasiWisata->is_published : true))>
                        <span class="label-text">Tampilkan di website</span>
                    </label>
                </div>

                <div>
                    <input type="hidden" name="is_featured" value="0">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" name="is_featured" value="1" class="checkbox checkbox-primary"
                            @checked((bool) old('is_featured', isset($destinasiWisata) ? $destinasiWisata->is_featured : false))>
                        <span class="label-text">Tandai sebagai rekomendasi</span>
                    </label>
                </div>

                <x-ui.input name="sort_order" type="number" label="Urutan Tampil" value="{{ old('sort_order', $destinasiWisata->sort_order ?? '') }}" />
            </div>
        </x-ui.card>

        <x-ui.card title="Foto Destinasi">
            @if (isset($destinasiWisata) && $destinasiWisata->gambar)
                <div class="mb-4 rounded-lg overflow-hidden bg-base-200">
                    <img src="{{ asset('storage/' . $destinasiWisata->gambar) }}" alt="{{ $destinasiWisata->nama }}"
                        class="w-full h-48 object-cover">
                </div>
            @endif

            <input type="file" name="gambar" accept="image/jpeg,image/png,image/webp"
                class="file-input file-input-bordered w-full">
            @error('gambar')
                <p class="text-error text-xs mt-2">{{ $message }}</p>
            @enderror
        </x-ui.card>
    </div>

    <div class="lg:col-span-2 space-y-6">
        <x-ui.card title="Informasi Destinasi">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <x-ui.input name="nama" label="Nama Destinasi" value="{{ old('nama', $destinasiWisata->nama ?? '') }}" required />
                </div>
                <x-ui.select name="kategori" label="Kategori" :options="\App\Models\DestinasiWisata::kategoriOptions()"
                    selected="{{ old('kategori', $destinasiWisata->kategori ?? 'wisata') }}" required />
                <x-ui.input name="slug" label="Slug (Opsional)" value="{{ old('slug', $destinasiWisata->slug ?? '') }}" />
                <div class="md:col-span-2">
                    <x-ui.textarea name="ringkasan" label="Ringkasan" rows="4"
                        value="{{ old('ringkasan', $destinasiWisata->ringkasan ?? '') }}" required />
                </div>
                <div class="md:col-span-2">
                    <x-ui.textarea name="deskripsi" label="Deskripsi Lengkap" rows="8"
                        value="{{ old('deskripsi', $destinasiWisata->deskripsi ?? '') }}" />
                </div>
                <div class="md:col-span-2">
                    <x-ui.input name="alamat" label="Alamat" value="{{ old('alamat', $destinasiWisata->alamat ?? '') }}" />
                </div>
                <x-ui.input name="jam_operasional" label="Jam Operasional" value="{{ old('jam_operasional', $destinasiWisata->jam_operasional ?? '') }}" />
                <x-ui.input name="harga_tiket" label="Harga / Tiket" value="{{ old('harga_tiket', $destinasiWisata->harga_tiket ?? '') }}" />
                <x-ui.input name="kontak" label="Kontak" value="{{ old('kontak', $destinasiWisata->kontak ?? '') }}" />
                <x-ui.input name="maps_url" label="Link Google Maps" value="{{ old('maps_url', $destinasiWisata->maps_url ?? '') }}" />
            </div>
        </x-ui.card>
    </div>
</div>

<div class="mt-6 flex justify-end gap-2">
    <a href="{{ route('admin.website.destinasi-wisata.index') }}" class="btn btn-ghost">Batal</a>
    <x-ui.button type="primary">{{ isset($destinasiWisata) ? 'Perbarui' : 'Simpan' }}</x-ui.button>
</div>
