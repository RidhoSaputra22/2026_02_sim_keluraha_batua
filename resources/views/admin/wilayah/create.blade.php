<x-layouts.app :title="'Tambah Data RT/RW'">
    <x-slot:header>
        <x-layouts.page-header title="Tambah Data RT/RW" description="Tambah data pengurus RT atau RW baru">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('master.wilayah.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('master.wilayah.store') }}">
            @csrf

            {{-- Data Pengurus --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Data Pengurus</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.select label="Penduduk" name="penduduk_id" required :options="$pendudukList->mapWithKeys(fn($p) => [$p->id => $p->nik . ' - ' . $p->nama])->toArray()" selected="{{ old('penduduk_id') }}" />
                <x-ui.select label="Kelurahan" name="kelurahan_id" required :options="$kelurahanList->mapWithKeys(fn($k) => [$k->id => $k->nama])->toArray()" selected="{{ old('kelurahan_id') }}" />
            </div>

            {{-- Data Jabatan --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Data Jabatan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                <x-ui.select label="Jabatan" name="jabatan_id" required :options="$jabatanList->mapWithKeys(fn($j) => [$j->id => $j->nama])->toArray()" selected="{{ old('jabatan_id') }}" />
                <x-ui.select label="RW" name="rw_id" :options="$rwList->mapWithKeys(fn($r) => [$r->id => 'RW ' . $r->nomor])->toArray()" selected="{{ old('rw_id') }}" />
                <x-ui.select label="RT" name="rt_id" :options="$rtList->mapWithKeys(fn($r) => [$r->id => 'RT ' . $r->nomor . ' / RW ' . ($r->rw->nomor ?? '-')])->toArray()" selected="{{ old('rt_id') }}" />
                <x-ui.input label="Tanggal Mulai" name="tgl_mulai" type="date" value="{{ old('tgl_mulai') }}" />
                <x-ui.select label="Status" name="status" required :options="['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif']" selected="{{ old('status', 'aktif') }}" />
            </div>

            {{-- Data Tambahan --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Data Tambahan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.input label="No. Telepon" name="no_telp" placeholder="08xxxxxxxxxx" value="{{ old('no_telp') }}" />
                <x-ui.input label="No. Rekening" name="no_rekening" placeholder="Nomor rekening" value="{{ old('no_rekening') }}" />
                <x-ui.input label="No. NPWP" name="no_npwp" placeholder="Nomor NPWP" value="{{ old('no_npwp') }}" />
                <div class="md:col-span-2">
                    <x-ui.textarea label="Alamat" name="alamat" placeholder="Alamat lengkap" value="{{ old('alamat') }}" />
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6 border-t pt-4">
                <x-ui.button type="ghost" href="{{ route('master.wilayah.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary">Simpan Data RT/RW</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
