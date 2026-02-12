<x-layouts.app :title="'Data Keluarga'">

    <x-slot:header>
        <x-layouts.page-header
            title="Data Keluarga"
            description="Daftar kartu keluarga di wilayah Anda"
        />
    </x-slot:header>

    {{-- Filter --}}
    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('rtrw.keluarga.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <x-ui.input label="Cari" name="search" placeholder="Cari No KK atau nama kepala keluarga..." value="{{ request('search') }}" />
            </div>
            <div class="flex gap-2">
                <x-ui.button type="primary" size="sm" isSubmit>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    Cari
                </x-ui.button>
                <x-ui.button type="ghost" size="sm" href="{{ route('rtrw.keluarga.index') }}">Reset</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    {{-- Table --}}
    <x-ui.card title="Daftar Keluarga ({{ $keluarga->total() }})">
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No. KK</th>
                        <th>Kepala Keluarga</th>
                        <th>RT/RW</th>
                        <th>Jumlah Anggota</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($keluarga as $idx => $kk)
                    <tr class="hover">
                        <td class="text-xs text-base-content/50">{{ $keluarga->firstItem() + $idx }}</td>
                        <td class="font-mono text-xs">{{ $kk->no_kk }}</td>
                        <td class="font-medium">{{ $kk->kepalaKeluarga?->nama ?? '-' }}</td>
                        <td class="text-xs">
                            RT {{ str_pad($kk->rt?->nomor ?? '-', 3, '0', STR_PAD_LEFT) }}
                            / RW {{ str_pad($kk->rt?->rw?->nomor ?? '-', 3, '0', STR_PAD_LEFT) }}
                        </td>
                        <td>
                            <x-ui.badge type="info" size="xs">{{ $kk->jumlah_anggota_keluarga }} orang</x-ui.badge>
                        </td>
                        <td>
                            @if($kk->kepalaKeluarga)
                            <x-ui.button type="ghost" size="xs" href="{{ route('rtrw.warga.show', $kk->kepalaKeluarga) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                Detail KK
                            </x-ui.button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-8">
                            <div class="text-base-content/50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                                <p>Belum ada data keluarga di wilayah Anda.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($keluarga->hasPages())
        <div class="mt-4">
            {{ $keluarga->links() }}
        </div>
        @endif
    </x-ui.card>

</x-layouts.app>
