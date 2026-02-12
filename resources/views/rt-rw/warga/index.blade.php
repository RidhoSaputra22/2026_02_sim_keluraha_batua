<x-layouts.app :title="'Data Warga'">

    <x-slot:header>
        <x-layouts.page-header
            title="Data Warga"
            description="Daftar penduduk di wilayah Anda"
        />
    </x-slot:header>

    {{-- Filter --}}
    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('rtrw.warga.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <x-ui.input label="Cari" name="search" placeholder="Cari nama atau NIK..." value="{{ request('search') }}" />
            </div>
            <div class="w-40">
                <x-ui.select label="Jenis Kelamin" name="jenis_kelamin" :options="['' => 'Semua', 'L' => 'Laki-laki', 'P' => 'Perempuan']" selected="{{ request('jenis_kelamin') }}" />
            </div>
            <div class="flex gap-2">
                <x-ui.button type="primary" size="sm" isSubmit>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    Cari
                </x-ui.button>
                <x-ui.button type="ghost" size="sm" href="{{ route('rtrw.warga.index') }}">Reset</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    {{-- Table --}}
    <x-ui.card title="Daftar Warga ({{ $penduduk->total() }})">
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>RT/RW</th>
                        <th>JK</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penduduk as $idx => $warga)
                    <tr class="hover">
                        <td class="text-xs text-base-content/50">{{ $penduduk->firstItem() + $idx }}</td>
                        <td class="font-mono text-xs">{{ $warga->nik }}</td>
                        <td class="font-medium">{{ $warga->nama }}</td>
                        <td class="text-sm max-w-[200px] truncate">{{ $warga->alamat ?? '-' }}</td>
                        <td class="text-xs">
                            RT {{ str_pad($warga->rt?->nomor ?? '-', 3, '0', STR_PAD_LEFT) }}
                            / RW {{ str_pad($warga->rt?->rw?->nomor ?? '-', 3, '0', STR_PAD_LEFT) }}
                        </td>
                        <td>
                            @if($warga->jenis_kelamin === 'L')
                                <x-ui.badge type="info" size="xs">L</x-ui.badge>
                            @elseif($warga->jenis_kelamin === 'P')
                                <x-ui.badge type="secondary" size="xs">P</x-ui.badge>
                            @else
                                <x-ui.badge type="ghost" size="xs">-</x-ui.badge>
                            @endif
                        </td>
                        <td>
                            @if($warga->status_data === 'aktif' || !$warga->status_data)
                                <x-ui.badge type="success" size="xs">Aktif</x-ui.badge>
                            @else
                                <x-ui.badge type="warning" size="xs">{{ ucfirst($warga->status_data) }}</x-ui.badge>
                            @endif
                        </td>
                        <td>
                            <x-ui.button type="ghost" size="xs" href="{{ route('rtrw.warga.show', $warga) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                Detail
                            </x-ui.button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-8">
                            <div class="text-base-content/50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                <p>Belum ada data warga di wilayah Anda.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($penduduk->hasPages())
        <div class="mt-4">
            {{ $penduduk->links() }}
        </div>
        @endif
    </x-ui.card>

</x-layouts.app>
