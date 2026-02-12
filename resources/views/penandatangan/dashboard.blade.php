<x-layouts.app :title="'Dashboard Penandatangan'">

    <x-slot:header>
        <x-layouts.page-header
            title="Dashboard Penandatangan"
            description="Tanda tangan dan finalisasi surat resmi"
        />
    </x-slot:header>

    @if(session('success'))
        <x-ui.alert type="success" class="mb-4">{{ session('success') }}</x-ui.alert>
    @endif
    @if(session('error'))
        <x-ui.alert type="error" class="mb-4">{{ session('error') }}</x-ui.alert>
    @endif

    {{-- Statistics --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-ui.card class="bg-warning/5">
            <x-ui.stat title="Menunggu TTD" value="{{ $suratMenungguTtd }}" description="Siap ditandatangani">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>

        <x-ui.card class="bg-success/5">
            <x-ui.stat title="Ditandatangani Hari Ini" value="{{ $suratDitandatanganiHariIni }}" description="Finalized">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>

        <x-ui.card class="bg-primary/5">
            <x-ui.stat title="Total TTD Bulan Ini" value="{{ $totalTtdBulanIni }}" description="Bulan berjalan">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>

        <x-ui.card class="bg-error/5">
            <x-ui.stat title="Ditolak Bulan Ini" value="{{ $suratDitolakPenandatangan }}" description="Dikembalikan ke verifikator">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>
    </div>

    {{-- Antrian TTD & Riwayat --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2">
            <x-ui.card title="Antrian Tanda Tangan">
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Jenis Surat</th>
                                <th>Pemohon</th>
                                <th>No. Surat</th>
                                <th>Verifikator</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($antrian as $s)
                            <tr class="hover">
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="font-medium">{{ $s->jenis->nama ?? '-' }}</div>
                                    @if($s->sifat)
                                    <span class="badge badge-xs {{ $s->sifat->nama === 'Sangat Segera' ? 'badge-error' : ($s->sifat->nama === 'Segera' ? 'badge-warning' : 'badge-ghost') }}">{{ $s->sifat->nama }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="font-medium">{{ $s->pemohon->nama ?? '-' }}</div>
                                    <div class="text-xs text-base-content/60">{{ $s->pemohon->penduduk->nik ?? '-' }}</div>
                                </td>
                                <td class="font-mono text-xs">{{ $s->nomor_surat ?? '-' }}</td>
                                <td class="text-xs">{{ $s->verifikator->name ?? '-' }}</td>
                                <td>
                                    <x-ui.button type="primary" size="xs" href="{{ route('persuratan.tanda-tangan.index') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        TTD
                                    </x-ui.button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-base-content/50">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <p>Tidak ada surat yang perlu ditandatangani.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($antrian->count() > 0)
                <x-slot:actions>
                    <x-ui.button type="ghost" size="sm" href="{{ route('persuratan.tanda-tangan.index') }}">Lihat Semua →</x-ui.button>
                </x-slot:actions>
                @endif
            </x-ui.card>
        </div>

        {{-- Sidebar Info --}}
        <div class="space-y-6">
            <x-ui.card title="Status Hari Ini">
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm">Menunggu TTD</span>
                        <x-ui.badge type="warning">{{ $suratMenungguTtd }}</x-ui.badge>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm">Ditandatangani</span>
                        <x-ui.badge type="success">{{ $suratDitandatanganiHariIni }}</x-ui.badge>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm">Dikembalikan</span>
                        <x-ui.badge type="error">{{ $dikembalikanHariIni }}</x-ui.badge>
                    </div>
                    <div class="divider my-1"></div>
                    <div class="flex justify-between items-center font-semibold">
                        <span class="text-sm">Total Proses</span>
                        <x-ui.badge type="primary">{{ $suratDitandatanganiHariIni + $dikembalikanHariIni }}</x-ui.badge>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card title="Surat Terbaru Ditandatangani">
                <div class="space-y-3">
                    @forelse($riwayat as $r)
                    <div class="flex items-start gap-3">
                        <div class="badge badge-success badge-xs mt-1.5"></div>
                        <div>
                            <p class="text-sm font-medium">{{ $r->jenis->nama ?? '-' }} — {{ Str::limit($r->pemohon->nama ?? '-', 15) }}</p>
                            <p class="text-xs text-base-content/60">{{ $r->nomor_surat ?? '-' }} · {{ $r->tgl_ttd?->format('H:i') }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-base-content/50 text-center py-4">Belum ada surat ditandatangani.</p>
                    @endforelse
                </div>
            </x-ui.card>

            {{-- Quick Link --}}
            <x-ui.card>
                <x-ui.button type="primary" size="sm" class="w-full" href="{{ route('persuratan.tanda-tangan.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                    Halaman Tanda Tangan Lengkap
                </x-ui.button>
            </x-ui.card>
        </div>
    </div>

    {{-- Rekap Surat per Jenis --}}
    <x-ui.card title="Rekap Penandatanganan Bulan Ini">
        @if($rekapJenis->count() > 0)
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            @foreach($rekapJenis as $rj)
            <div class="text-center bg-base-200/50 rounded-lg p-3">
                <div class="text-2xl font-bold text-primary">{{ $rj->total }}</div>
                <div class="text-xs text-base-content/70">{{ $rj->jenis->nama ?? '-' }}</div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-sm text-base-content/50 text-center py-4">Belum ada surat ditandatangani bulan ini.</p>
        @endif
    </x-ui.card>

</x-layouts.app>
