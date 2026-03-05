<x-layouts.app :title="'Edit RT ' . str_pad($rt->nomor, 3, '0', STR_PAD_LEFT)">
    <x-slot:header>
        <x-layouts.page-header title="Edit RT {{ str_pad($rt->nomor, 3, '0', STR_PAD_LEFT) }}" description="RW {{ str_pad($rt->rw->nomor, 3, '0', STR_PAD_LEFT) }} — Kel. {{ $rt->rw->kelurahan->nama ?? '-' }}">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('master.rt.show', $rt) }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <form method="POST" action="{{ route('master.rt.update', $rt) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Kolom Kiri: Foto --}}
            <div class="lg:col-span-1 space-y-6">
                <x-ui.card title="Foto RT">
                    <div x-data="{ preview: '{{ $rt->foto ? asset('storage/' . $rt->foto) : '' }}' }">
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

                    @if($rt->foto)
                        <div class="mt-3">
                            <button type="button" class="btn btn-error btn-outline btn-xs"
                                @click="if(confirm('Hapus foto RT?')) { $refs.deleteFoto.submit() }">
                                Hapus Foto
                            </button>
                        </div>
                    @endif
                </x-ui.card>
            </div>

            {{-- Kolom Kanan --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Data Dasar --}}
                <x-ui.card title="Data Dasar">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-ui.select label="RW" name="rw_id" required :options="$rwList->mapWithKeys(fn($r) => [$r->id => 'RW ' . str_pad($r->nomor, 3, '0', STR_PAD_LEFT) . ' — ' . ($r->kelurahan->nama ?? '')])->toArray()" selected="{{ old('rw_id', $rt->rw_id) }}" />
                        <x-ui.input label="Nomor RT" name="nomor" type="number" required :value="old('nomor', $rt->nomor)" min="1" :error="$errors->first('nomor')" />
                    </div>
                </x-ui.card>

                {{-- Info Umum --}}
                <x-ui.card title="Informasi Umum">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-ui.input name="luas_area" label="Luas Area (km²)" type="number" :value="old('luas_area', $rt->luas_area)" placeholder="0.00" step="0.01" :error="$errors->first('luas_area')" />
                        <x-ui.input name="no_telp" label="Telepon" :value="old('no_telp', $rt->no_telp)" placeholder="No. telepon" :error="$errors->first('no_telp')" />
                        <div class="md:col-span-2">
                            <x-ui.input name="alamat_pos" label="Alamat Pos" :value="old('alamat_pos', $rt->alamat_pos)" placeholder="Alamat pos RT" :error="$errors->first('alamat_pos')" />
                        </div>
                    </div>
                </x-ui.card>

                {{-- Deskripsi & Fasilitas --}}
                <x-ui.card title="Deskripsi & Fasilitas">
                    <div class="space-y-4">
                        <x-ui.textarea name="deskripsi" label="Deskripsi" :value="old('deskripsi', $rt->deskripsi)" placeholder="Deskripsi umum tentang RT" rows="4" />
                        <x-ui.textarea name="fasilitas" label="Fasilitas Umum" :value="old('fasilitas', $rt->fasilitas)" placeholder="Daftar fasilitas umum di RT (satu per baris)" rows="4" />
                    </div>
                </x-ui.card>

                {{-- Tombol --}}
                <div class="flex justify-end gap-3">
                    <x-ui.button type="ghost" href="{{ route('master.rt.show', $rt) }}">Batal</x-ui.button>
                    <x-ui.button type="primary" :isSubmit="true">Simpan Perubahan</x-ui.button>
                </div>
            </div>
        </div>
    </form>

    {{-- Form hapus foto (hidden) --}}
    @if($rt->foto)
    <form x-ref="deleteFoto" method="POST" action="{{ route('master.rt.delete-foto', $rt) }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>
    @endif
</x-layouts.app>
