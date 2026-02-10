<x-layouts.app :title="'Edit Penduduk'">
    <x-slot:header>
        <x-layouts.page-header title="Edit Data Penduduk" description="Ubah data: {{ $penduduk->nama }}" />
    </x-slot:header>

    <x-layouts.breadcrumb :items="[
        ['label' => 'Data Penduduk', 'url' => route('admin.penduduk.index')],
        ['label' => 'Edit: ' . $penduduk->nama],
    ]" />

    <x-ui.card class="max-w-4xl">
        <form method="POST" action="{{ route('admin.penduduk.update', $penduduk) }}">
            @csrf @method('PUT')

            <h3 class="text-lg font-semibold mb-4">Identitas</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.input name="nik" label="NIK" required value="{{ $penduduk->nik }}" />
                <x-ui.input name="nama" label="Nama Lengkap" required value="{{ $penduduk->nama }}" />
                <x-ui.input name="no_kk" label="No. Kartu Keluarga" value="{{ $penduduk->no_kk }}" />
                <x-ui.select name="jenis_kelamin" label="Jenis Kelamin" required :options="['L' => 'Laki-laki', 'P' => 'Perempuan']" selected="{{ $penduduk->jenis_kelamin?->value }}" />
            </div>

            <h3 class="text-lg font-semibold mb-4">Data Kelahiran</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.input name="tempat_lahir" label="Tempat Lahir" value="{{ $penduduk->tempat_lahir }}" />
                <x-ui.input name="tanggal_lahir" label="Tanggal Lahir" type="date" value="{{ $penduduk->tanggal_lahir?->format('Y-m-d') }}" />
                <x-ui.select name="gol_darah" label="Golongan Darah" :options="['A' => 'A', 'B' => 'B', 'AB' => 'AB', 'O' => 'O', '-' => 'Tidak Tahu']" selected="{{ $penduduk->gol_darah }}" />
                <x-ui.select name="kewarganegaraan" label="Kewarganegaraan" :options="['WNI' => 'WNI', 'WNA' => 'WNA']" selected="{{ $penduduk->kewarganegaraan }}" />
            </div>

            <h3 class="text-lg font-semibold mb-4">Status Pribadi</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.select name="agama" label="Agama" :options="['Islam' => 'Islam', 'Kristen' => 'Kristen', 'Katolik' => 'Katolik', 'Hindu' => 'Hindu', 'Buddha' => 'Buddha', 'Konghucu' => 'Konghucu']" selected="{{ $penduduk->agama }}" />
                <x-ui.select name="status_kawin" label="Status Perkawinan" :options="['Belum Kawin' => 'Belum Kawin', 'Kawin' => 'Kawin', 'Cerai Hidup' => 'Cerai Hidup', 'Cerai Mati' => 'Cerai Mati']" selected="{{ $penduduk->status_kawin }}" />
                <x-ui.input name="pekerjaan" label="Pekerjaan" value="{{ $penduduk->pekerjaan }}" />
                <x-ui.select name="pendidikan" label="Pendidikan Terakhir" :options="['Tidak/Belum Sekolah' => 'Tidak/Belum Sekolah', 'SD' => 'SD', 'SMP' => 'SMP', 'SMA' => 'SMA', 'D1/D2' => 'D1/D2', 'D3' => 'D3', 'S1' => 'S1', 'S2' => 'S2', 'S3' => 'S3']" selected="{{ $penduduk->pendidikan }}" />
            </div>

            <h3 class="text-lg font-semibold mb-4">Alamat</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="md:col-span-2">
                    <x-ui.textarea name="alamat" label="Alamat Lengkap" value="{{ $penduduk->alamat }}" rows="2" />
                </div>
                <x-ui.input name="rt" label="RT" value="{{ $penduduk->rt }}" />
                <x-ui.input name="rw" label="RW" value="{{ $penduduk->rw }}" />
                <x-ui.input name="kelurahan" label="Kelurahan" value="{{ $penduduk->kelurahan }}" />
                <x-ui.input name="kecamatan" label="Kecamatan" value="{{ $penduduk->kecamatan }}" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.select name="status_data" label="Status Data" :options="['aktif' => 'Aktif', 'pindah' => 'Pindah', 'meninggal' => 'Meninggal']" selected="{{ $penduduk->status_data }}" />
                <x-ui.textarea name="keterangan" label="Keterangan" value="{{ $penduduk->keterangan }}" rows="2" />
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <x-ui.button type="ghost" href="{{ route('admin.penduduk.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary">Perbarui Data</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
