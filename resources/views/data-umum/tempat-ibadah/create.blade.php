<x-layouts.app :title="'Tambah Tempat Ibadah'">
    <x-slot:header>
        <x-layouts.page-header title="Tambah Tempat Ibadah" description="Tambah data tempat ibadah baru">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('data-umum.tempat-ibadah.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('data-umum.tempat-ibadah.store') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                @php
                    $kelurahanOptions = $kelurahanList->pluck('nama', 'id')->toArray();
                    $rwOptions = $rwList->mapWithKeys(fn($rw) => [$rw->id => 'RW ' . $rw->nomor])->toArray();
                    $rtOptions = $rtList->mapWithKeys(fn($rt) => [$rt->id => 'RT ' . $rt->nomor . ' / RW ' . ($rt->rw->nomor ?? '-')])->toArray();
                @endphp
                <x-ui.select label="Kelurahan" name="kelurahan_id" placeholder="Pilih Kelurahan" :options="$kelurahanOptions" selected="{{ old('kelurahan_id') }}" required />
                <x-ui.select label="Jenis Tempat Ibadah" name="tempat_ibadah" placeholder="Pilih Jenis" :options="['Masjid' => 'Masjid', 'Musholla' => 'Musholla', 'Gereja' => 'Gereja', 'Pura' => 'Pura', 'Vihara' => 'Vihara', 'Klenteng' => 'Klenteng']" selected="{{ old('tempat_ibadah') }}" required />
                <x-ui.input label="Nama" name="nama" placeholder="Nama tempat ibadah" value="{{ old('nama') }}" required />
                <x-ui.input label="Pengurus" name="pengurus" placeholder="Nama pengurus" value="{{ old('pengurus') }}" />
                <x-ui.select label="RW" name="rw_id" placeholder="Pilih RW" :options="$rwOptions" selected="{{ old('rw_id') }}" />
                <x-ui.select label="RT" name="rt_id" placeholder="Pilih RT" :options="$rtOptions" selected="{{ old('rt_id') }}" />
                <div class="md:col-span-2">
                    <x-ui.input label="Alamat" name="alamat" placeholder="Alamat lengkap" value="{{ old('alamat') }}" />
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6 border-t pt-4">
                <x-ui.button type="ghost" href="{{ route('data-umum.tempat-ibadah.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary">Simpan</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
