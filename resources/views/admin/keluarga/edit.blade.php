<x-layouts.app :title="'Edit Kartu Keluarga'">
    <x-slot:header>
        <x-layouts.page-header title="Edit Kartu Keluarga" description="Ubah data kartu keluarga {{ $keluarga->no_kk }}">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('kependudukan.keluarga.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('kependudukan.keluarga.update', $keluarga) }}">
            @csrf @method('PUT')

            {{-- Informasi Kartu Keluarga --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Informasi Kartu Keluarga</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.input label="Nomor KK" name="no_kk" value="{{ old('no_kk', $keluarga->no_kk) }}" required maxlength="16" />
                <x-ui.select label="Kepala Keluarga" name="kepala_keluarga_id" :options="$pendudukList->mapWithKeys(fn($p) => [$p->id => $p->nik . ' - ' . $p->nama])->toArray()" selected="{{ old('kepala_keluarga_id', $keluarga->kepala_keluarga_id) }}" />
                <x-ui.input label="Jumlah Anggota Keluarga" name="jumlah_anggota_keluarga" type="number" value="{{ old('jumlah_anggota_keluarga', $keluarga->jumlah_anggota_keluarga) }}" min="1" />
                <x-ui.select label="RT / RW" name="rt_id" :options="$rtList->mapWithKeys(fn($r) => [$r->id => 'RT ' . $r->nomor . ' / RW ' . ($r->rw->nomor ?? '-')])->toArray()" selected="{{ old('rt_id', $keluarga->rt_id) }}" />
            </div>

            {{-- Arsip --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Arsip</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.input label="Path Arsip" name="arsip_path" value="{{ old('arsip_path', $keluarga->arsip_path) }}" />
            </div>

            <div class="flex justify-end gap-2 mt-6 border-t pt-4">
                <x-ui.button type="ghost" href="{{ route('kependudukan.keluarga.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary">Update Data Keluarga</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
