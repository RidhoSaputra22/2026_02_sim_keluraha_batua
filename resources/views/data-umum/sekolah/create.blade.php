<x-layouts.app :title="'Tambah Sekolah'">
    <x-slot:header>
        <x-layouts.page-header title="Tambah Sekolah" description="Tambah data sekolah baru">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('data-umum.sekolah.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('data-umum.sekolah.store') }}">
            @csrf

            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Identitas Sekolah</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                @php $kelurahanOptions = $kelurahanList->pluck('nama', 'id')->toArray(); @endphp
                <x-ui.select label="Kelurahan" name="kelurahan_id" placeholder="Pilih Kelurahan" :options="$kelurahanOptions" selected="{{ old('kelurahan_id') }}" required />
                <x-ui.input label="Nama Sekolah" name="nama_sekolah" placeholder="Nama sekolah" value="{{ old('nama_sekolah') }}" required />
                <x-ui.input label="NPSN" name="npsn" placeholder="Nomor Pokok Sekolah Nasional" value="{{ old('npsn') }}" />
                <x-ui.select label="Jenjang" name="jenjang" placeholder="Pilih Jenjang" :options="['TK' => 'TK', 'SD' => 'SD', 'SMP' => 'SMP', 'SMA' => 'SMA', 'SMK' => 'SMK']" selected="{{ old('jenjang') }}" />
                <x-ui.select label="Status" name="status" placeholder="Pilih Status" :options="['Negeri' => 'Negeri', 'Swasta' => 'Swasta']" selected="{{ old('status') }}" />
                <x-ui.input label="Tahun Ajar" name="tahun_ajar" placeholder="Contoh: 2025/2026" value="{{ old('tahun_ajar') }}" />
                <div class="md:col-span-2">
                    <x-ui.input label="Alamat" name="alamat" placeholder="Alamat lengkap sekolah" value="{{ old('alamat') }}" />
                </div>
            </div>

            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Data Statistik</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <x-ui.input label="Jumlah Siswa" name="jumlah_siswa" type="number" placeholder="0" value="{{ old('jumlah_siswa') }}" />
                <x-ui.input label="Rombel" name="rombel" type="number" placeholder="0" value="{{ old('rombel') }}" />
                <x-ui.input label="Jumlah Guru" name="jumlah_guru" type="number" placeholder="0" value="{{ old('jumlah_guru') }}" />
                <x-ui.input label="Jumlah Pegawai" name="jumlah_pegawai" type="number" placeholder="0" value="{{ old('jumlah_pegawai') }}" />
                <x-ui.input label="Ruang Kelas" name="ruang_kelas" type="number" placeholder="0" value="{{ old('ruang_kelas') }}" />
                <x-ui.input label="Ruang Lab" name="jumlah_r_lab" type="number" placeholder="0" value="{{ old('jumlah_r_lab') }}" />
                <x-ui.input label="Ruang Perpustakaan" name="jumlah_r_perpus" type="number" placeholder="0" value="{{ old('jumlah_r_perpus') }}" />
            </div>

            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Koordinat (Opsional)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.input label="Latitude" name="latitude" placeholder="-5.xxxxx" value="{{ old('latitude') }}" />
                <x-ui.input label="Longitude" name="longitude" placeholder="119.xxxxx" value="{{ old('longitude') }}" />
            </div>

            <div class="flex justify-end gap-2 mt-6 border-t pt-4">
                <x-ui.button type="ghost" href="{{ route('data-umum.sekolah.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary">Simpan</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
