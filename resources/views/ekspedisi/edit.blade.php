<x-layouts.app :title="'Edit Ekspedisi'">
    <x-slot:header>
        <x-layouts.page-header title="Edit Ekspedisi" description="Perbarui data ekspedisi surat">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('ekspedisi.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('ekspedisi.update', $ekspedisi) }}">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                @php
                    $kelurahanOptions = $kelurahanList->pluck('nama', 'id')->toArray();
                @endphp
                <x-ui.select label="Kelurahan" name="kelurahan_id" placeholder="Pilih Kelurahan" :options="$kelurahanOptions" selected="{{ old('kelurahan_id', $ekspedisi->kelurahan_id) }}" required />
                <x-ui.input label="Nama Ekspedisi" name="ekspedisi" placeholder="Nama ekspedisi" value="{{ old('ekspedisi', $ekspedisi->ekspedisi) }}" required />
                <x-ui.input label="Pemilik Usaha" name="pemilik_usaha" placeholder="Nama pemilik usaha" value="{{ old('pemilik_usaha', $ekspedisi->pemilik_usaha) }}" required />
                <x-ui.input label="Penanggung Jawab" name="penanggung_jawab" placeholder="Nama penanggung jawab" value="{{ old('penanggung_jawab', $ekspedisi->penanggung_jawab) }}" />
                <x-ui.input label="Telp / HP" name="telp_hp" placeholder="Nomor telepon" value="{{ old('telp_hp', $ekspedisi->telp_hp) }}" />
                <x-ui.input label="Kegiatan Ekspedisi" name="kegiatan_ekspedisi" placeholder="Jenis kegiatan ekspedisi" value="{{ old('kegiatan_ekspedisi', $ekspedisi->kegiatan_ekspedisi) }}" />
                <div class="md:col-span-2">
                    <x-ui.input label="Alamat" name="alamat" placeholder="Alamat lengkap" value="{{ old('alamat', $ekspedisi->alamat) }}" />
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6 border-t pt-4">
                <x-ui.button type="ghost" href="{{ route('ekspedisi.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary">Perbarui</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
