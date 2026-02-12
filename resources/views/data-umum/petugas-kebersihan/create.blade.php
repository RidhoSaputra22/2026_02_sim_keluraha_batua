<x-layouts.app :title="'Tambah Petugas Kebersihan'">
    <x-slot:header>
        <x-layouts.page-header title="Tambah Petugas Kebersihan" description="Tambah data petugas kebersihan baru">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('data-umum.petugas-kebersihan.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('data-umum.petugas-kebersihan.store') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                @php $kelurahanOptions = $kelurahanList->pluck('nama', 'id')->toArray(); @endphp
                <x-ui.select label="Kelurahan" name="kelurahan_id" placeholder="Pilih Kelurahan" :options="$kelurahanOptions" selected="{{ old('kelurahan_id') }}" required />
                <x-ui.input label="Nama" name="nama" placeholder="Nama petugas" value="{{ old('nama') }}" required />
                <x-ui.input label="NIK" name="nik" placeholder="Nomor Induk Kependudukan" value="{{ old('nik') }}" />
                <x-ui.select label="Jenis Kelamin" name="jenis_kelamin" placeholder="Pilih" :options="['Laki-laki' => 'Laki-laki', 'Perempuan' => 'Perempuan']" selected="{{ old('jenis_kelamin') }}" />
                <x-ui.input label="Pekerjaan" name="pekerjaan" placeholder="Pekerjaan" value="{{ old('pekerjaan') }}" />
                <x-ui.input label="Unit Kerja" name="unit_kerja" placeholder="Unit kerja" value="{{ old('unit_kerja') }}" />
                <x-ui.input label="Lokasi" name="lokasi" placeholder="Lokasi penugasan" value="{{ old('lokasi') }}" />
                <x-ui.select label="Status" name="status" placeholder="Pilih Status" :options="['aktif' => 'Aktif', 'tidak_aktif' => 'Tidak Aktif']" selected="{{ old('status', 'aktif') }}" />
            </div>

            <div class="flex justify-end gap-2 mt-6 border-t pt-4">
                <x-ui.button type="ghost" href="{{ route('data-umum.petugas-kebersihan.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary">Simpan</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
