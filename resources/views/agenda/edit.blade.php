<x-layouts.app :title="'Edit Agenda'">
    <x-slot:header>
        <x-layouts.page-header title="Edit Agenda Kegiatan" description="Perbarui data agenda kegiatan">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('agenda.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('agenda.update', $agenda) }}">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                @php
                    $kelurahanOptions = $kelurahanList->pluck('nama', 'id')->toArray();
                    $instansiOptions = $instansiList->pluck('nama', 'id')->toArray();
                @endphp
                <x-ui.select label="Kelurahan" name="kelurahan_id" placeholder="Pilih Kelurahan" :options="$kelurahanOptions" selected="{{ old('kelurahan_id', $agenda->kelurahan_id) }}" required />
                <x-ui.select label="Instansi" name="instansi_id" placeholder="Pilih Instansi (opsional)" :options="$instansiOptions" selected="{{ old('instansi_id', $agenda->instansi_id) }}" />
                <x-ui.input label="Hari / Tanggal Kegiatan" name="hari_kegiatan" type="date" value="{{ old('hari_kegiatan', $agenda->hari_kegiatan?->format('Y-m-d')) }}" required />
                <x-ui.input label="Jam" name="jam" placeholder="Contoh: 09:00 - 12:00" value="{{ old('jam', $agenda->jam) }}" />
                <div class="md:col-span-2">
                    <x-ui.input label="Perihal" name="perihal" placeholder="Perihal kegiatan" value="{{ old('perihal', $agenda->perihal) }}" required />
                </div>
                <x-ui.input label="Lokasi" name="lokasi" placeholder="Lokasi kegiatan" value="{{ old('lokasi', $agenda->lokasi) }}" />
                <x-ui.input label="Penanggung Jawab" name="penanggung_jawab" placeholder="Nama penanggung jawab" value="{{ old('penanggung_jawab', $agenda->penanggung_jawab) }}" />
                <div class="md:col-span-2">
                    <x-ui.textarea label="Keterangan" name="keterangan" placeholder="Keterangan tambahan (opsional)">{{ old('keterangan', $agenda->keterangan) }}</x-ui.textarea>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6 border-t pt-4">
                <x-ui.button type="ghost" href="{{ route('agenda.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary">Perbarui</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
