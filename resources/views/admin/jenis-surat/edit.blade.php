<x-layouts.app :title="'Edit Jenis Surat'">
    <x-slot:header>
        <x-layouts.page-header title="Edit Jenis Surat" description="Ubah data jenis surat &quot;{{ $jenisSurat->nama }}&quot;">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('admin.jenis-surat.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.jenis-surat.update', $jenisSurat) }}">
            @csrf @method('PUT')

            <div class="max-w-lg">
                <x-ui.input label="Nama Jenis Surat" name="nama" value="{{ old('nama', $jenisSurat->nama) }}" required />
            </div>

            <div class="flex justify-end gap-2 mt-6 border-t pt-4">
                <x-ui.button type="ghost" href="{{ route('admin.jenis-surat.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary">Update Jenis Surat</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
