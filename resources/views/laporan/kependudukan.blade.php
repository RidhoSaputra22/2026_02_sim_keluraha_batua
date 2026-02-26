<x-layouts.app :title="'Laporan Kependudukan'">
    <x-slot:header>
        <x-layouts.page-header title="Laporan Kependudukan" description="Statistik dan ringkasan data kependudukan">
        </x-layouts.page-header>
    </x-slot:header>

    {{-- Summary Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <x-ui.stat title="Total Penduduk" :value="number_format($totalPenduduk)" description="Jiwa terdaftar" />
        <x-ui.stat title="Kartu Keluarga" :value="number_format($totalKK)" description="KK terdaftar" />
        <x-ui.stat title="Laki-laki" :value="number_format($totalLaki)" description="Penduduk pria" />
        <x-ui.stat title="Perempuan" :value="number_format($totalPerempuan)" description="Penduduk wanita" />
        <x-ui.stat title="Mutasi Bulan Ini" :value="$mutasiBulanIni" description="Pindah & datang" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Per RW --}}
        <x-ui.card>
            <h3 class="text-lg font-semibold mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                Penduduk Per RW
            </h3>
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>RW</th>
                            <th class="text-center">Jumlah RT</th>
                            <th class="text-center">Penduduk</th>
                            <th class="text-center">KK</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($perRw as $rw)
                        <tr>
                            <td class="font-medium">RW {{ $rw->nomor }}</td>
                            <td class="text-center">{{ $rw->rts_count }}</td>
                            <td class="text-center">
                                <x-ui.badge type="primary" size="sm">{{ $rw->jumlah_penduduk }}</x-ui.badge>
                            </td>
                            <td class="text-center">
                                <x-ui.badge type="accent" size="sm">{{ $rw->jumlah_kk }}</x-ui.badge>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-base-content/50">Belum ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        {{-- Per Agama --}}
        <x-ui.card>
            <h3 class="text-lg font-semibold mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                Penduduk Per Agama
            </h3>
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Agama</th>
                            <th class="text-center">Jumlah</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($perAgama as $item)
                        <tr>
                            <td class="font-medium">{{ $item->agama ?? 'Tidak diketahui' }}</td>
                            <td class="text-center">
                                <x-ui.badge type="primary" size="sm">{{ number_format($item->total) }}</x-ui.badge>
                            </td>
                            <td>
                                @php $pct = $totalPenduduk > 0 ? round(($item->total / $totalPenduduk) * 100, 1) : 0; @endphp
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

        {{-- Per Pendidikan --}}
        <x-ui.card>
            <h3 class="text-lg font-semibold mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 14l9-5-9-5-9 5 9 5z" /><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" /></svg>
                Penduduk Per Pendidikan
            </h3>
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Pendidikan</th>
                            <th class="text-center">Jumlah</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($perPendidikan as $item)
                        <tr>
                            <td class="font-medium">{{ $item->pendidikan ?? 'Tidak diketahui' }}</td>
                            <td class="text-center">
                                <x-ui.badge type="secondary" size="sm">{{ number_format($item->total) }}</x-ui.badge>
                            </td>
                            <td>
                                @php $pct = $totalPenduduk > 0 ? round(($item->total / $totalPenduduk) * 100, 1) : 0; @endphp
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

        {{-- Per Status Kawin --}}
        <x-ui.card>
            <h3 class="text-lg font-semibold mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                Penduduk Per Status Kawin
            </h3>
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Status Kawin</th>
                            <th class="text-center">Jumlah</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($perStatusKawin as $item)
                        <tr>
                            <td class="font-medium">{{ $item->status_kawin ?? 'Tidak diketahui' }}</td>
                            <td class="text-center">
                                <x-ui.badge type="accent" size="sm">{{ number_format($item->total) }}</x-ui.badge>
                            </td>
                            <td>
                                @php $pct = $totalPenduduk > 0 ? round(($item->total / $totalPenduduk) * 100, 1) : 0; @endphp
                                <div class="flex items-center gap-2">
                                    <progress class="progress progress-accent w-20" value="{{ $pct }}" max="100"></progress>
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
</x-layouts.app>
