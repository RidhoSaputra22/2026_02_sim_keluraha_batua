<x-layouts.app :title="'Edit Data Kematian'">
    <x-slot:header>
        <x-layouts.page-header title="Edit Data Kematian" description="Ubah data kematian {{ $kematian->penduduk->nama ?? '' }}">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('kependudukan.kematian.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('kependudukan.kematian.update', $kematian) }}">
            @csrf @method('PUT')

            {{-- Data Penduduk --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Data Penduduk</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                @php
                    $pendudukOptions = $pendudukList->mapWithKeys(fn($p) => [$p->id => $p->nama . ' - ' . $p->nik])->toArray();
                @endphp
                <x-ui.select label="Penduduk yang Meninggal" name="penduduk_id" placeholder="Pilih Penduduk" :options="$pendudukOptions" selected="{{ old('penduduk_id', $kematian->penduduk_id) }}" />
                <x-ui.input label="Tanggal Meninggal" name="tanggal_meninggal" type="date" value="{{ old('tanggal_meninggal', $kematian->tanggal_meninggal->format('Y-m-d')) }}" required />
            </div>

            {{-- Detail Kematian --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Detail Kematian</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.input label="Tempat Meninggal" name="tempat_meninggal" value="{{ old('tempat_meninggal', $kematian->tempat_meninggal) }}" />
                <x-ui.input label="Penyebab" name="penyebab" value="{{ old('penyebab', $kematian->penyebab) }}" />
                <x-ui.input label="No. Akte Kematian" name="no_akte_kematian" value="{{ old('no_akte_kematian', $kematian->no_akte_kematian) }}" />
                <x-ui.textarea label="Keterangan" name="keterangan" rows="3">{{ old('keterangan', $kematian->keterangan) }}</x-ui.textarea>
            </div>

            <div class="flex justify-end gap-2 mt-6 border-t pt-4">
                <x-ui.button type="ghost" href="{{ route('kependudukan.kematian.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary">Update Data Kematian</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
