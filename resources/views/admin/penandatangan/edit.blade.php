<x-layouts.app :title="'Edit Penandatangan'">
    <x-slot:header>
        <x-layouts.page-header title="Edit Penandatangan" description="Ubah data penandatangan {{ $penandatangan->pegawai->nama ?? '' }}">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('admin.penandatangan.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.penandatangan.update', $penandatangan) }}">
            @csrf @method('PUT')

            {{-- Pilih Pegawai --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Data Penandatangan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.select label="Pegawai" name="pegawai_id" required :options="$pegawaiList->mapWithKeys(fn($p) => [$p->id => $p->nip . ' - ' . $p->nama . ' (' . $p->jabatan . ')'])->toArray()" selected="{{ old('pegawai_id', $penandatangan->pegawai_id) }}" />
                <x-ui.input label="No. Telepon" name="no_telp" value="{{ old('no_telp', $penandatangan->no_telp) }}" />
                <x-ui.select label="Status" name="status" required :options="['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif']" selected="{{ old('status', $penandatangan->status) }}" />
            </div>

            <div class="flex justify-end gap-2 mt-6 border-t pt-4">
                <x-ui.button type="ghost" href="{{ route('admin.penandatangan.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary">Update Penandatangan</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
