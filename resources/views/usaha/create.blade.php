<x-layouts.app :title="'Tambah Usaha'">
    <x-slot:header>
        <x-layouts.page-header title="Tambah Usaha" description="Registrasi data usaha baru">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('usaha.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('usaha.store') }}">
            @csrf

            {{-- Data Pemilik --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Data Pemilik</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                @php
                    $pendudukOptions = $pendudukList->mapWithKeys(fn($p) => [$p->id => $p->nama . ' - ' . $p->nik])->toArray();
                @endphp
                <x-ui.select label="Penduduk (Opsional)" name="penduduk_id" placeholder="Pilih Penduduk Terdaftar" :options="$pendudukOptions" selected="{{ old('penduduk_id') }}" />
                <x-ui.input label="Nama Pemilik" name="nama_pemilik" placeholder="Nama lengkap pemilik usaha" value="{{ old('nama_pemilik') }}" required />
                <x-ui.input label="NIK Pemilik" name="nik_pemilik" placeholder="Nomor Induk Kependudukan" value="{{ old('nik_pemilik') }}" />
                <x-ui.input label="No. HP" name="no_hp" placeholder="Nomor telepon / HP" value="{{ old('no_hp') }}" />
            </div>

            {{-- Data Usaha --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Data Usaha</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.input label="Nama Usaha" name="nama_ukm" placeholder="Nama usaha / UMKM" value="{{ old('nama_ukm') }}" required />
                @php
                    $jenisOptions = $jenisUsahaList->pluck('nama', 'id')->toArray();
                @endphp
                <x-ui.select label="Jenis Usaha" name="jenis_usaha_id" placeholder="Pilih Jenis Usaha" :options="$jenisOptions" selected="{{ old('jenis_usaha_id') }}" />
                <x-ui.input label="Sektor UMKM" name="sektor_umkm" placeholder="Contoh: Perdagangan, Jasa, Produksi" value="{{ old('sektor_umkm') }}" />
                <x-ui.select label="Status" name="status" :options="['aktif' => 'Aktif', 'tidak_aktif' => 'Tidak Aktif']" selected="{{ old('status', 'aktif') }}" />
            </div>

            {{-- Lokasi --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Lokasi</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                @php
                    $kelurahanOptions = $kelurahanList->pluck('nama', 'id')->toArray();
                    $rtOptions = $rtList->mapWithKeys(fn($rt) => [$rt->id => 'RT ' . $rt->nomor . ' / RW ' . ($rt->rw->nomor ?? '-')])->toArray();
                @endphp
                <x-ui.select label="Kelurahan" name="kelurahan_id" placeholder="Pilih Kelurahan" :options="$kelurahanOptions" selected="{{ old('kelurahan_id') }}" required />
                <x-ui.select label="RT / RW" name="rt_id" placeholder="Pilih RT / RW" :options="$rtOptions" selected="{{ old('rt_id') }}" />
                <div class="md:col-span-2">
                    <x-ui.input label="Alamat Lengkap" name="alamat" placeholder="Alamat lengkap lokasi usaha" value="{{ old('alamat') }}" />
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6 border-t pt-4">
                <x-ui.button type="ghost" href="{{ route('usaha.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary">Simpan</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
