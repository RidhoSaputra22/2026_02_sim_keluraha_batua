<x-layouts.app :title="'Edit Data Kontrakan & Kost'">
    <x-slot:header>
        <x-layouts.page-header title="Edit Kontrakan & Kost" description="Perbarui data kontrakan dan rumah kost">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('data-umum.kontrakan.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('data-umum.kontrakan.update', $kontrakan) }}">
            @csrf @method('PUT')

            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Informasi Lokasi</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                @php $kelurahanOptions = $kelurahanList->pluck('nama', 'id')->toArray(); @endphp
                <x-ui.select label="Kelurahan" name="kelurahan_id" placeholder="Pilih Kelurahan" :options="$kelurahanOptions" selected="{{ old('kelurahan_id', $kontrakan->kelurahan_id) }}" required />
                <x-ui.input label="Nama Kontrakan/Kost" name="nama" placeholder="Nama kontrakan atau kost" value="{{ old('nama', $kontrakan->nama) }}" />
                @php $rtOptions = $rtList->mapWithKeys(fn($rt) => [$rt->id => 'RT ' . $rt->nomor . ' / RW ' . ($rt->rw->nomor ?? '-')])->toArray(); @endphp
                <x-ui.select label="RT" name="rt_id" placeholder="Pilih RT" :options="$rtOptions" selected="{{ old('rt_id', $kontrakan->rt_id) }}" />
                @if($rwList->isNotEmpty())
                    @php $rwOptions = $rwList->mapWithKeys(fn($rw) => [$rw->id => 'RW ' . $rw->nomor])->toArray(); @endphp
                    <x-ui.select label="RW" name="rw_id" placeholder="Pilih RW" :options="$rwOptions" selected="{{ old('rw_id', $kontrakan->rw_id) }}" />
                @endif
                <x-ui.input label="Pemilik" name="pemilik" placeholder="Nama pemilik" value="{{ old('pemilik', $kontrakan->pemilik) }}" />
                <x-ui.input label="No HP Pemilik" name="no_hp_pemilik" placeholder="08xxxxxxxxxx" value="{{ old('no_hp_pemilik', $kontrakan->no_hp_pemilik) }}" />
                <div class="md:col-span-2">
                    <x-ui.input label="Alamat" name="alamat" placeholder="Alamat lengkap" value="{{ old('alamat', $kontrakan->alamat) }}" />
                </div>
            </div>

            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Jenis Unit</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.select label="Jenis Unit" name="jenis_unit" placeholder="Pilih Jenis Unit" :options="\App\Models\Kontrakan::JENIS_UNIT_OPTIONS" selected="{{ old('jenis_unit', $kontrakan->jenis_unit) }}" />
                <x-ui.input label="Jumlah Kamar" name="jumlah_kamar" type="number" placeholder="0" value="{{ old('jumlah_kamar', $kontrakan->jumlah_kamar) }}" />
            </div>

            <x-ui.map-picker
                label="Lokasi Kontrakan/Kost (Opsional)"
                :latitudeValue="old('latitude', $kontrakan->latitude)"
                :longitudeValue="old('longitude', $kontrakan->longitude)"
                addressField="alamat"
                :addressValue="old('alamat', $kontrakan->alamat)"
            />

            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Keterangan</h3>
            <div class="mb-6">
                <x-ui.textarea name="keterangan" placeholder="Keterangan tambahan (opsional)" value="{{ old('keterangan', $kontrakan->keterangan) }}" />
            </div>

            <div class="flex justify-end gap-2 mt-6 border-t pt-4">
                <x-ui.button type="ghost" href="{{ route('data-umum.kontrakan.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary" :isSubmit="true">Perbarui</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
