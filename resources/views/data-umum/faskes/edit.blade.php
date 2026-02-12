<x-layouts.app :title="'Edit Faskes'">
    <x-slot:header>
        <x-layouts.page-header title="Edit Fasilitas Kesehatan" description="Perbarui data fasilitas kesehatan">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('data-umum.faskes.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('data-umum.faskes.update', $faske) }}">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                @php
                    $kelurahanOptions = $kelurahanList->pluck('nama', 'id')->toArray();
                    $rwOptions = $rwList->mapWithKeys(fn($rw) => [$rw->id => 'RW ' . $rw->nomor])->toArray();
                @endphp
                <x-ui.select label="Kelurahan" name="kelurahan_id" placeholder="Pilih Kelurahan" :options="$kelurahanOptions" selected="{{ old('kelurahan_id', $faske->kelurahan_id) }}" required />
                <x-ui.input label="Nama Faskes" name="nama_rs" placeholder="Nama rumah sakit / puskesmas / klinik" value="{{ old('nama_rs', $faske->nama_rs) }}" required />
                <x-ui.input label="Jenis" name="jenis" placeholder="Contoh: Rumah Sakit, Puskesmas, Klinik" value="{{ old('jenis', $faske->jenis) }}" />
                <x-ui.input label="Kelas" name="kelas" placeholder="Contoh: A, B, C, D" value="{{ old('kelas', $faske->kelas) }}" />
                <x-ui.input label="Jenis Pelayanan" name="jenis_pelayanan" placeholder="Contoh: Rawat Inap, Rawat Jalan" value="{{ old('jenis_pelayanan', $faske->jenis_pelayanan) }}" />
                <x-ui.input label="Akreditasi" name="akreditasi" placeholder="Status akreditasi" value="{{ old('akreditasi', $faske->akreditasi) }}" />
                <x-ui.select label="RW" name="rw_id" placeholder="Pilih RW" :options="$rwOptions" selected="{{ old('rw_id', $faske->rw_id) }}" />
                <x-ui.input label="Telepon" name="telp" placeholder="Nomor telepon" value="{{ old('telp', $faske->telp) }}" />
                <div class="md:col-span-2">
                    <x-ui.input label="Alamat" name="alamat" placeholder="Alamat lengkap" value="{{ old('alamat', $faske->alamat) }}" />
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6 border-t pt-4">
                <x-ui.button type="ghost" href="{{ route('data-umum.faskes.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary">Perbarui</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
