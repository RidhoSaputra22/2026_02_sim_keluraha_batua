<x-layouts.app :title="'Mutasi Penduduk'">
    <x-slot:header>
        <x-layouts.page-header title="Mutasi Penduduk" description="Kelola data mutasi penduduk (pindah & datang)">
            <x-slot:actions>
                <x-ui.button type="primary" size="sm" href="{{ route('kependudukan.mutasi.create') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Tambah Mutasi
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    @if(session('success'))
        <x-ui.alert type="success" class="mb-4">{{ session('success') }}</x-ui.alert>
    @endif

    {{-- Filters --}}
    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('kependudukan.mutasi.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <x-ui.input name="search" placeholder="Cari nama, NIK, no surat pindah..." value="{{ request('search') }}" />
            </div>
            <div class="w-full md:w-40">
                <x-ui.select name="jenis_mutasi" placeholder="Semua Jenis" :options="['pindah' => 'Pindah', 'datang' => 'Datang']" selected="{{ request('jenis_mutasi') }}" />
            </div>
            <div class="w-full md:w-40">
                <x-ui.select name="status" placeholder="Semua Status" :options="['proses' => 'Proses', 'selesai' => 'Selesai', 'batal' => 'Batal']" selected="{{ request('status') }}" />
            </div>
            <div class="flex gap-2">
                <x-ui.button type="primary" size="md">Cari</x-ui.button>
                <x-ui.button type="ghost" size="md" href="{{ route('kependudukan.mutasi.index') }}">Reset</x-ui.button>
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
                        <th>Penduduk</th>
                        <th>Jenis</th>
                        <th>Tanggal</th>
                        <th>Asal / Tujuan</th>
                        <th>Alasan</th>
                        <th class="text-center">Status</th>
                        <th class="w-32 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mutasi as $m)
                    <tr class="hover">
                        <td class="text-sm text-base-content/60">{{ $mutasi->firstItem() + $loop->index }}</td>
                        <td>
                            <div class="font-medium">{{ $m->penduduk->nama ?? '-' }}</div>
                            <div class="text-xs text-base-content/60">NIK: {{ $m->penduduk->nik ?? '-' }}</div>
                        </td>
                        <td>
                            @if($m->jenis_mutasi === 'pindah')
                                <x-ui.badge type="warning" size="sm">Pindah</x-ui.badge>
                            @else
                                <x-ui.badge type="info" size="sm">Datang</x-ui.badge>
                            @endif
                        </td>
                        <td class="text-sm">{{ $m->tanggal_mutasi->format('d/m/Y') }}</td>
                        <td class="text-sm">
                            @if($m->jenis_mutasi === 'pindah')
                                <div><span class="text-base-content/50">Dari:</span> {{ $m->alamat_asal ?? ($m->rtAsal ? 'RT '.$m->rtAsal->nomor.'/RW '.$m->rtAsal->rw->nomor : '-') }}</div>
                                <div><span class="text-base-content/50">Ke:</span> {{ $m->alamat_tujuan ?? '-' }}</div>
                            @else
                                <div><span class="text-base-content/50">Dari:</span> {{ $m->alamat_asal ?? '-' }}</div>
                                <div><span class="text-base-content/50">Ke:</span> {{ $m->alamat_tujuan ?? ($m->rtTujuan ? 'RT '.$m->rtTujuan->nomor.'/RW '.$m->rtTujuan->rw->nomor : '-') }}</div>
                            @endif
                        </td>
                        <td class="text-sm">{{ Str::limit($m->alasan, 30) ?? '-' }}</td>
                        <td class="text-center">
                            @php
                                $statusType = match($m->status) {
                                    'proses' => 'warning',
                                    'selesai' => 'success',
                                    'batal' => 'error',
                                    default => 'ghost',
                                };
                            @endphp
                            <x-ui.badge :type="$statusType" size="sm">{{ ucfirst($m->status) }}</x-ui.badge>
                        </td>
                        <td>
                            <div class="flex justify-end gap-1">
                                <x-ui.button type="ghost" size="xs" href="{{ route('kependudukan.mutasi.edit', $m) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </x-ui.button>
                                <form method="POST" action="{{ route('kependudukan.mutasi.destroy', $m) }}" onsubmit="return confirm('Hapus data mutasi ini?')">
                                    @csrf @method('DELETE')
                                    <x-ui.button type="error" size="xs" :outline="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </x-ui.button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-8 text-base-content/60">Tidak ada data mutasi penduduk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($mutasi->hasPages())
            <div class="mt-4">{{ $mutasi->links() }}</div>
        @endif
    </x-ui.card>
</x-layouts.app>
