<x-layouts.app :title="'Tambah Survey'">
    <x-slot:header>
        <x-layouts.page-header title="Tambah Survey Kepuasan" description="Input data survey kepuasan layanan">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('survey.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('survey.store') }}">
            @csrf

            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Data Responden</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                @php
                    $kelurahanOptions = $kelurahanList->pluck('nama', 'id')->toArray();
                    $layananOptions = $layananList->pluck('nama', 'id')->toArray();
                @endphp
                <x-ui.select label="Kelurahan" name="kelurahan_id" placeholder="Pilih Kelurahan" :options="$kelurahanOptions" selected="{{ old('kelurahan_id') }}" required />
                <x-ui.select label="Jenis Kelamin" name="jenis_kelamin" placeholder="Pilih" :options="['Laki-laki' => 'Laki-laki', 'Perempuan' => 'Perempuan']" selected="{{ old('jenis_kelamin') }}" />
                <x-ui.input label="Umur" name="umur" type="number" placeholder="Umur responden" value="{{ old('umur') }}" />
                <x-ui.select label="Pendidikan" name="pendidikan" placeholder="Pilih Pendidikan" :options="[
                    'SD' => 'SD', 'SMP' => 'SMP', 'SMA' => 'SMA/SMK',
                    'D3' => 'D3', 'S1' => 'S1', 'S2' => 'S2', 'S3' => 'S3'
                ]" selected="{{ old('pendidikan') }}" />
                <x-ui.input label="Pekerjaan" name="pekerjaan" placeholder="Pekerjaan responden" value="{{ old('pekerjaan') }}" />
            </div>

            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Penilaian Layanan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.select label="Jenis Layanan" name="jenis_layanan_id" placeholder="Pilih Layanan" :options="$layananOptions" selected="{{ old('jenis_layanan_id') }}" required />
                <x-ui.input label="Jumlah Nilai" name="jumlah_nilai" type="number" placeholder="Total nilai dari semua aspek" value="{{ old('jumlah_nilai') }}" />
                <x-ui.input label="Nilai Rata-rata" name="nilai_rata_rata" type="number" step="0.01" placeholder="Skala 1-5" value="{{ old('nilai_rata_rata') }}" />
            </div>

            <div class="flex justify-end gap-2 mt-6 border-t pt-4">
                <x-ui.button type="ghost" href="{{ route('survey.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary">Simpan</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
