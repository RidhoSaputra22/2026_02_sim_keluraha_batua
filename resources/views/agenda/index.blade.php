<x-layouts.app :title="'Agenda Kegiatan'">
    <x-slot:header>
        <x-layouts.page-header title="Agenda & Kegiatan" description="Kelola agenda dan kegiatan kelurahan">
            <x-slot:actions>
                <x-ui.button type="primary" size="sm" href="{{ route('agenda.create') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Tambah Agenda
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    @if(session('success'))
        <x-ui.alert type="success" class="mb-4">{{ session('success') }}</x-ui.alert>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @php
            $totalAgenda = \App\Models\AgendaKegiatan::count();
            $bulanIni = \App\Models\AgendaKegiatan::whereMonth('hari_kegiatan', now()->month)->whereYear('hari_kegiatan', now()->year)->count();
            $adaHasil = \App\Models\AgendaKegiatan::has('hasil')->count();
            $belumHasil = $totalAgenda - $adaHasil;
        @endphp
        <x-ui.stat title="Total Agenda" :value="$totalAgenda" description="Semua kegiatan" />
        <x-ui.stat title="Bulan Ini" :value="$bulanIni" description="{{ now()->translatedFormat('F Y') }}" />
        <x-ui.stat title="Ada Hasil" :value="$adaHasil" description="Sudah ada notulen" />
        <x-ui.stat title="Belum Hasil" :value="$belumHasil" description="Belum ada notulen" />
    </div>

    {{-- Filters --}}
    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('agenda.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <x-ui.input name="search" placeholder="Cari perihal, lokasi, penanggung jawab..." value="{{ request('search') }}" />
            </div>
            <div class="w-full md:w-36">
                <x-ui.select name="bulan" placeholder="Semua Bulan" :options="[
                    '1' => 'Januari', '2' => 'Februari', '3' => 'Maret', '4' => 'April',
                    '5' => 'Mei', '6' => 'Juni', '7' => 'Juli', '8' => 'Agustus',
                    '9' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                ]" selected="{{ request('bulan') }}" />
            </div>
            <div class="w-full md:w-28">
                <x-ui.input name="tahun" placeholder="Tahun" value="{{ request('tahun') }}" />
            </div>
            <div class="flex gap-2">
                <x-ui.button type="primary" size="md">Cari</x-ui.button>
                <x-ui.button type="ghost" size="md" href="{{ route('agenda.index') }}">Reset</x-ui.button>
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
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Perihal</th>
                        <th>Lokasi</th>
                        <th>Instansi</th>
                        <th>PJ</th>
                        <th>Hasil</th>
                        <th class="w-36 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($agendaList as $item)
                    <tr class="hover">
                        <td class="text-sm text-base-content/60">{{ $agendaList->firstItem() + $loop->index }}</td>
                        <td class="text-sm">{{ $item->hari_kegiatan?->translatedFormat('d M Y') ?? '-' }}</td>
                        <td class="text-sm">{{ $item->jam ?? '-' }}</td>
                        <td>
                            <div class="font-medium">{{ Str::limit($item->perihal, 40) }}</div>
                            @if($item->keterangan)
                                <div class="text-xs text-base-content/60">{{ Str::limit($item->keterangan, 30) }}</div>
                            @endif
                        </td>
                        <td class="text-sm">{{ $item->lokasi ?? '-' }}</td>
                        <td class="text-sm">{{ $item->instansi->nama ?? '-' }}</td>
                        <td class="text-sm">{{ $item->penanggung_jawab ?? '-' }}</td>
                        <td class="text-center">
                            @if($item->hasil)
                                <span class="badge badge-success badge-sm">Ada</span>
                            @else
                                <span class="badge badge-ghost badge-sm">Belum</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex justify-end gap-1">
                                <x-ui.button type="info" size="xs" :outline="true" href="{{ route('agenda.show', $item) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </x-ui.button>
                                <x-ui.button type="ghost" size="xs" href="{{ route('agenda.edit', $item) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </x-ui.button>
                                <form method="POST" action="{{ route('agenda.destroy', $item) }}" onsubmit="return confirm('Hapus agenda ini?')">
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
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                <p>Belum ada agenda kegiatan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($agendaList->hasPages())
            <div class="mt-4">{{ $agendaList->links() }}</div>
        @endif
    </x-ui.card>
</x-layouts.app>
