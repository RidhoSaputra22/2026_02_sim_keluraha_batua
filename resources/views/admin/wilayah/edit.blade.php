<x-layouts.app :title="'Edit Data RT/RW'">
    <x-slot:header>
        <x-layouts.page-header title="Edit Data RT/RW" description="Ubah data {{ $wilayah->jabatan }} - {{ $wilayah->nama }}">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('admin.wilayah.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.wilayah.update', $wilayah) }}">
            @csrf @method('PUT')

            {{-- Data Pribadi --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Data Pribadi</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.input label="NIK" name="nik" value="{{ old('nik', $wilayah->nik) }}" maxlength="16" />
                <x-ui.input label="Nama Lengkap" name="nama" value="{{ old('nama', $wilayah->nama) }}" required />
                <x-ui.input label="No. Telepon" name="no_telp" value="{{ old('no_telp', $wilayah->no_telp) }}" />
                <x-ui.input label="Email" name="email" type="email" value="{{ old('email', $wilayah->email) }}" />
                <div class="md:col-span-2">
                    <x-ui.textarea label="Alamat" name="alamat" value="{{ old('alamat', $wilayah->alamat) }}" />
                </div>
            </div>

            {{-- Data Jabatan --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Data Jabatan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                <x-ui.select label="Jabatan" name="jabatan" :options="['RT' => 'Ketua RT', 'RW' => 'Ketua RW']" selected="{{ old('jabatan', $wilayah->jabatan) }}" required />
                <x-ui.input label="RT" name="rt" value="{{ old('rt', $wilayah->rt) }}" />
                <x-ui.input label="RW" name="rw" value="{{ old('rw', $wilayah->rw) }}" />
                <x-ui.input label="Kelurahan" name="kelurahan" value="{{ old('kelurahan', $wilayah->kelurahan) }}" />
                <x-ui.input label="Kecamatan" name="kecamatan" value="{{ old('kecamatan', $wilayah->kecamatan) }}" />
                <x-ui.input label="Periode" name="periode" value="{{ old('periode', $wilayah->periode) }}" />
                <x-ui.input label="Tanggal Mulai" name="tgl_mulai" type="date" value="{{ old('tgl_mulai', $wilayah->tgl_mulai) }}" />
                <x-ui.input label="Tanggal Selesai" name="tgl_selesai" type="date" value="{{ old('tgl_selesai', $wilayah->tgl_selesai) }}" />
                <x-ui.select label="Status" name="status" :options="['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif']" selected="{{ old('status', $wilayah->status) }}" />
            </div>

            {{-- Data Tambahan --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Data Tambahan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.input label="No. Rekening" name="no_rekening" value="{{ old('no_rekening', $wilayah->no_rekening) }}" />
                <x-ui.input label="No. NPWP" name="no_npwp" value="{{ old('no_npwp', $wilayah->no_npwp) }}" />
                <div class="md:col-span-2">
                    <x-ui.textarea label="Keterangan" name="keterangan" value="{{ old('keterangan', $wilayah->keterangan) }}" />
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6 border-t pt-4">
                <x-ui.button type="ghost" href="{{ route('admin.wilayah.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary">Update Data RT/RW</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
