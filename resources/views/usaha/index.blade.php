<x-layouts.app :title="'Daftar Usaha'">
    <x-slot:header>
        <x-layouts.page-header title="Daftar Usaha" description="Kelola data usaha UMKM/PK5 di kelurahan">
            <x-slot:actions>
                <x-ui.button type="primary" size="sm" href="{{ route('usaha.create') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Tambah Usaha
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

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @php
            $totalUsaha = \App\Models\Umkm::count();
            $totalAktif = \App\Models\Umkm::where('status', 'aktif')->count();
            $totalTidakAktif = \App\Models\Umkm::where('status', 'tidak_aktif')->count();
            $totalJenis = \App\Models\JenisUsaha::count();
        @endphp
        <x-ui.stat title="Total Usaha" :value="$totalUsaha" description="Semua data usaha" />
        <x-ui.stat title="Aktif" :value="$totalAktif" description="Usaha aktif" />
        <x-ui.stat title="Tidak Aktif" :value="$totalTidakAktif" description="Usaha tidak aktif" />
        <x-ui.stat title="Jenis Usaha" :value="$totalJenis" description="Kategori usaha" />
    </div>

    {{-- Filters --}}
    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('usaha.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <x-ui.input name="search" placeholder="Cari nama usaha, pemilik, NIK, alamat..." value="{{ request('search') }}" />
            </div>
            <div class="w-full md:w-48">
                <x-ui.select name="jenis_usaha_id" placeholder="Semua Jenis" :options="$jenisUsahaList->pluck('nama', 'id')->toArray()" selected="{{ request('jenis_usaha_id') }}" />
            </div>
            <div class="w-full md:w-40">
                <x-ui.select name="status" placeholder="Semua Status" :options="['aktif' => 'Aktif', 'tidak_aktif' => 'Tidak Aktif']" selected="{{ request('status') }}" />
            </div>
            <div class="w-full md:w-48">
                <x-ui.select name="sektor_umkm" placeholder="Semua Sektor" :options="$sektorList->mapWithKeys(fn($s) => [$s => $s])->toArray()" selected="{{ request('sektor_umkm') }}" />
            </div>
            <div class="flex gap-2">
                <x-ui.button type="primary" size="md">Cari</x-ui.button>
                <x-ui.button type="ghost" size="md" href="{{ route('usaha.index') }}">Reset</x-ui.button>
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
                        <th>Nama Usaha</th>
                        <th>Pemilik</th>
                        <th>Jenis</th>
                        <th>Sektor</th>
                        <th>Lokasi</th>
                        <th class="text-center">Status</th>
                        <th class="w-32 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usahaList as $u)
                    <tr class="hover">
                        <td class="text-sm text-base-content/60">{{ $usahaList->firstItem() + $loop->index }}</td>
                        <td>
                            <div class="font-medium">{{ $u->nama_ukm }}</div>
                            <div class="text-xs text-base-content/60">{{ Str::limit($u->alamat, 40) }}</div>
                        </td>
                        <td>
                            <div class="font-medium">{{ $u->nama_pemilik }}</div>
                            <div class="text-xs text-base-content/60">{{ $u->nik_pemilik ?? '-' }}</div>
                        </td>
                        <td class="text-sm">{{ $u->jenisUsaha->nama ?? '-' }}</td>
                        <td class="text-sm">{{ $u->sektor_umkm ?? '-' }}</td>
                        <td class="text-sm">
                            @if($u->rt && $u->rt->rw)
                                RT {{ $u->rt->nomor }} / RW {{ $u->rt->rw->nomor }}
                            @else
                                {{ $u->kelurahan->nama ?? '-' }}
                            @endif
                        </td>
                        <td class="text-center">
                            @php
                                $statusType = match($u->status) {
                                    'aktif' => 'success',
                                    'tidak_aktif' => 'error',
                                    default => 'ghost',
                                };
                            @endphp
                            <x-ui.badge :type="$statusType" size="sm">{{ ucfirst(str_replace('_', ' ', $u->status ?? 'N/A')) }}</x-ui.badge>
                        </td>
                        <td>
                            <div class="flex justify-end gap-1">
                                <x-ui.button type="ghost" size="xs" href="{{ route('usaha.edit', $u) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </x-ui.button>
                                <form method="POST" action="{{ route('usaha.destroy', $u) }}" onsubmit="return confirm('Hapus data usaha ini?')">
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
                        <td colspan="8" class="text-center py-8 text-base-content/50">
                            <div class="flex flex-col items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                <p>Belum ada data usaha</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($usahaList->hasPages())
            <div class="mt-4">
                {{ $usahaList->links() }}
            </div>
        @endif
    </x-ui.card>
</x-layouts.app>
