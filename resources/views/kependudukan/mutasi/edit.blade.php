<x-layouts.app :title="'Edit Mutasi Penduduk'">
    <x-slot:header>
        <x-layouts.page-header title="Edit Mutasi Penduduk" description="Ubah data mutasi {{ $mutasi->penduduk->nama ?? '' }}">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('kependudukan.mutasi.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('kependudukan.mutasi.update', $mutasi) }}">
            @csrf @method('PUT')

            {{-- Data Penduduk --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Data Penduduk</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                @php
                    $pendudukOptions = $pendudukList->mapWithKeys(fn($p) => [$p->id => $p->nama . ' - ' . $p->nik])->toArray();
                @endphp
                <x-ui.select label="Penduduk" name="penduduk_id" placeholder="Pilih Penduduk" :options="$pendudukOptions" selected="{{ old('penduduk_id', $mutasi->penduduk_id) }}" />

                <x-ui.select label="Jenis Mutasi" name="jenis_mutasi" :options="['pindah' => 'Pindah', 'datang' => 'Datang']" selected="{{ old('jenis_mutasi', $mutasi->jenis_mutasi) }}" />
            </div>

            {{-- Detail Mutasi --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Detail Mutasi</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <x-ui.input label="Tanggal Mutasi" name="tanggal_mutasi" type="date" value="{{ old('tanggal_mutasi', $mutasi->tanggal_mutasi->format('Y-m-d')) }}" required />
                <x-ui.input label="No. Surat Pindah" name="no_surat_pindah" value="{{ old('no_surat_pindah', $mutasi->no_surat_pindah) }}" />
                <x-ui.select label="Status" name="status" :options="['proses' => 'Proses', 'selesai' => 'Selesai', 'batal' => 'Batal']" selected="{{ old('status', $mutasi->status) }}" />
            </div>

            {{-- Alamat --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Alamat Asal & Tujuan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.input label="Alamat Asal" name="alamat_asal" value="{{ old('alamat_asal', $mutasi->alamat_asal) }}" />

                @php
                    $rtOptions = $rtList->mapWithKeys(fn($rt) => [$rt->id => 'RT ' . $rt->nomor . ' / RW ' . ($rt->rw->nomor ?? '-')])->toArray();
                @endphp
                <x-ui.select label="RT Asal" name="rt_asal_id" placeholder="Pilih RT Asal" :options="$rtOptions" selected="{{ old('rt_asal_id', $mutasi->rt_asal_id) }}" />

                <x-ui.input label="Alamat Tujuan" name="alamat_tujuan" value="{{ old('alamat_tujuan', $mutasi->alamat_tujuan) }}" />
                <x-ui.select label="RT Tujuan" name="rt_tujuan_id" placeholder="Pilih RT Tujuan" :options="$rtOptions" selected="{{ old('rt_tujuan_id', $mutasi->rt_tujuan_id) }}" />
            </div>

            {{-- Keterangan --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Keterangan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.input label="Alasan Mutasi" name="alasan" value="{{ old('alasan', $mutasi->alasan) }}" />
                <x-ui.textarea label="Keterangan Tambahan" name="keterangan" rows="3">{{ old('keterangan', $mutasi->keterangan) }}</x-ui.textarea>
            </div>

            <div class="flex justify-end gap-2 mt-6 border-t pt-4">
                <x-ui.button type="ghost" href="{{ route('kependudukan.mutasi.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary">Update Mutasi</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
