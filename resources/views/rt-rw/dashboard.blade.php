<x-layouts.app :title="'Dashboard RT/RW'">

    <x-slot:header>
        <x-layouts.page-header
            title="Dashboard RT/RW"
            description="Monitoring dan pendataan warga di wilayah Anda"
        />
    </x-slot:header>

    {{-- Statistics --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-ui.card class="bg-primary/5">
            <x-ui.stat title="Total Warga" value="{{ $totalWarga ?? 320 }}" description="Di wilayah RT/RW Anda">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>

        <x-ui.card class="bg-secondary/5">
            <x-ui.stat title="Jumlah KK" value="{{ $totalKK ?? 85 }}" description="Kepala keluarga">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>

        <x-ui.card class="bg-accent/5">
            <x-ui.stat title="Pengantar Bulan Ini" value="{{ $totalPengantarBulanIni ?? 12 }}" description="Surat pengantar">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>

        <x-ui.card class="bg-info/5">
            <x-ui.stat title="Laporan Masuk" value="{{ $laporanMasuk ?? 3 }}" description="Laporan & pengaduan">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Daftar Warga Terbaru --}}
        <div class="lg:col-span-2">
            <x-ui.card title="Warga Terbaru di Wilayah">
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>Alamat</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="hover">
                                <td class="font-mono text-xs">7371xxxxxxxxxx01</td>
                                <td>Ahmad Yani</td>
                                <td class="text-xs">Jl. Batua Raya No. 12</td>
                                <td><x-ui.badge type="success" size="xs">Tetap</x-ui.badge></td>
                                <td>
                                    <x-ui.button type="ghost" size="xs" href="#">Detail</x-ui.button>
                                </td>
                            </tr>
                            <tr class="hover">
                                <td class="font-mono text-xs">7371xxxxxxxxxx02</td>
                                <td>Siti Rahma</td>
                                <td class="text-xs">Jl. Batua Raya No. 15</td>
                                <td><x-ui.badge type="success" size="xs">Tetap</x-ui.badge></td>
                                <td>
                                    <x-ui.button type="ghost" size="xs" href="#">Detail</x-ui.button>
                                </td>
                            </tr>
                            <tr class="hover">
                                <td class="font-mono text-xs">7371xxxxxxxxxx03</td>
                                <td>Budi Santoso</td>
                                <td class="text-xs">Jl. Batua Baru No. 8</td>
                                <td><x-ui.badge type="warning" size="xs">Pendatang</x-ui.badge></td>
                                <td>
                                    <x-ui.button type="ghost" size="xs" href="#">Detail</x-ui.button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <x-slot:actions>
                    <x-ui.button type="ghost" size="sm" href="#">Lihat Semua Warga →</x-ui.button>
                </x-slot:actions>
            </x-ui.card>
        </div>

        {{-- Info & Aksi Cepat --}}
        <div class="space-y-6">
            <x-ui.card title="Aksi Cepat">
                <div class="space-y-2">
                    <x-ui.button type="primary" class="w-full justify-start gap-2" href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                        Tambah Data Warga
                    </x-ui.button>
                    <x-ui.button type="secondary" class="w-full justify-start gap-2" :outline="true" href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Buat Surat Pengantar
                    </x-ui.button>
                    <x-ui.button type="accent" class="w-full justify-start gap-2" :outline="true" href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" /></svg>
                        Laporkan Kejadian
                    </x-ui.button>
                </div>
            </x-ui.card>

            <x-ui.card title="Komposisi Warga">
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm">Laki-laki</span>
                        <span class="font-semibold">165</span>
                    </div>
                    <progress class="progress progress-info w-full" value="165" max="320"></progress>
                    <div class="flex justify-between items-center">
                        <span class="text-sm">Perempuan</span>
                        <span class="font-semibold">155</span>
                    </div>
                    <progress class="progress progress-secondary w-full" value="155" max="320"></progress>
                    <div class="divider my-1"></div>
                    <div class="grid grid-cols-2 gap-2 text-center text-xs">
                        <div class="bg-base-200/50 rounded p-2">
                            <div class="font-bold">82</div>
                            <div>Anak (0-17)</div>
                        </div>
                        <div class="bg-base-200/50 rounded p-2">
                            <div class="font-bold">190</div>
                            <div>Dewasa (18-59)</div>
                        </div>
                        <div class="bg-base-200/50 rounded p-2">
                            <div class="font-bold">48</div>
                            <div>Lansia (60+)</div>
                        </div>
                        <div class="bg-base-200/50 rounded p-2">
                            <div class="font-bold">320</div>
                            <div>Total</div>
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>

    {{-- Mutasi & Pengantar Terbaru --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-ui.card title="Mutasi Warga Terbaru">
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <x-ui.badge type="success" size="xs">Datang</x-ui.badge>
                    <div>
                        <p class="text-sm font-medium">Budi Santoso</p>
                        <p class="text-xs text-base-content/60">Pindah dari Kec. Tamalate · 08 Feb 2026</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <x-ui.badge type="error" size="xs">Pindah</x-ui.badge>
                    <div>
                        <p class="text-sm font-medium">Dewi Anggraeni</p>
                        <p class="text-xs text-base-content/60">Pindah ke Kec. Panakkukang · 05 Feb 2026</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <x-ui.badge type="info" size="xs">Lahir</x-ui.badge>
                    <div>
                        <p class="text-sm font-medium">Bayi — Kel. Ahmad Yani</p>
                        <p class="text-xs text-base-content/60">Lahir 03 Feb 2026</p>
                    </div>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card title="Surat Pengantar Terbaru">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium">Pengantar SKTM — Ahmad Yani</p>
                        <p class="text-xs text-base-content/60">10 Feb 2026</p>
                    </div>
                    <x-ui.badge type="success" size="xs">Selesai</x-ui.badge>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium">Pengantar Domisili — Siti Rahma</p>
                        <p class="text-xs text-base-content/60">09 Feb 2026</p>
                    </div>
                    <x-ui.badge type="warning" size="xs">Proses</x-ui.badge>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium">Pengantar Nikah — Andi R.</p>
                        <p class="text-xs text-base-content/60">07 Feb 2026</p>
                    </div>
                    <x-ui.badge type="success" size="xs">Selesai</x-ui.badge>
                </div>
            </div>
        </x-ui.card>
    </div>

</x-layouts.app>
