<x-layouts.app :title="'Data Kendaraan'">
    <x-slot:header>
        <x-layouts.page-header title="Data Kendaraan" description="Kelola data kendaraan kelurahan">
            <x-slot:actions>
                <x-ui.button type="primary" size="sm" href="{{ route('data-umum.kendaraan.create') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Tambah Kendaraan
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    @if(session('success'))
        <x-ui.alert type="success" class="mb-4">{{ session('success') }}</x-ui.alert>
    @endif

    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('data-umum.kendaraan.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <x-ui.input name="search" placeholder="Cari pengemudi, no. polisi, merek..." value="{{ request('search') }}" />
            </div>
            <div class="flex gap-2">
                <x-ui.button type="primary" size="md">Cari</x-ui.button>
                <x-ui.button type="ghost" size="md" href="{{ route('data-umum.kendaraan.index') }}">Reset</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th class="w-12">No</th>
                        <th>Jenis Barang</th>
                        <th>Merek / Type</th>
                        <th>No. Polisi</th>
                        <th>No. Rangka</th>
                        <th>No. Mesin</th>
                        <th>Pengemudi</th>
                        <th>Tahun</th>
                        <th class="w-32 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kendaraanList as $item)
                    <tr class="hover">
                        <td class="text-sm text-base-content/60">{{ $kendaraanList->firstItem() + $loop->index }}</td>
                        <td class="font-medium">{{ $item->jenis_barang }}</td>
                        <td class="text-sm">{{ $item->merek_type ?? '-' }}</td>
                        <td class="text-sm font-mono">{{ $item->no_polisi ?? '-' }}</td>
                        <td class="text-sm font-mono">{{ $item->no_rangka ?? '-' }}</td>
                        <td class="text-sm font-mono">{{ $item->no_mesin ?? '-' }}</td>
                        <td class="text-sm">{{ $item->nama_pengemudi ?? '-' }}</td>
                        <td class="text-sm">{{ $item->tahun_perolehan ?? '-' }}</td>
                        <td>
                            <div class="flex justify-end gap-1">
                                <x-ui.button type="ghost" size="xs" href="{{ route('data-umum.kendaraan.edit', $item) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </x-ui.button>
                                <form method="POST" action="{{ route('data-umum.kendaraan.destroy', $item) }}" onsubmit="return confirm('Hapus data kendaraan ini?')">
                                    @csrf @method('DELETE')
                                    <x-ui.button type="error" size="xs" :outline="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </x-ui.button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-8 text-base-content/50">
                            <p>Belum ada data kendaraan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($kendaraanList->hasPages())
            <div class="mt-4">{{ $kendaraanList->links() }}</div>
        @endif
    </x-ui.card>
</x-layouts.app>
