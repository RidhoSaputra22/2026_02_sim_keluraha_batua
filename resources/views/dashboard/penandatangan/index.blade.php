<x-layouts.app :title="'Dashboard Penandatangan'">

    <x-slot:header>
        <x-layouts.page-header
            title="Dashboard Penandatangan"
            description="Tanda tangan dan finalisasi surat resmi"
        />
    </x-slot:header>

    {{-- Statistics --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-ui.card class="bg-warning/5">
            <x-ui.stat title="Menunggu TTD" value="{{ $suratMenungguTtd ?? 8 }}" description="Siap ditandatangani">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>

        <x-ui.card class="bg-success/5">
            <x-ui.stat title="Ditandatangani Hari Ini" value="{{ $suratDitandatanganiHariIni ?? 6 }}" description="Finalized">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>

        <x-ui.card class="bg-primary/5">
            <x-ui.stat title="Total TTD Bulan Ini" value="{{ $totalTtdBulanIni ?? 72 }}" description="Bulan berjalan">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>

        <x-ui.card class="bg-secondary/5">
            <x-ui.stat title="Surat Selesai" value="{{ $suratSelesaiBulanIni ?? 70 }}" description="Sudah dicetak & diarsip">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
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
                            <tr class="hover">
                                <td>1</td>
                                <td>SKTM</td>
                                <td>Ahmad Yani</td>
                                <td class="font-mono text-xs">001/SKT/KB/II/2026</td>
                                <td class="text-xs">Kasi Pem</td>
                                <td class="flex gap-1">
                                    <x-ui.button type="ghost" size="xs" href="#">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </x-ui.button>
                                    <x-ui.button type="primary" size="xs">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        TTD
                                    </x-ui.button>
                                </td>
                            </tr>
                            <tr class="hover">
                                <td>2</td>
                                <td>Domisili</td>
                                <td>Siti Rahma</td>
                                <td class="font-mono text-xs">002/SKD/KB/II/2026</td>
                                <td class="text-xs">Seklur</td>
                                <td class="flex gap-1">
                                    <x-ui.button type="ghost" size="xs" href="#">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </x-ui.button>
                                    <x-ui.button type="primary" size="xs">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        TTD
                                    </x-ui.button>
                                </td>
                            </tr>
                            <tr class="hover">
                                <td>3</td>
                                <td>Pengantar Nikah</td>
                                <td>Budi Santoso</td>
                                <td class="font-mono text-xs">003/SPN/KB/II/2026</td>
                                <td class="text-xs">Kasi Pem</td>
                                <td class="flex gap-1">
                                    <x-ui.button type="ghost" size="xs" href="#">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </x-ui.button>
                                    <x-ui.button type="primary" size="xs">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        TTD
                                    </x-ui.button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <x-slot:actions>
                    <x-ui.button type="ghost" size="sm" href="#">Lihat Semua →</x-ui.button>
                </x-slot:actions>
            </x-ui.card>
        </div>

        {{-- Sidebar Info --}}
        <div class="space-y-6">
            <x-ui.card title="Status Hari Ini">
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm">Menunggu TTD</span>
                        <x-ui.badge type="warning">8</x-ui.badge>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm">Ditandatangani</span>
                        <x-ui.badge type="success">6</x-ui.badge>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm">Dikembalikan</span>
                        <x-ui.badge type="error">0</x-ui.badge>
                    </div>
                    <div class="divider my-1"></div>
                    <div class="flex justify-between items-center font-semibold">
                        <span class="text-sm">Total Proses</span>
                        <x-ui.badge type="primary">14</x-ui.badge>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card title="Surat Terbaru Ditandatangani">
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="badge badge-success badge-xs mt-1.5"></div>
                        <div>
                            <p class="text-sm font-medium">SKTM — Dedi Kurniawan</p>
                            <p class="text-xs text-base-content/60">001/SKT/KB/II/2026 · 10:30</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="badge badge-success badge-xs mt-1.5"></div>
                        <div>
                            <p class="text-sm font-medium">Domisili — Rani Safitri</p>
                            <p class="text-xs text-base-content/60">002/SKD/KB/II/2026 · 10:15</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="badge badge-success badge-xs mt-1.5"></div>
                        <div>
                            <p class="text-sm font-medium">Ket. Usaha — Haris M.</p>
                            <p class="text-xs text-base-content/60">003/SKU/KB/II/2026 · 09:45</p>
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>

    {{-- Rekap Surat per Jenis --}}
    <x-ui.card title="Rekap Penandatanganan Bulan Ini">
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
            <div class="text-center bg-base-200/50 rounded-lg p-3">
                <div class="text-2xl font-bold text-primary">18</div>
                <div class="text-xs text-base-content/70">SKTM</div>
            </div>
            <div class="text-center bg-base-200/50 rounded-lg p-3">
                <div class="text-2xl font-bold text-primary">15</div>
                <div class="text-xs text-base-content/70">Domisili</div>
            </div>
            <div class="text-center bg-base-200/50 rounded-lg p-3">
                <div class="text-2xl font-bold text-primary">12</div>
                <div class="text-xs text-base-content/70">Ket. Usaha</div>
            </div>
            <div class="text-center bg-base-200/50 rounded-lg p-3">
                <div class="text-2xl font-bold text-primary">10</div>
                <div class="text-xs text-base-content/70">Pengantar Nikah</div>
            </div>
            <div class="text-center bg-base-200/50 rounded-lg p-3">
                <div class="text-2xl font-bold text-primary">9</div>
                <div class="text-xs text-base-content/70">Ket. Lahir</div>
            </div>
            <div class="text-center bg-base-200/50 rounded-lg p-3">
                <div class="text-2xl font-bold text-primary">8</div>
                <div class="text-xs text-base-content/70">Lainnya</div>
            </div>
        </div>
    </x-ui.card>

</x-layouts.app>
