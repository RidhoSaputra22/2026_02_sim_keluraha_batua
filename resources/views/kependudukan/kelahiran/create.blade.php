<x-layouts.app :title="'Tambah Data Kelahiran'">
    <x-slot:header>
        <x-layouts.page-header title="Tambah Data Kelahiran" description="Catat data kelahiran penduduk baru">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('kependudukan.kelahiran.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('kependudukan.kelahiran.store') }}">
            @csrf

            {{-- Data Bayi --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Data Bayi</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                <x-ui.input label="Nama Bayi" name="nama_bayi" placeholder="Masukkan nama bayi" value="{{ old('nama_bayi') }}" required />
                <x-ui.select label="Jenis Kelamin" name="jenis_kelamin" :options="['L' => 'Laki-laki', 'P' => 'Perempuan']" selected="{{ old('jenis_kelamin') }}" />
                <x-ui.input label="Tempat Lahir" name="tempat_lahir" placeholder="Kota/Kabupaten" value="{{ old('tempat_lahir') }}" />
                <x-ui.input label="Tanggal Lahir" name="tanggal_lahir" type="date" value="{{ old('tanggal_lahir', date('Y-m-d')) }}" required />
                <x-ui.input label="Jam Lahir" name="jam_lahir" type="time" value="{{ old('jam_lahir') }}" />
                <x-ui.input label="No. Akte Kelahiran" name="no_akte" placeholder="Nomor akte (jika sudah ada)" value="{{ old('no_akte') }}" />
            </div>

            {{-- Data Orang Tua --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Data Orang Tua</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                @php
                    $pendudukOptions = $pendudukList->mapWithKeys(fn($p) => [$p->id => $p->nama . ' - ' . $p->nik])->toArray();
                    $rtOptions = $rtList->mapWithKeys(fn($rt) => [$rt->id => 'RT ' . $rt->nomor . ' / RW ' . ($rt->rw->nomor ?? '-')])->toArray();
                @endphp
                <x-ui.select label="Ibu" name="ibu_id" placeholder="Pilih Ibu" :options="$pendudukOptions" selected="{{ old('ibu_id') }}" />
                <x-ui.select label="Ayah" name="ayah_id" placeholder="Pilih Ayah" :options="$pendudukOptions" selected="{{ old('ayah_id') }}" />
            </div>

            {{-- Lokasi --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Lokasi</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.select label="RT/RW" name="rt_id" placeholder="Pilih RT/RW" :options="$rtOptions" selected="{{ old('rt_id') }}" />
                <x-ui.textarea label="Keterangan" name="keterangan" rows="3" placeholder="Keterangan tambahan (opsional)">{{ old('keterangan') }}</x-ui.textarea>
            </div>

            <div class="flex justify-end gap-2 mt-6 border-t pt-4">
                <x-ui.button type="ghost" href="{{ route('kependudukan.kelahiran.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary">Simpan Data Kelahiran</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
