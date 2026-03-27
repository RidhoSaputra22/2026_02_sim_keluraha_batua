<x-layouts.app :title="'Tambah Data Asrama'">
    <x-slot:header>
        <x-layouts.page-header title="Tambah Asrama" description="Tambah data asrama baru">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('data-umum.asrama.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('data-umum.asrama.store') }}">
            @csrf

            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Informasi Asrama</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                @php $kelurahanOptions = $kelurahanList->pluck('nama', 'id')->toArray(); @endphp
                <x-ui.select label="Kelurahan" name="kelurahan_id" placeholder="Pilih Kelurahan" :options="$kelurahanOptions" selected="{{ old('kelurahan_id') }}" required />
                <x-ui.select label="Jenis Asrama" name="jenis" placeholder="Pilih Jenis" :options="$jenisOptions" selected="{{ old('jenis') }}" required />
                <x-ui.input label="Nama Asrama" name="nama" placeholder="Nama asrama" value="{{ old('nama') }}" />
                <x-ui.input label="Jumlah" name="jumlah" type="number" placeholder="0" value="{{ old('jumlah', 0) }}" />
                @php $rtOptions = $rtList->mapWithKeys(fn($rt) => [$rt->id => 'RT ' . $rt->nomor . ' / RW ' . ($rt->rw->nomor ?? '-')])->toArray(); @endphp
                <x-ui.select label="RT" name="rt_id" placeholder="Pilih RT" :options="$rtOptions" selected="{{ old('rt_id') }}" />
                @if($rwList->isNotEmpty())
                    @php $rwOptions = $rwList->mapWithKeys(fn($rw) => [$rw->id => 'RW ' . $rw->nomor])->toArray(); @endphp
                    <x-ui.select label="RW" name="rw_id" placeholder="Pilih RW" :options="$rwOptions" selected="{{ old('rw_id') }}" />
                @endif
                <div class="md:col-span-2">
                    <x-ui.input label="Alamat" name="alamat" placeholder="Alamat lengkap" value="{{ old('alamat') }}" />
                </div>
            </div>

            <x-ui.map-picker
                label="Lokasi Asrama (Opsional)"
                :latitudeValue="old('latitude')"
                :longitudeValue="old('longitude')"
                addressField="alamat"
                :addressValue="old('alamat')"
            />

            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Keterangan</h3>
            <div class="mb-6">
                <x-ui.textarea name="keterangan" placeholder="Keterangan tambahan (opsional)" value="{{ old('keterangan') }}" />
            </div>

            <div class="flex justify-end gap-2 mt-6 border-t pt-4">
                <x-ui.button type="ghost" href="{{ route('data-umum.asrama.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary" :isSubmit="true">Simpan</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
