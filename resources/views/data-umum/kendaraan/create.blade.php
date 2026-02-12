<x-layouts.app :title="'Tambah Kendaraan'">
    <x-slot:header>
        <x-layouts.page-header title="Tambah Kendaraan" description="Tambah data kendaraan baru">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('data-umum.kendaraan.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('data-umum.kendaraan.store') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                @php $kelurahanOptions = $kelurahanList->pluck('nama', 'id')->toArray(); @endphp
                <x-ui.select label="Kelurahan" name="kelurahan_id" placeholder="Pilih Kelurahan" :options="$kelurahanOptions" selected="{{ old('kelurahan_id') }}" required />
                <x-ui.input label="Jenis Barang" name="jenis_barang" placeholder="Contoh: Mobil, Motor, Truk" value="{{ old('jenis_barang') }}" required />
                <x-ui.input label="Merek / Type" name="merek_type" placeholder="Contoh: Toyota Avanza" value="{{ old('merek_type') }}" />
                <x-ui.input label="No. Polisi" name="no_polisi" placeholder="Contoh: DD 1234 AB" value="{{ old('no_polisi') }}" />
                <x-ui.input label="No. Rangka" name="no_rangka" placeholder="Nomor rangka kendaraan" value="{{ old('no_rangka') }}" />
                <x-ui.input label="No. Mesin" name="no_mesin" placeholder="Nomor mesin kendaraan" value="{{ old('no_mesin') }}" />
                <x-ui.input label="Nama Pengemudi" name="nama_pengemudi" placeholder="Nama pengemudi" value="{{ old('nama_pengemudi') }}" />
                <x-ui.input label="Tahun Perolehan" name="tahun_perolehan" placeholder="Contoh: 2024" value="{{ old('tahun_perolehan') }}" />
            </div>

            <div class="flex justify-end gap-2 mt-6 border-t pt-4">
                <x-ui.button type="ghost" href="{{ route('data-umum.kendaraan.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary">Simpan</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
