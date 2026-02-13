<x-layouts.app :title="'Tambah Jenis Surat'">
    <x-slot:header>
        <x-layouts.page-header title="Tambah Jenis Surat" description="Tambah jenis surat baru ke dalam sistem">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('master.jenis-surat.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('master.jenis-surat.store') }}">
            @csrf

            <div class="max-w-lg">
                <x-ui.input label="Nama Jenis Surat" name="nama" placeholder="Contoh: Surat Keterangan Domisili" value="{{ old('nama') }}" required />
                <p class="text-sm text-base-content/60 mt-1">Masukkan nama jenis surat yang akan digunakan dalam sistem persuratan.</p>
            </div>

            <div class="flex justify-end gap-2 mt-6 border-t pt-4">
                <x-ui.button type="ghost" href="{{ route('master.jenis-surat.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary">Simpan Jenis Surat</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
