<x-layouts.app :title="'Fasilitas Kesehatan'">
    <x-slot:header>
        <x-layouts.page-header title="Fasilitas Kesehatan" description="Kelola data fasilitas kesehatan di wilayah kelurahan">
            <x-slot:actions>
                <x-ui.button type="primary" size="sm" href="{{ route('data-umum.faskes.create') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Tambah Faskes
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    @if(session('success'))
        <x-ui.alert type="success" class="mb-4">{{ session('success') }}</x-ui.alert>
    @endif

    {{-- Filters --}}
    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('data-umum.faskes.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <x-ui.input name="search" placeholder="Cari nama faskes, alamat, jenis..." value="{{ request('search') }}" />
            </div>
            <div class="w-full md:w-48">
                <x-ui.select name="jenis" placeholder="Semua Jenis" :options="$jenisList->mapWithKeys(fn($j) => [$j => $j])->toArray()" selected="{{ request('jenis') }}" />
            </div>
            <div class="flex gap-2">
                <x-ui.button type="primary" size="md">Cari</x-ui.button>
                <x-ui.button type="ghost" size="md" href="{{ route('data-umum.faskes.index') }}">Reset</x-ui.button>
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
                        <th>Nama Faskes</th>
                        <th>Jenis</th>
                        <th>Kelas</th>
                        <th>Alamat</th>
                        <th>RW</th>
                        <th>Akreditasi</th>
                        <th>Telp</th>
                        <th class="w-32 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($faskesList as $item)
                    <tr class="hover">
                        <td class="text-sm text-base-content/60">{{ $faskesList->firstItem() + $loop->index }}</td>
                        <td class="font-medium">{{ $item->nama_rs }}</td>
                        <td class="text-sm">{{ $item->jenis ?? '-' }}</td>
                        <td class="text-sm">{{ $item->kelas ?? '-' }}</td>
                        <td class="text-sm">{{ Str::limit($item->alamat, 35) }}</td>
                        <td class="text-sm">{{ $item->rw->nomor ?? '-' }}</td>
                        <td class="text-sm">{{ $item->akreditasi ?? '-' }}</td>
                        <td class="text-sm">{{ $item->telp ?? '-' }}</td>
                        <td>
                            <div class="flex justify-end gap-1">
                                <x-ui.button type="ghost" size="xs" href="{{ route('data-umum.faskes.edit', $item) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </x-ui.button>
                                <form method="POST" action="{{ route('data-umum.faskes.destroy', $item) }}" onsubmit="return confirm('Hapus data faskes ini?')">
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
                            <div class="flex flex-col items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                <p>Belum ada data fasilitas kesehatan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($faskesList->hasPages())
            <div class="mt-4">{{ $faskesList->links() }}</div>
        @endif
    </x-ui.card>
</x-layouts.app>
