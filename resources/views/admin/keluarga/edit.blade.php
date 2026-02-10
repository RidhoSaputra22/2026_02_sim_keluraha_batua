<x-layouts.app :title="'Edit Kartu Keluarga'">
    <x-slot:header>
        <x-layouts.page-header title="Edit Kartu Keluarga" description="Ubah data kartu keluarga {{ $keluarga->no_kk }}">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('admin.keluarga.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.keluarga.update', $keluarga) }}">
            @csrf @method('PUT')

            {{-- Informasi Kartu Keluarga --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Informasi Kartu Keluarga</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.input label="Nomor KK" name="no_kk" value="{{ old('no_kk', $keluarga->no_kk) }}" required maxlength="16" />
                <x-ui.input label="Nama Kepala Keluarga" name="nama_kepala_keluarga" value="{{ old('nama_kepala_keluarga', $keluarga->nama_kepala_keluarga) }}" required />
                <x-ui.input label="NIK Kepala Keluarga" name="nik_kepala_keluarga" value="{{ old('nik_kepala_keluarga', $keluarga->nik_kepala_keluarga) }}" maxlength="16" />
                <x-ui.input label="Jumlah Anggota Keluarga" name="jumlah_anggota_keluarga" type="number" value="{{ old('jumlah_anggota_keluarga', $keluarga->jumlah_anggota_keluarga) }}" min="1" />
            </div>

            {{-- Alamat --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Alamat</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="md:col-span-2">
                    <x-ui.textarea label="Alamat Lengkap" name="alamat" value="{{ old('alamat', $keluarga->alamat) }}" />
                </div>
                <x-ui.input label="RT" name="rt" value="{{ old('rt', $keluarga->rt) }}" />
                <x-ui.input label="RW" name="rw" value="{{ old('rw', $keluarga->rw) }}" />
                <x-ui.input label="Kelurahan" name="kelurahan" value="{{ old('kelurahan', $keluarga->kelurahan) }}" />
                <x-ui.input label="Kecamatan" name="kecamatan" value="{{ old('kecamatan', $keluarga->kecamatan) }}" />
            </div>

            {{-- Status --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Status Data</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.select label="Status" name="status" :options="['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif']" selected="{{ old('status', $keluarga->status) }}" />
                <x-ui.textarea label="Keterangan / Arsip" name="arsip" value="{{ old('arsip', $keluarga->arsip) }}" />
            </div>

            <div class="flex justify-end gap-2 mt-6 border-t pt-4">
                <x-ui.button type="ghost" href="{{ route('admin.keluarga.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary">Update Data Keluarga</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
