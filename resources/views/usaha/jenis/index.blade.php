<x-layouts.app :title="'Jenis Usaha'">
    <x-slot:header>
        <x-layouts.page-header title="Jenis Usaha" description="Kelola kategori jenis usaha">
            <x-slot:actions>
                <x-ui.button type="primary" size="sm" onclick="document.getElementById('modal-tambah-jenis').showModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Tambah Jenis
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    @if(session('success'))
        <x-ui.alert type="success" class="mb-4">{{ session('success') }}</x-ui.alert>
    @endif
    @if(session('error'))
        <x-ui.alert type="error" class="mb-4">{{ session('error') }}</x-ui.alert>
    @endif

    {{-- Filters --}}
    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('usaha.jenis.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <x-ui.input name="search" placeholder="Cari nama jenis usaha..." value="{{ request('search') }}" />
            </div>
            <div class="flex gap-2">
                <x-ui.button type="primary" size="md">Cari</x-ui.button>
                <x-ui.button type="ghost" size="md" href="{{ route('usaha.jenis.index') }}">Reset</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    {{-- Table --}}
    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th class="w-12">No</th>
                        <th>Nama Jenis Usaha</th>
                        <th>Keterangan</th>
                        <th class="text-center">Jumlah Usaha</th>
                        <th class="w-40 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jenisList as $jenis)
                    <tr class="hover">
                        <td class="text-sm text-base-content/60">{{ $jenisList->firstItem() + $loop->index }}</td>
                        <td class="font-medium">{{ $jenis->nama }}</td>
                        <td class="text-sm text-base-content/70">{{ $jenis->keterangan ?? '-' }}</td>
                        <td class="text-center">
                            <x-ui.badge type="primary" size="sm">{{ $jenis->umkms_count }}</x-ui.badge>
                        </td>
                        <td>
                            <div class="flex justify-end gap-1">
                                <x-ui.button type="ghost" size="xs" onclick="document.getElementById('modal-edit-{{ $jenis->id }}').showModal()">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </x-ui.button>
                                <form method="POST" action="{{ route('usaha.jenis.destroy', $jenis) }}" onsubmit="return confirm('Hapus jenis usaha ini?')">
                                    @csrf @method('DELETE')
                                    <x-ui.button type="error" size="xs" :outline="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </x-ui.button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    {{-- Edit Modal --}}
                    <x-ui.modal id="modal-edit-{{ $jenis->id }}" title="Edit Jenis Usaha">
                        <form method="POST" action="{{ route('usaha.jenis.update', $jenis) }}">
                            @csrf @method('PUT')
                            <div class="space-y-4">
                                <x-ui.input label="Nama Jenis Usaha" name="nama" placeholder="Masukkan nama jenis usaha" value="{{ old('nama', $jenis->nama) }}" required />
                                <x-ui.input label="Keterangan" name="keterangan" placeholder="Keterangan singkat (opsional)" value="{{ old('keterangan', $jenis->keterangan) }}" />
                            </div>
                            <x-slot:actions>
                                <form method="dialog"><x-ui.button type="ghost" :isSubmit="false">Batal</x-ui.button></form>
                                <x-ui.button type="primary">Simpan</x-ui.button>
                            </x-slot:actions>
                        </form>
                    </x-ui.modal>

                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-8 text-base-content/50">
                            <div class="flex flex-col items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                                <p>Belum ada data jenis usaha</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jenisList->hasPages())
            <div class="mt-4">
                {{ $jenisList->links() }}
            </div>
        @endif
    </x-ui.card>

    {{-- Add Modal --}}
    <x-ui.modal id="modal-tambah-jenis" title="Tambah Jenis Usaha">
        <form method="POST" action="{{ route('usaha.jenis.store') }}">
            @csrf
            <div class="space-y-4">
                <x-ui.input label="Nama Jenis Usaha" name="nama" placeholder="Masukkan nama jenis usaha" value="{{ old('nama') }}" required />
                <x-ui.input label="Keterangan" name="keterangan" placeholder="Keterangan singkat (opsional)" value="{{ old('keterangan') }}" />
            </div>
            <x-slot:actions>
                <form method="dialog"><x-ui.button type="ghost" :isSubmit="false">Batal</x-ui.button></form>
                <x-ui.button type="primary">Simpan</x-ui.button>
            </x-slot:actions>
        </form>
    </x-ui.modal>
</x-layouts.app>
