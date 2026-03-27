<x-layouts.app :title="'Edit Profil Kelurahan ' . $kelurahan->nama">
    <x-slot:header>
        <x-layouts.page-header title="Edit Profil Kelurahan {{ $kelurahan->nama }}" description="Perbarui biodata dan profil kelurahan">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('master.profil-wilayah.kelurahan.show', $kelurahan) }}">
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <form method="POST" action="{{ route('master.profil-wilayah.kelurahan.update', $kelurahan) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Kolom Kiri: Foto --}}
            <div class="lg:col-span-1 space-y-6">
                <x-ui.card title="Foto Kelurahan">
                    <div x-data="{ preview: '{{ $kelurahan->foto ? asset('storage/' . $kelurahan->foto) : '' }}' }">
                        <div class="w-full aspect-video rounded-lg overflow-hidden bg-base-200 mb-3">
                            <template x-if="preview">
                                <img :src="preview" alt="Preview Foto" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!preview">
                                <div class="flex items-center justify-center h-full text-base-content/40">
                                    <div class="text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        <p class="text-sm">Upload foto</p>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <input type="file" name="foto" accept="image/jpeg,image/png,image/webp"
                            class="file-input file-input-bordered file-input-sm w-full"
                            @change="if ($event.target.files[0]) { preview = URL.createObjectURL($event.target.files[0]) }">
                        <p class="text-xs text-base-content/50 mt-1">Format: JPG, PNG, WebP. Maks 2MB.</p>

                        @error('foto')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    @if($kelurahan->foto)
                        <div class="mt-3">
                            <button type="button" class="btn btn-error btn-outline btn-xs"
                                @click="if(confirm('Hapus foto kelurahan?')) { $refs.deleteFoto.submit() }">
                                Hapus Foto
                            </button>
                        </div>
                    @endif
                </x-ui.card>

                {{-- Pimpinan --}}
                <x-ui.card title="Data Lurah">
                    <div class="space-y-3">
                        <x-ui.input name="nama_lurah" label="Nama Lurah" :value="$kelurahan->nama_lurah" placeholder="Nama lengkap lurah" :error="$errors->first('nama_lurah')" />
                        <x-ui.input name="nip_lurah" label="NIP Lurah" :value="$kelurahan->nip_lurah" placeholder="NIP lurah" :error="$errors->first('nip_lurah')" />
                    </div>
                </x-ui.card>
            </div>

            {{-- Kolom Kanan: Detail --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Info Umum --}}
                <x-ui.card title="Informasi Umum">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-ui.input name="kode_pos" label="Kode Pos" :value="$kelurahan->kode_pos" placeholder="Kode pos" :error="$errors->first('kode_pos')" />
                        <x-ui.input name="luas_area" label="Luas Area (km²)" type="number" :value="$kelurahan->luas_area" placeholder="0.00" step="0.01" :error="$errors->first('luas_area')" />
                        <div class="md:col-span-2">
                            <x-ui.input name="alamat_kantor" label="Alamat Kantor" :value="$kelurahan->alamat_kantor" placeholder="Alamat kantor kelurahan" :error="$errors->first('alamat_kantor')" />
                        </div>
                        <x-ui.input name="no_telp" label="Telepon" :value="$kelurahan->no_telp" placeholder="No. telepon kantor" :error="$errors->first('no_telp')" />
                        <x-ui.input name="email" label="Email" type="email" :value="$kelurahan->email" placeholder="email@domain.com" :error="$errors->first('email')" />
                        <div class="md:col-span-2">
                            <x-ui.input name="website" label="Website" :value="$kelurahan->website" placeholder="https://..." :error="$errors->first('website')" />
                        </div>
                    </div>
                </x-ui.card>

                {{-- Batas Wilayah --}}
                <x-ui.card title="Batas Wilayah">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-ui.input name="batas_utara" label="Batas Utara" :value="$kelurahan->batas_utara" placeholder="Kelurahan/desa di utara" :error="$errors->first('batas_utara')" />
                        <x-ui.input name="batas_selatan" label="Batas Selatan" :value="$kelurahan->batas_selatan" placeholder="Kelurahan/desa di selatan" :error="$errors->first('batas_selatan')" />
                        <x-ui.input name="batas_timur" label="Batas Timur" :value="$kelurahan->batas_timur" placeholder="Kelurahan/desa di timur" :error="$errors->first('batas_timur')" />
                        <x-ui.input name="batas_barat" label="Batas Barat" :value="$kelurahan->batas_barat" placeholder="Kelurahan/desa di barat" :error="$errors->first('batas_barat')" />
                    </div>
                </x-ui.card>

                {{-- Visi & Misi --}}
                <x-ui.card title="Visi & Misi">
                    <div class="space-y-4">
                        <x-ui.textarea name="visi" label="Visi" :value="$kelurahan->visi" placeholder="Visi kelurahan" rows="3" :error="$errors->first('visi')" />
                        <x-ui.textarea name="misi" label="Misi" :value="$kelurahan->misi" placeholder="Misi kelurahan (satu per baris)" rows="5" :error="$errors->first('misi')" />
                    </div>
                </x-ui.card>

                {{-- Deskripsi --}}
                <x-ui.card title="Deskripsi">
                    <x-ui.textarea name="deskripsi" label="Deskripsi Kelurahan" :value="$kelurahan->deskripsi" placeholder="Deskripsi umum tentang kelurahan" rows="5" :error="$errors->first('deskripsi')" />
                </x-ui.card>

                {{-- Tombol --}}
                <div class="flex justify-end gap-3">
                    <x-ui.button type="ghost" href="{{ route('master.profil-wilayah.kelurahan.show', $kelurahan) }}">Batal</x-ui.button>
                    <x-ui.button type="primary" :isSubmit="true">Simpan Perubahan</x-ui.button>
                </div>
            </div>
        </div>
    </form>

    {{-- Form hapus foto (hidden) --}}
    @if($kelurahan->foto)
    <form x-ref="deleteFoto" method="POST" action="{{ route('master.profil-wilayah.kelurahan.delete-foto', $kelurahan) }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>
    @endif
</x-layouts.app>
