<x-layouts.app :title="'Data Asrama'">
    <x-slot:header>
        <x-layouts.page-header title="Data Asrama" description="Kelola data asrama di wilayah kelurahan">
            <x-slot:actions>
                <x-ui.import-export-buttons module="asrama" />
                <x-ui.button type="primary" size="sm" href="{{ route('data-umum.asrama.create') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Tambah Asrama
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    @if(session('success'))
        <x-ui.alert type="success" class="mb-4">{{ session('success') }}</x-ui.alert>
    @endif

    {{-- Filters --}}
    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('data-umum.asrama.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <x-ui.input name="search" placeholder="Cari nama, alamat..." value="{{ request('search') }}" />
            </div>
            <div class="w-full md:w-40">
                <x-ui.select name="jenis" placeholder="Semua Jenis" :options="$jenisOptions" selected="{{ request('jenis') }}" />
            </div>
            <div class="flex gap-2">
                <x-ui.button type="primary" size="md" :isSubmit="true">Cari</x-ui.button>
                <x-ui.button type="ghost" size="md" href="{{ route('data-umum.asrama.index') }}">Reset</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @foreach(['TNI', 'POLRI', 'Mahasiswa', 'Kerukunan'] as $jenis)
        <x-ui.stat
            :title="$jenis"
            :value="$summaryData[$jenis] ?? 0"
            description="unit"
        />
        @endforeach
    </div>

    {{-- Table --}}
    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th class="w-12">No</th>
                        <th>Nama / Alamat</th>
                        <th>Jenis</th>
                        <th class="text-center">Jumlah</th>
                        <th>RT/RW</th>
                        <th>Keterangan</th>
                        <th class="w-32 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($asramaList as $item)
                    <tr class="hover">
                        <td class="text-sm text-base-content/60">{{ $asramaList->firstItem() + $loop->index }}</td>
                        <td>
                            <div class="font-medium">{{ $item->nama ?? '-' }}</div>
                            <div class="text-xs text-base-content/60">{{ Str::limit($item->alamat, 35) }}</div>
                        </td>
                        <td><x-ui.badge type="primary" size="sm" :outline="true">{{ $item->jenis }}</x-ui.badge></td>
                        <td class="text-sm text-center font-semibold">{{ $item->jumlah ?? 0 }}</td>
                        <td class="text-sm">
                            @if($item->rt)
                                RT {{ $item->rt->nomor }} / RW {{ $item->rt->rw->nomor ?? '-' }}
                            @elseif($item->rw)
                                RW {{ $item->rw->nomor }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-sm text-base-content/60">{{ Str::limit($item->keterangan, 30) ?? '-' }}</td>
                        <td>
                            <div class="flex justify-end gap-1">
                                <x-ui.button type="ghost" size="xs" href="{{ route('data-umum.asrama.edit', $item) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </x-ui.button>
                                <x-ui.button type="error" size="xs" :outline="true" @click="$dispatch('confirm-delete', { action: '{{ route('data-umum.asrama.destroy', $item) }}', message: 'Hapus data asrama ini?' })">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </x-ui.button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-base-content/50">
                            <div class="flex flex-col items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                <p>Belum ada data asrama</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($asramaList->hasPages())
            <div class="mt-4">{{ $asramaList->links() }}</div>
        @endif
    </x-ui.card>
</x-layouts.app>
