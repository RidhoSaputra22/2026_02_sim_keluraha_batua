<x-layouts.app :title="'Tambah Penduduk'">
    <x-slot:header>
        <x-layouts.page-header title="Tambah Data Penduduk" description="Input data penduduk baru" />
    </x-slot:header>

    <x-layouts.breadcrumb :items="[
        ['label' => 'Data Penduduk', 'url' => route('admin.penduduk.index')],
        ['label' => 'Tambah Penduduk'],
    ]" />

    <x-ui.card class="max-w-4xl">
        <form method="POST" action="{{ route('admin.penduduk.store') }}">
            @csrf

            {{-- Identitas --}}
            <h3 class="text-lg font-semibold mb-4">Identitas</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.input name="nik" label="NIK" required placeholder="16 digit NIK" value="{{ old('nik') }}" />
                <x-ui.input name="nama" label="Nama Lengkap" required placeholder="Nama sesuai KTP" value="{{ old('nama') }}" />
                <x-ui.select name="jenis_kelamin" label="Jenis Kelamin" required :options="['L' => 'Laki-laki', 'P' => 'Perempuan']" selected="{{ old('jenis_kelamin') }}" />
                <x-ui.select name="keluarga_id" label="Kartu Keluarga" :options="$keluargaList->mapWithKeys(fn($k) => [$k->id => $k->no_kk . ' - ' . ($k->kepalaKeluarga->nama ?? 'N/A')])->toArray()" selected="{{ old('keluarga_id') }}" />
            </div>

            {{-- Data Pribadi --}}
            <h3 class="text-lg font-semibold mb-4">Data Pribadi</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.select name="agama" label="Agama" :options="['Islam' => 'Islam', 'Kristen' => 'Kristen', 'Katolik' => 'Katolik', 'Hindu' => 'Hindu', 'Buddha' => 'Buddha', 'Konghucu' => 'Konghucu']" selected="{{ old('agama') }}" />
                <x-ui.select name="status_kawin" label="Status Perkawinan" :options="['Belum Kawin' => 'Belum Kawin', 'Kawin' => 'Kawin', 'Cerai Hidup' => 'Cerai Hidup', 'Cerai Mati' => 'Cerai Mati']" selected="{{ old('status_kawin') }}" />
                <x-ui.select name="gol_darah" label="Golongan Darah" :options="['A' => 'A', 'B' => 'B', 'AB' => 'AB', 'O' => 'O', '-' => 'Tidak Tahu']" selected="{{ old('gol_darah') }}" />
                <x-ui.select name="pendidikan" label="Pendidikan Terakhir" :options="['Tidak/Belum Sekolah' => 'Tidak/Belum Sekolah', 'SD' => 'SD', 'SMP' => 'SMP', 'SMA' => 'SMA', 'D1/D2' => 'D1/D2', 'D3' => 'D3', 'S1' => 'S1', 'S2' => 'S2', 'S3' => 'S3']" selected="{{ old('pendidikan') }}" />
            </div>

            {{-- Alamat --}}
            <h3 class="text-lg font-semibold mb-4">Alamat</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="md:col-span-2">
                    <x-ui.textarea name="alamat" label="Alamat Lengkap" placeholder="Jalan, Gang, Nomor..." rows="2" value="{{ old('alamat') }}" />
                </div>
                <x-ui.select name="rt_id" label="RT / RW" :options="$rtList->mapWithKeys(fn($r) => [$r->id => 'RT ' . $r->nomor . ' / RW ' . ($r->rw->nomor ?? '-')])->toArray()" selected="{{ old('rt_id') }}" />
                <x-ui.select name="status_data" label="Status Data" :options="['aktif' => 'Aktif', 'pindah' => 'Pindah', 'meninggal' => 'Meninggal']" selected="{{ old('status_data', 'aktif') }}" />
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <x-ui.button type="ghost" href="{{ route('admin.penduduk.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary">Simpan Data</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
