<x-layouts.app :title="'Detail Jenis Surat'">
    <x-slot:header>
        <x-layouts.page-header title="Detail Jenis Surat" description="{{ $jenisSurat->nama }}">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('admin.jenis-surat.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
                <x-ui.button type="primary" size="sm" href="{{ route('admin.jenis-surat.edit', $jenisSurat) }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    Edit
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card>
        <div class="space-y-4">
            <div>
                <label class="text-sm font-medium text-base-content/60">Nama Jenis Surat</label>
                <p class="text-lg font-semibold">{{ $jenisSurat->nama }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-base-content/60">Jumlah Surat Terbit</label>
                <p class="text-lg"><span class="badge badge-primary badge-lg">{{ $jenisSurat->surats_count ?? 0 }} surat</span></p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-base-content/60">Dibuat Pada</label>
                    <p>{{ $jenisSurat->created_at?->format('d F Y, H:i') ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-base-content/60">Terakhir Diperbarui</label>
                    <p>{{ $jenisSurat->updated_at?->format('d F Y, H:i') ?? '-' }}</p>
                </div>
            </div>
        </div>
    </x-ui.card>
</x-layouts.app>
