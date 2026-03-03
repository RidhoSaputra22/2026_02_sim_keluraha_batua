<x-layouts.app :title="'Data Retribusi Sampah'">
    <x-slot:header>
        <x-layouts.page-header title="Data Retribusi Sampah"
            description="Kelola data retribusi sampah nasabah di wilayah kelurahan">
            <x-slot:actions>
                <x-ui.import-export-buttons module="retribusi-sampah" />
                <x-ui.button type="primary" size="sm" href="{{ route('data-umum.retribusi-sampah.create') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Data
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    @if (session('success'))
        <x-ui.alert type="success" class="mb-4">{{ session('success') }}</x-ui.alert>
    @endif

    {{-- Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <x-ui.stat title="Total Nasabah" value="{{ $totalNasabah }}" description="nasabah terdaftar" />
        <x-ui.stat title="Total Beban" value="Rp {{ number_format($totalBeban, 0, ',', '.') }}"
            description="seluruh retribusi" />
        <x-ui.stat title="Lunas" value="Rp {{ number_format($totalLunas, 0, ',', '.') }}" description="sudah lunas" />
        <x-ui.stat title="Belum Lunas" value="Rp {{ number_format($totalBelum, 0, ',', '.') }}"
            description="belum dibayar" />
    </div>

    {{-- Filters --}}
    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('data-umum.retribusi-sampah.index') }}"
            class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <x-ui.input name="search" placeholder="Cari nama nasabah, alamat, No. SKRD..."
                    value="{{ request('search') }}" />
            </div>
            <div class="w-full md:w-40">
                <x-ui.select name="status" placeholder="Semua Status"
                    :options="['Lunas' => 'Lunas', 'Belum' => 'Belum']" selected="{{ request('status') }}" />
            </div>
            <div class="w-full md:w-40">
                <x-ui.select name="tahun" placeholder="Semua Tahun"
                    :options="$tahunList->mapWithKeys(fn($t) => [$t => $t])->toArray()"
                    selected="{{ request('tahun') }}" />
            </div>
            <div class="flex gap-2">
                <x-ui.button type="primary" size="md" :isSubmit="true">Cari</x-ui.button>
                <x-ui.button type="ghost" size="md"
                    href="{{ route('data-umum.retribusi-sampah.index') }}">Reset</x-ui.button>
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
                        <th>Nama Nasabah</th>
                        <th>Alamat</th>
                        <th>No. SKRD</th>
                        <th class="text-right">Beban (Rp)</th>
                        <th class="text-center">Status</th>
                        <th>RT/RW</th>
                        <th class="w-32 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($retribusiList as $item)
                        <tr class="hover">
                            <td class="text-sm text-base-content/60">
                                {{ $retribusiList->firstItem() + $loop->index }}</td>
                            <td class="font-medium">{{ $item->nama_nasabah }}</td>
                            <td class="text-sm">{{ $item->alamat ?? '-' }}</td>
                            <td class="text-sm font-mono">{{ $item->no_skrd ?? '-' }}</td>
                            <td class="text-sm text-right font-semibold">
                                {{ number_format($item->beban ?? 0, 0, ',', '.') }}</td>
                            <td class="text-center">
                                @if ($item->status === 'Lunas')
                                    <x-ui.badge type="success" size="sm">Lunas</x-ui.badge>
                                @else
                                    <x-ui.badge type="warning" size="sm">Belum</x-ui.badge>
                                @endif
                            </td>
                            <td class="text-sm">
                                @if ($item->rt)
                                    RT {{ $item->rt->nomor }} / RW {{ $item->rt->rw->nomor ?? '-' }}
                                @elseif($item->rw)
                                    RW {{ $item->rw->nomor }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <div class="flex justify-end gap-1">
                                    <x-ui.button type="ghost" size="xs"
                                        href="{{ route('data-umum.retribusi-sampah.edit', $item) }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </x-ui.button>
                                    <x-ui.button type="error" size="xs" :outline="true"
                                        @click="$dispatch('confirm-delete', { action: '{{ route('data-umum.retribusi-sampah.destroy', $item) }}', message: 'Hapus data retribusi ini?' })">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </x-ui.button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-base-content/50">
                                <div class="flex flex-col items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 opacity-30" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <p>Belum ada data retribusi sampah</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($retribusiList->hasPages())
            <div class="mt-4">{{ $retribusiList->links() }}</div>
        @endif
    </x-ui.card>
</x-layouts.app>
