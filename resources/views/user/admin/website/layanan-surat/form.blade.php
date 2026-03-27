@php
    $persyaratanValue = old(
        'persyaratan',
        isset($layananSurat)
            ? $layananSurat->persyaratans->map(fn ($item) => [
                'nama' => $item->nama,
                'keterangan' => $item->keterangan,
                'is_required' => $item->is_required,
            ])->values()->all()
            : [['nama' => '', 'keterangan' => '', 'is_required' => true]]
    );
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="space-y-6">
        <x-ui.card title="Status Layanan">
            <input type="hidden" name="is_active" value="0">
            <label class="label cursor-pointer justify-start gap-3">
                <input type="checkbox" name="is_active" value="1" class="checkbox checkbox-primary"
                    @checked((bool) old('is_active', isset($layananSurat) ? $layananSurat->is_active : true))>
                <span class="label-text">Aktif di halaman surat online</span>
            </label>

            <div class="mt-4 space-y-4">
                <x-ui.input name="icon" label="Nama Icon (Material)" value="{{ old('icon', $layananSurat->icon ?? 'description') }}" />
                <x-ui.input name="sort_order" type="number" label="Urutan Tampil" value="{{ old('sort_order', $layananSurat->sort_order ?? '') }}" />
            </div>
        </x-ui.card>

        <x-ui.card title="Informasi Tambahan">
            <div class="space-y-4">
                <x-ui.input name="estimasi_layanan" label="Estimasi Layanan" value="{{ old('estimasi_layanan', $layananSurat->estimasi_layanan ?? '') }}"
                    placeholder="Contoh: 1-2 hari kerja" />
                <x-ui.input name="biaya" label="Biaya" value="{{ old('biaya', $layananSurat->biaya ?? '') }}"
                    placeholder="Contoh: Gratis" />
                <x-ui.input name="kontak_petugas" label="Kontak Petugas" value="{{ old('kontak_petugas', $layananSurat->kontak_petugas ?? '') }}"
                    placeholder="WA / telepon petugas" />
            </div>
        </x-ui.card>
    </div>

    <div class="lg:col-span-2 space-y-6">
        <x-ui.card title="Informasi Layanan">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <x-ui.input name="nama" label="Nama Layanan Surat" value="{{ old('nama', $layananSurat->nama ?? '') }}" required />
                </div>
                <x-ui.input name="slug" label="Slug (Opsional)" value="{{ old('slug', $layananSurat->slug ?? '') }}" />
                <div class="md:col-span-2">
                    <x-ui.textarea name="deskripsi" label="Deskripsi Singkat" rows="4"
                        value="{{ old('deskripsi', $layananSurat->deskripsi ?? '') }}" />
                </div>
                <div class="md:col-span-2">
                    <x-ui.textarea name="catatan" label="Catatan Tambahan" rows="4"
                        value="{{ old('catatan', $layananSurat->catatan ?? '') }}"
                        placeholder="Informasi penting yang perlu dibaca warga sebelum datang ke kantor kelurahan." />
                </div>
            </div>
        </x-ui.card>

        <x-ui.card title="Daftar Persyaratan">
            <div x-data="{ persyaratan: @js(array_values($persyaratanValue)) }" class="space-y-4">
                <template x-for="(item, index) in persyaratan" :key="index">
                    <div class="border border-base-300 rounded-lg p-4 space-y-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1">
                                <label class="label">
                                    <span class="label-text font-medium">Persyaratan</span>
                                </label>
                                <textarea class="textarea textarea-bordered w-full"
                                    :name="`persyaratan[${index}][nama]`"
                                    x-model="item.nama" rows="3"
                                    placeholder="Contoh: Fotokopi KTP pemohon"></textarea>
                            </div>
                            <button type="button" class="btn btn-error btn-outline btn-sm mt-9"
                                @click="persyaratan.splice(index, 1)"
                                x-show="persyaratan.length > 1">
                                Hapus
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="label">
                                    <span class="label-text">Keterangan</span>
                                </label>
                                <input type="text" class="input input-bordered w-full"
                                    :name="`persyaratan[${index}][keterangan]`"
                                    x-model="item.keterangan" placeholder="Opsional" />
                            </div>
                            <div class="flex items-end">
                                <div>
                                    <input type="hidden" :name="`persyaratan[${index}][is_required]`" value="0">
                                    <label class="label cursor-pointer justify-start gap-3">
                                        <input type="checkbox" class="checkbox checkbox-primary" value="1"
                                            :name="`persyaratan[${index}][is_required]`"
                                            :checked="item.is_required"
                                            @change="item.is_required = $event.target.checked">
                                        <span class="label-text">Wajib dilampirkan</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <button type="button" class="btn btn-outline btn-primary"
                    @click="persyaratan.push({ nama: '', keterangan: '', is_required: true })">
                    Tambah Persyaratan
                </button>

                @error('persyaratan')
                    <p class="text-error text-sm">{{ $message }}</p>
                @enderror
            </div>
        </x-ui.card>
    </div>
</div>

<div class="mt-6 flex justify-end gap-2">
    <a href="{{ route('admin.website.layanan-surat.index') }}" class="btn btn-ghost">Batal</a>
    <x-ui.button type="primary">{{ isset($layananSurat) ? 'Perbarui' : 'Simpan' }}</x-ui.button>
</div>
