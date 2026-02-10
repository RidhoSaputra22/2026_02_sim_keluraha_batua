<x-layouts.app :title="'Tambah Data RT/RW'">
    <x-slot:header>
        <x-layouts.page-header title="Tambah Data RT/RW" description="Tambah data ketua RT atau RW baru">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('admin.wilayah.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.wilayah.store') }}">
            @csrf

            {{-- Data Pribadi --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Data Pribadi</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.input label="NIK" name="nik" placeholder="Masukkan 16 digit NIK" value="{{ old('nik') }}" maxlength="16" />
                <x-ui.input label="Nama Lengkap" name="nama" placeholder="Masukkan nama lengkap" value="{{ old('nama') }}" required />
                <x-ui.input label="No. Telepon" name="no_telp" placeholder="08xxxxxxxxxx" value="{{ old('no_telp') }}" />
                <x-ui.input label="Email" name="email" type="email" placeholder="email@contoh.com" value="{{ old('email') }}" />
                <div class="md:col-span-2">
                    <x-ui.textarea label="Alamat" name="alamat" placeholder="Alamat lengkap" value="{{ old('alamat') }}" />
                </div>
            </div>

            {{-- Data Jabatan --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Data Jabatan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                <x-ui.select label="Jabatan" name="jabatan" :options="['RT' => 'Ketua RT', 'RW' => 'Ketua RW']" selected="{{ old('jabatan') }}" required />
                <x-ui.input label="RT" name="rt" placeholder="Contoh: 001" value="{{ old('rt') }}" />
                <x-ui.input label="RW" name="rw" placeholder="Contoh: 001" value="{{ old('rw') }}" />
                <x-ui.input label="Kelurahan" name="kelurahan" value="{{ old('kelurahan', 'Batua') }}" />
                <x-ui.input label="Kecamatan" name="kecamatan" value="{{ old('kecamatan', 'Manggala') }}" />
                <x-ui.input label="Periode" name="periode" placeholder="Contoh: 2024-2029" value="{{ old('periode') }}" />
                <x-ui.input label="Tanggal Mulai" name="tgl_mulai" type="date" value="{{ old('tgl_mulai') }}" />
                <x-ui.input label="Tanggal Selesai" name="tgl_selesai" type="date" value="{{ old('tgl_selesai') }}" />
                <x-ui.select label="Status" name="status" :options="['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif']" selected="{{ old('status', 'aktif') }}" />
            </div>

            {{-- Data Tambahan --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Data Tambahan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.input label="No. Rekening" name="no_rekening" placeholder="Nomor rekening" value="{{ old('no_rekening') }}" />
                <x-ui.input label="No. NPWP" name="no_npwp" placeholder="Nomor NPWP" value="{{ old('no_npwp') }}" />
                <div class="md:col-span-2">
                    <x-ui.textarea label="Keterangan" name="keterangan" placeholder="Keterangan tambahan..." value="{{ old('keterangan') }}" />
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6 border-t pt-4">
                <x-ui.button type="ghost" href="{{ route('admin.wilayah.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary">Simpan Data RT/RW</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
