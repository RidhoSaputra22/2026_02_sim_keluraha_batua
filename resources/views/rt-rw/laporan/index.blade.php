<x-layouts.app :title="'Laporan Wilayah'">

    <x-slot:header>
        <x-layouts.page-header title="Laporan Wilayah"
            description="Statistik dan rekap data kependudukan di wilayah Anda" />
    </x-slot:header>

    {{-- Ringkasan Utama --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <x-ui.card class="bg-primary/5">
            <x-ui.stat title="Total Warga" value="{{ $totalWarga }}" description="Penduduk terdaftar" />
        </x-ui.card>
        <x-ui.card class="bg-secondary/5">
            <x-ui.stat title="Total KK" value="{{ $totalKK }}" description="Kartu Keluarga" />
        </x-ui.card>
        <x-ui.card class="bg-info/5">
            <x-ui.stat title="Laki-laki" value="{{ $lakiLaki }}"
                description="{{ $totalWarga > 0 ? round(($lakiLaki / $totalWarga) * 100, 1) : 0 }}%" />
        </x-ui.card>
        <x-ui.card class="bg-secondary/5">
            <x-ui.stat title="Perempuan" value="{{ $perempuan }}"
                description="{{ $totalWarga > 0 ? round(($perempuan / $totalWarga) * 100, 1) : 0 }}%" />
        </x-ui.card>
    </div>

    {{-- Mutasi Tahun Ini --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <x-ui.card>
            <div class="text-center">
                <div class="text-2xl font-bold text-success">{{ $mutasiDatang }}</div>
                <div class="text-xs text-base-content/60 mt-1">Datang</div>
            </div>
        </x-ui.card>
        <x-ui.card>
            <div class="text-center">
                <div class="text-2xl font-bold text-error">{{ $mutasiPindah }}</div>
                <div class="text-xs text-base-content/60 mt-1">Pindah</div>
            </div>
        </x-ui.card>
        <x-ui.card>
            <div class="text-center">
                <div class="text-2xl font-bold text-info">{{ $kelahiranTahunIni }}</div>
                <div class="text-xs text-base-content/60 mt-1">Lahir</div>
            </div>
        </x-ui.card>
        <x-ui.card>
            <div class="text-center">
                <div class="text-2xl font-bold text-warning">{{ $kematianTahunIni }}</div>
                <div class="text-xs text-base-content/60 mt-1">Meninggal</div>
            </div>
        </x-ui.card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Per RT Stats --}}
        <x-ui.card title="Statistik Per RT">
            <div class="overflow-x-auto">
                <table class="table table-zebra table-sm">
                    <thead>
                        <tr>
                            <th>RT</th>
                            <th>RW</th>
                            <th>Jumlah Penduduk</th>
                            <th>Jumlah KK</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($perRtStats as $rt)
                            <tr>
                                <td>RT {{ str_pad($rt->nomor, 3, '0', STR_PAD_LEFT) }}</td>
                                <td>RW {{ str_pad($rt->rw?->nomor ?? '-', 3, '0', STR_PAD_LEFT) }}</td>
                                <td>
                                    <x-ui.badge type="primary" size="xs">{{ $rt->penduduks_count }}</x-ui.badge>
                                </td>
                                <td>
                                    <x-ui.badge type="secondary" size="xs">{{ $rt->jumlah_kk }}</x-ui.badge>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-base-content/50">Tidak ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="font-bold">
                            <td colspan="2">Total</td>
                            <td>{{ $perRtStats->sum('penduduks_count') }}</td>
                            <td>{{ $perRtStats->sum('jumlah_kk') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-ui.card>

        {{-- Agama --}}
        <x-ui.card title="Berdasarkan Agama">
            <div class="space-y-3">
                @forelse($agamaStats as $stat)
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm">{{ $stat->agama ?? 'Tidak Diketahui' }}</span>
                            <span class="text-sm font-semibold">{{ $stat->total }}</span>
                        </div>
                        <progress class="progress progress-primary w-full" value="{{ $stat->total }}"
                            max="{{ $totalWarga ?: 1 }}"></progress>
                    </div>
                @empty
                    <p class="text-sm text-base-content/50">Tidak ada data.</p>
                @endforelse
            </div>
        </x-ui.card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Pendidikan --}}
        <x-ui.card title="Berdasarkan Pendidikan">
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Pendidikan</th>
                            <th>Jumlah</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendidikanStats as $stat)
                            <tr>
                                <td>{{ $stat->pendidikan ?? 'Tidak Diketahui' }}</td>
                                <td class="font-semibold">{{ $stat->total }}</td>
                                <td>{{ $totalWarga > 0 ? round(($stat->total / $totalWarga) * 100, 1) : 0 }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-base-content/50">Tidak ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        {{-- Status Kawin --}}
        <x-ui.card title="Berdasarkan Status Perkawinan">
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Jumlah</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($statusKawinStats as $stat)
                            <tr>
                                <td>{{ $stat->status_kawin ?? 'Tidak Diketahui' }}</td>
                                <td class="font-semibold">{{ $stat->total }}</td>
                                <td>{{ $totalWarga > 0 ? round(($stat->total / $totalWarga) * 100, 1) : 0 }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-base-content/50">Tidak ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>

</x-layouts.app>
