<x-layouts.app :title="'Dashboard'">

    {{-- Page Header --}}
    <x-slot:header>
        <x-layouts.page-header 
            title="Dashboard" 
            description="Ringkasan data dan aktivitas Kelurahan Batua" 
        />
    </x-slot:header>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-ui.card class="bg-primary/5">
            <x-ui.stat 
                title="Total Penduduk" 
                value="{{ number_format($totalPenduduk ?? 12450) }}" 
                description="Data terakhir"
                trend="up"
                trendValue="+2.5%"
            >
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>

        <x-ui.card class="bg-secondary/5">
            <x-ui.stat 
                title="Kartu Keluarga" 
                value="{{ number_format($totalKK ?? 3120) }}"
                description="Terdaftar"
            >
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>

        <x-ui.card class="bg-accent/5">
            <x-ui.stat 
                title="Surat Bulan Ini" 
                value="{{ $totalSuratBulanIni ?? 87 }}"
                description="Diproses"
                trend="up"
                trendValue="+12"
            >
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>

        <x-ui.card class="bg-warning/5">
            <x-ui.stat 
                title="Menunggu Verifikasi" 
                value="{{ $suratMenunggu ?? 5 }}"
                description="Perlu tindakan"
            >
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>
    </div>

    {{-- Two-column layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        {{-- Layanan Terbaru --}}
        <div class="lg:col-span-2">
            <x-ui.card title="Layanan Surat Terbaru">
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>No. Surat</th>
                                <th>Jenis</th>
                                <th>Pemohon</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="hover">
                                <td class="font-mono text-sm">045/KB/II/2026</td>
                                <td>SKTM</td>
                                <td>Ahmad Yani</td>
                                <td>10 Feb 2026</td>
                                <td><x-ui.badge type="success" size="sm">Selesai</x-ui.badge></td>
                            </tr>
                            <tr class="hover">
                                <td class="font-mono text-sm">044/KB/II/2026</td>
                                <td>Domisili</td>
                                <td>Siti Rahma</td>
                                <td>09 Feb 2026</td>
                                <td><x-ui.badge type="warning" size="sm">Verifikasi</x-ui.badge></td>
                            </tr>
                            <tr class="hover">
                                <td class="font-mono text-sm">043/KB/II/2026</td>
                                <td>Ket. Usaha</td>
                                <td>Budi Santoso</td>
                                <td>09 Feb 2026</td>
                                <td><x-ui.badge type="info" size="sm">TTD</x-ui.badge></td>
                            </tr>
                            <tr class="hover">
                                <td class="font-mono text-sm">042/KB/II/2026</td>
                                <td>Pengantar Nikah</td>
                                <td>Rina Melati</td>
                                <td>08 Feb 2026</td>
                                <td><x-ui.badge type="success" size="sm">Selesai</x-ui.badge></td>
                            </tr>
                            <tr class="hover">
                                <td class="font-mono text-sm">041/KB/II/2026</td>
                                <td>SKTM</td>
                                <td>Dedi Kurniawan</td>
                                <td>08 Feb 2026</td>
                                <td><x-ui.badge type="error" size="sm">Ditolak</x-ui.badge></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <x-slot:actions>
                    <x-ui.button type="ghost" size="sm" href="#">Lihat Semua →</x-ui.button>
                </x-slot:actions>
            </x-ui.card>
        </div>

        {{-- Sidebar info --}}
        <div class="space-y-6">
            {{-- Mutasi Penduduk --}}
            <x-ui.card title="Mutasi Penduduk (Bulan Ini)">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-success rounded-full"></div>
                            <span class="text-sm">Kelahiran</span>
                        </div>
                        <span class="font-semibold">{{ $mutasiLahir ?? 8 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-error rounded-full"></div>
                            <span class="text-sm">Kematian</span>
                        </div>
                        <span class="font-semibold">{{ $mutasiMeninggal ?? 3 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-info rounded-full"></div>
                            <span class="text-sm">Datang</span>
                        </div>
                        <span class="font-semibold">{{ $mutasiDatang ?? 12 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-warning rounded-full"></div>
                            <span class="text-sm">Pindah</span>
                        </div>
                        <span class="font-semibold">{{ $mutasiPindah ?? 6 }}</span>
                    </div>
                </div>
            </x-ui.card>

            {{-- Quick Actions --}}
            <x-ui.card title="Aksi Cepat">
                <div class="space-y-2">
                    <x-ui.button type="primary" size="sm" class="w-full justify-start gap-2" href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Buat Surat Baru
                    </x-ui.button>
                    <x-ui.button type="secondary" size="sm" class="w-full justify-start gap-2" href="#" :outline="true">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        Tambah Penduduk
                    </x-ui.button>
                    <x-ui.button type="accent" size="sm" class="w-full justify-start gap-2" href="#" :outline="true">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Cetak Laporan
                    </x-ui.button>
                </div>
            </x-ui.card>
        </div>
    </div>

    {{-- Bottom row: Surat per jenis & Data Usaha --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Surat per Jenis --}}
        <x-ui.card title="Rekap Surat per Jenis (Bulan Ini)">
            <div class="space-y-3">
                @php
                    $jenisSurat = [
                        ['nama' => 'SKTM', 'jumlah' => 24, 'persen' => 28],
                        ['nama' => 'Domisili', 'jumlah' => 20, 'persen' => 23],
                        ['nama' => 'Keterangan Usaha', 'jumlah' => 15, 'persen' => 17],
                        ['nama' => 'Pengantar Nikah', 'jumlah' => 12, 'persen' => 14],
                        ['nama' => 'Ket. Kelahiran', 'jumlah' => 8, 'persen' => 9],
                        ['nama' => 'Lainnya', 'jumlah' => 8, 'persen' => 9],
                    ];
                @endphp
                @foreach($jenisSurat as $jenis)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span>{{ $jenis['nama'] }}</span>
                            <span class="font-medium">{{ $jenis['jumlah'] }}</span>
                        </div>
                        <progress class="progress progress-primary w-full" value="{{ $jenis['persen'] }}" max="100"></progress>
                    </div>
                @endforeach
            </div>
        </x-ui.card>

        {{-- Data Usaha Ringkasan --}}
        <x-ui.card title="Data Usaha / PK5">
            <div class="stats stats-vertical w-full">
                <div class="stat px-0">
                    <div class="stat-title">Total Usaha Terdaftar</div>
                    <div class="stat-value text-primary text-2xl">{{ $totalUsaha ?? 156 }}</div>
                    <div class="stat-desc">di seluruh wilayah kelurahan</div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 mt-4">
                <div class="bg-base-200 rounded-lg p-3 text-center">
                    <div class="text-xl font-bold text-base-content">{{ $usahaAktif ?? 142 }}</div>
                    <div class="text-xs text-base-content/60">Aktif</div>
                </div>
                <div class="bg-base-200 rounded-lg p-3 text-center">
                    <div class="text-xl font-bold text-base-content">{{ $usahaTidakAktif ?? 14 }}</div>
                    <div class="text-xs text-base-content/60">Tidak Aktif</div>
                </div>
            </div>
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="#">Lihat Detail →</x-ui.button>
            </x-slot:actions>
        </x-ui.card>
    </div>

</x-layouts.app>
