<x-layouts.app :title="'Laporan Persuratan'">
    <x-slot:header>
        <x-layouts.page-header title="Laporan Persuratan" description="Statistik dan ringkasan layanan persuratan">
        </x-layouts.page-header>
    </x-slot:header>

    {{-- Summary Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <x-ui.stat title="Total Surat" :value="number_format($totalSurat)" description="Semua surat" />
        <x-ui.stat title="Draft" :value="$totalDraft" description="Menunggu verifikasi" />
        <x-ui.stat title="Proses" :value="$totalProses" description="Menunggu tanda tangan" />
        <x-ui.stat title="Selesai" :value="$totalSigned" description="Sudah ditandatangani" />
        <x-ui.stat title="Ditolak" :value="$totalReject" description="Perlu perbaikan" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Per Jenis Surat --}}
        <x-ui.card>
            <h3 class="text-lg font-semibold mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Per Jenis Surat
            </h3>
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Jenis Surat</th>
                            <th class="text-center">Jumlah</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($perJenis as $jenis)
                        <tr>
                            <td class="font-medium">{{ $jenis->nama }}</td>
                            <td class="text-center">
                                <span class="badge badge-primary badge-sm">{{ $jenis->surats_count }}</span>
                            </td>
                            <td>
                                @php $pct = $totalSurat > 0 ? round(($jenis->surats_count / $totalSurat) * 100, 1) : 0; @endphp
                                <div class="flex items-center gap-2">
                                    <progress class="progress progress-primary w-20" value="{{ $pct }}" max="100"></progress>
                                    <span class="text-xs">{{ $pct }}%</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-base-content/50">Belum ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        {{-- Per Status --}}
        <x-ui.card>
            <h3 class="text-lg font-semibold mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                Per Status
            </h3>
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th class="text-center">Jumlah</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($perStatus as $item)
                        <tr>
                            <td>
                                @php
                                    $statusClass = match($item->status_esign) {
                                        'draft' => 'badge-warning',
                                        'proses' => 'badge-info',
                                        'signed' => 'badge-success',
                                        'reject' => 'badge-error',
                                        default => 'badge-ghost',
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }} badge-sm">{{ ucfirst($item->status_esign) }}</span>
                            </td>
                            <td class="text-center">
                                <span class="font-semibold">{{ $item->total }}</span>
                            </td>
                            <td>
                                @php $pct = $totalSurat > 0 ? round(($item->total / $totalSurat) * 100, 1) : 0; @endphp
                                <div class="flex items-center gap-2">
                                    <progress class="progress progress-secondary w-20" value="{{ $pct }}" max="100"></progress>
                                    <span class="text-xs">{{ $pct }}%</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-base-content/50">Belum ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>

    {{-- Per Bulan --}}
    <x-ui.card class="mb-6">
        <h3 class="text-lg font-semibold mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            Surat Per Bulan ({{ now()->year }})
        </h3>
        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Bulan</th>
                        <th class="text-center">Jumlah Surat</th>
                        <th>Grafik</th>
                    </tr>
                </thead>
                <tbody>
                    @php $maxBulan = $perBulan->max() ?: 1; @endphp
                    @forelse($perBulan as $bulan => $total)
                    <tr>
                        <td class="font-medium">{{ $bulan }}</td>
                        <td class="text-center">
                            <span class="badge badge-primary badge-sm">{{ $total }}</span>
                        </td>
                        <td>
                            <progress class="progress progress-primary w-40" value="{{ $total }}" max="{{ $maxBulan }}"></progress>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-base-content/50">Belum ada data surat tahun ini</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    {{-- Surat Terbaru --}}
    <x-ui.card>
        <h3 class="text-lg font-semibold mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            Surat Terbaru
        </h3>
        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Nomor Surat</th>
                        <th>Jenis</th>
                        <th>Pemohon</th>
                        <th>Tanggal</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suratTerbaru as $s)
                    <tr>
                        <td class="font-mono text-sm">{{ $s->nomor_surat ?? '-' }}</td>
                        <td>{{ $s->jenis->nama ?? '-' }}</td>
                        <td>{{ $s->pemohon->nama ?? '-' }}</td>
                        <td class="text-sm">{{ $s->tgl_input?->format('d/m/Y') ?? '-' }}</td>
                        <td class="text-center">
                            @php
                                $sc = match($s->status_esign) {
                                    'draft' => 'badge-warning',
                                    'proses' => 'badge-info',
                                    'signed' => 'badge-success',
                                    'reject' => 'badge-error',
                                    default => 'badge-ghost',
                                };
                            @endphp
                            <span class="badge {{ $sc }} badge-sm">{{ ucfirst($s->status_esign ?? '-') }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-base-content/50">Belum ada data surat</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
</x-layouts.app>
