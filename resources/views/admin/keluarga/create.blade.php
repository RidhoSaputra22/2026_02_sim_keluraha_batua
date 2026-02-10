<x-layouts.app :title="'Tambah Kartu Keluarga'">
    <x-slot:header>
        <x-layouts.page-header title="Tambah Kartu Keluarga" description="Tambah data kartu keluarga baru">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('admin.keluarga.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.keluarga.store') }}">
            @csrf

            {{-- Informasi Kartu Keluarga --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Informasi Kartu Keluarga</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.input label="Nomor KK" name="no_kk" placeholder="Masukkan 16 digit No. KK" value="{{ old('no_kk') }}" required maxlength="16" />
                <x-ui.input label="Nama Kepala Keluarga" name="nama_kepala_keluarga" placeholder="Masukkan nama kepala keluarga" value="{{ old('nama_kepala_keluarga') }}" required />
                <x-ui.input label="NIK Kepala Keluarga" name="nik_kepala_keluarga" placeholder="Masukkan 16 digit NIK" value="{{ old('nik_kepala_keluarga') }}" maxlength="16" />
                <x-ui.input label="Jumlah Anggota Keluarga" name="jumlah_anggota_keluarga" type="number" placeholder="0" value="{{ old('jumlah_anggota_keluarga') }}" min="1" />
            </div>

            {{-- Alamat --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Alamat</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="md:col-span-2">
                    <x-ui.textarea label="Alamat Lengkap" name="alamat" placeholder="Masukkan alamat lengkap" value="{{ old('alamat') }}" />
                </div>
                <x-ui.input label="RT" name="rt" placeholder="Contoh: 001" value="{{ old('rt') }}" />
                <x-ui.input label="RW" name="rw" placeholder="Contoh: 001" value="{{ old('rw') }}" />
                <x-ui.input label="Kelurahan" name="kelurahan" placeholder="Kelurahan" value="{{ old('kelurahan', 'Batua') }}" />
                <x-ui.input label="Kecamatan" name="kecamatan" placeholder="Kecamatan" value="{{ old('kecamatan', 'Manggala') }}" />
            </div>

            {{-- Status --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Status Data</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.select label="Status" name="status" :options="['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif']" selected="{{ old('status', 'aktif') }}" />
                <x-ui.textarea label="Keterangan / Arsip" name="arsip" placeholder="Keterangan tambahan..." value="{{ old('arsip') }}" />
            </div>

            <div class="flex justify-end gap-2 mt-6 border-t pt-4">
                <x-ui.button type="ghost" href="{{ route('admin.keluarga.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary">Simpan Data Keluarga</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
