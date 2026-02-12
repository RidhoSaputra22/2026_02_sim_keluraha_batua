<x-layouts.app :title="'Detail Agenda'">
    <x-slot:header>
        <x-layouts.page-header title="Detail Agenda Kegiatan" description="Lihat detail agenda dan hasil kegiatan">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('agenda.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
                <x-ui.button type="warning" size="sm" href="{{ route('agenda.edit', $agenda) }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    Edit
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    @if(session('success'))
        <x-ui.alert type="success" class="mb-4">{{ session('success') }}</x-ui.alert>
    @endif

    {{-- Detail Agenda --}}
    <x-ui.card class="mb-6">
        <h3 class="text-lg font-semibold mb-4 border-b pb-2">Informasi Agenda</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-base-content/60">Tanggal Kegiatan</p>
                <p class="font-medium">{{ $agenda->hari_kegiatan?->translatedFormat('l, d F Y') ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-base-content/60">Jam</p>
                <p class="font-medium">{{ $agenda->jam ?? '-' }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-sm text-base-content/60">Perihal</p>
                <p class="font-medium">{{ $agenda->perihal }}</p>
            </div>
            <div>
                <p class="text-sm text-base-content/60">Lokasi</p>
                <p class="font-medium">{{ $agenda->lokasi ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-base-content/60">Instansi</p>
                <p class="font-medium">{{ $agenda->instansi->nama ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-base-content/60">Penanggung Jawab</p>
                <p class="font-medium">{{ $agenda->penanggung_jawab ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-base-content/60">Kelurahan</p>
                <p class="font-medium">{{ $agenda->kelurahan->nama ?? '-' }}</p>
            </div>
            @if($agenda->keterangan)
            <div class="md:col-span-2">
                <p class="text-sm text-base-content/60">Keterangan</p>
                <p class="font-medium">{{ $agenda->keterangan }}</p>
            </div>
            @endif
        </div>
    </x-ui.card>

    {{-- Hasil Kegiatan --}}
    <x-ui.card>
        <h3 class="text-lg font-semibold mb-4 border-b pb-2">Hasil Kegiatan / Notulen</h3>

        @if($agenda->hasil)
            <div class="bg-base-200/50 rounded-lg p-4 mb-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-sm text-base-content/60">Hari / Tanggal Pelaksanaan</p>
                        <p class="font-medium">{{ $agenda->hasil->hari_tanggal?->translatedFormat('l, d F Y H:i') ?? '-' }}</p>
                    </div>
                </div>
                @if($agenda->hasil->notulen)
                    <div class="mb-4">
                        <p class="text-sm text-base-content/60 mb-1">Notulen</p>
                        <div class="prose max-w-none">{{ $agenda->hasil->notulen }}</div>
                    </div>
                @endif
                @if($agenda->hasil->keterangan)
                    <div>
                        <p class="text-sm text-base-content/60 mb-1">Keterangan</p>
                        <p>{{ $agenda->hasil->keterangan }}</p>
                    </div>
                @endif
            </div>
        @endif

        <div class="border-t pt-4">
            <h4 class="font-medium mb-3">{{ $agenda->hasil ? 'Perbarui Hasil Kegiatan' : 'Input Hasil Kegiatan' }}</h4>
            <form method="POST" action="{{ route('agenda.hasil.store', $agenda) }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <x-ui.input label="Hari / Tanggal Pelaksanaan" name="hari_tanggal" type="datetime-local" value="{{ old('hari_tanggal', $agenda->hasil?->hari_tanggal?->format('Y-m-d\TH:i')) }}" required />
                </div>
                <div class="mb-4">
                    <x-ui.textarea label="Notulen" name="notulen" placeholder="Isi notulen / catatan kegiatan" rows="5">{{ old('notulen', $agenda->hasil?->notulen) }}</x-ui.textarea>
                </div>
                <div class="mb-4">
                    <x-ui.textarea label="Keterangan" name="keterangan" placeholder="Keterangan tambahan (opsional)" rows="2">{{ old('keterangan', $agenda->hasil?->keterangan) }}</x-ui.textarea>
                </div>
                <div class="flex justify-end">
                    <x-ui.button type="primary">{{ $agenda->hasil ? 'Perbarui Hasil' : 'Simpan Hasil' }}</x-ui.button>
                </div>
            </form>
        </div>
    </x-ui.card>
</x-layouts.app>
