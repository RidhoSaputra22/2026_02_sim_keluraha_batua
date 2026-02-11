<x-layouts.app :title="'Arsip Surat'">
    <x-slot:header>
        <x-layouts.page-header title="Arsip Surat" description="Arsip surat yang telah ditandatangani dan selesai diproses">
        </x-layouts.page-header>
    </x-slot:header>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        @php
            $totalArsip = \App\Models\Surat::where('status_esign', 'signed')->count();
            $arsipBulanIni = \App\Models\Surat::where('status_esign', 'signed')->whereMonth('tgl_ttd', now()->month)->whereYear('tgl_ttd', now()->year)->count();
            $jenisCount = \App\Models\Surat::where('status_esign', 'signed')->distinct('jenis_id')->count('jenis_id');
        @endphp
        <x-ui.stat title="Total Arsip" :value="$totalArsip" description="Seluruh surat selesai" />
        <x-ui.stat title="Bulan Ini" :value="$arsipBulanIni" description="{{ now()->translatedFormat('F Y') }}" />
        <x-ui.stat title="Jenis Surat" :value="$jenisCount" description="Variasi jenis" />
    </div>

    {{-- Filters --}}
    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('persuratan.arsip.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <x-ui.input name="search" placeholder="Cari nomor surat, perihal, pemohon..." value="{{ request('search') }}" />
            </div>
            <div class="w-full md:w-48">
                <x-ui.select name="jenis_id" placeholder="Semua Jenis" :options="$jenisList->pluck('nama', 'id')->toArray()" selected="{{ request('jenis_id') }}" />
            </div>
            <div class="w-full md:w-40">
                <x-ui.input name="dari_tanggal" type="date" placeholder="Dari tanggal" value="{{ request('dari_tanggal') }}" />
            </div>
            <div class="w-full md:w-40">
                <x-ui.input name="sampai_tanggal" type="date" placeholder="Sampai tanggal" value="{{ request('sampai_tanggal') }}" />
            </div>
            <div class="flex gap-2">
                <x-ui.button type="primary" size="md">Cari</x-ui.button>
                <x-ui.button type="ghost" size="md" href="{{ route('persuratan.arsip.index') }}">Reset</x-ui.button>
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
                        <th>Nomor Surat</th>
                        <th>Jenis / Perihal</th>
                        <th>Pemohon</th>
                        <th>Tanggal Surat</th>
                        <th>Penandatangan</th>
                        <th>Tgl TTD</th>
                        <th class="text-center">Arsip</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($surats as $s)
                    <tr class="hover">
                        <td class="text-sm text-base-content/60">{{ $surats->firstItem() + $loop->index }}</td>
                        <td>
                            <div class="font-mono text-sm font-medium">{{ $s->nomor_surat }}</div>
                            <div class="text-xs text-base-content/50">{{ $s->arah === 'masuk' ? 'Masuk' : 'Keluar' }}</div>
                        </td>
                        <td>
                            <div class="font-medium">{{ $s->jenis->nama ?? '-' }}</div>
                            <div class="text-xs text-base-content/60">{{ Str::limit($s->perihal, 40) }}</div>
                        </td>
                        <td>
                            <div class="font-medium">{{ $s->pemohon->nama ?? '-' }}</div>
                            <div class="text-xs text-base-content/60">{{ $s->pemohon->penduduk->nik ?? '-' }}</div>
                        </td>
                        <td class="text-sm">{{ $s->tanggal_surat?->format('d/m/Y') ?? '-' }}</td>
                        <td class="text-sm">{{ $s->penandatanganPejabat->pegawai->nama ?? '-' }}</td>
                        <td class="text-sm">{{ $s->tgl_ttd?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td class="text-center">
                            @if($s->arsip_path)
                                <a href="{{ asset('storage/' . $s->arsip_path) }}" target="_blank" class="btn btn-xs btn-ghost text-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    PDF
                                </a>
                            @else
                                <span class="text-xs text-base-content/40">Belum ada</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-8 text-base-content/50">
                            <div class="flex flex-col items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                                <p>Belum ada arsip surat.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $surats->links() }}
        </div>
    </x-ui.card>
</x-layouts.app>
