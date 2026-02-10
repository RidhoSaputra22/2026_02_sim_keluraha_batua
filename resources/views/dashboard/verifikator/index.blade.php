<x-layouts.app :title="'Dashboard Verifikator'">

    <x-slot:header>
        <x-layouts.page-header
            title="Dashboard Verifikator"
            description="Verifikasi dan validasi permohonan surat"
        />
    </x-slot:header>

    {{-- Statistics --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-ui.card class="bg-warning/5">
            <x-ui.stat title="Menunggu Verifikasi" value="{{ $suratMenungguVerifikasi ?? 7 }}" description="Perlu ditinjau">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>

        <x-ui.card class="bg-success/5">
            <x-ui.stat title="Disetujui Hari Ini" value="{{ $suratDisetujuiHariIni ?? 5 }}" description="Approved">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>

        <x-ui.card class="bg-error/5">
            <x-ui.stat title="Ditolak Hari Ini" value="{{ $suratDitolakHariIni ?? 1 }}" description="Rejected">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>

        <x-ui.card class="bg-info/5">
            <x-ui.stat title="Perlu Perbaikan" value="{{ $suratPerluPerbaikan ?? 2 }}" description="Dikembalikan ke operator">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>
    </div>

    {{-- Antrian Verifikasi --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <x-ui.card title="Antrian Verifikasi">
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Jenis Surat</th>
                                <th>Pemohon</th>
                                <th>Operator</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="hover">
                                <td>1</td>
                                <td>SKTM</td>
                                <td>Ahmad Yani</td>
                                <td class="text-xs">Operator A</td>
                                <td>10 Feb 2026</td>
                                <td class="flex gap-1">
                                    <x-ui.button type="ghost" size="xs" href="#">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </x-ui.button>
                                    <x-ui.button type="success" size="xs">Setujui</x-ui.button>
                                    <x-ui.button type="error" size="xs" :outline="true">Tolak</x-ui.button>
                                </td>
                            </tr>
                            <tr class="hover">
                                <td>2</td>
                                <td>Domisili</td>
                                <td>Siti Rahma</td>
                                <td class="text-xs">Operator B</td>
                                <td>10 Feb 2026</td>
                                <td class="flex gap-1">
                                    <x-ui.button type="ghost" size="xs" href="#">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </x-ui.button>
                                    <x-ui.button type="success" size="xs">Setujui</x-ui.button>
                                    <x-ui.button type="error" size="xs" :outline="true">Tolak</x-ui.button>
                                </td>
                            </tr>
                            <tr class="hover">
                                <td>3</td>
                                <td>Ket. Usaha</td>
                                <td>Budi Santoso</td>
                                <td class="text-xs">Operator A</td>
                                <td>09 Feb 2026</td>
                                <td class="flex gap-1">
                                    <x-ui.button type="ghost" size="xs" href="#">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </x-ui.button>
                                    <x-ui.button type="success" size="xs">Setujui</x-ui.button>
                                    <x-ui.button type="error" size="xs" :outline="true">Tolak</x-ui.button>
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

        {{-- Statistik Verifikasi --}}
        <div class="space-y-6">
            <x-ui.card title="Kinerja Bulan Ini">
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span>Total Diverifikasi</span>
                            <span class="font-bold">{{ $totalVerifikasiBulanIni ?? 65 }}</span>
                        </div>
                        <progress class="progress progress-primary w-full" value="65" max="100"></progress>
                    </div>
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="bg-success/10 rounded-lg p-2">
                            <div class="text-lg font-bold text-success">58</div>
                            <div class="text-xs">Disetujui</div>
                        </div>
                        <div class="bg-error/10 rounded-lg p-2">
                            <div class="text-lg font-bold text-error">4</div>
                            <div class="text-xs">Ditolak</div>
                        </div>
                        <div class="bg-warning/10 rounded-lg p-2">
                            <div class="text-lg font-bold text-warning">3</div>
                            <div class="text-xs">Perbaikan</div>
                        </div>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card title="Riwayat Verifikasi Terbaru">
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <x-ui.badge type="success" size="xs">✓</x-ui.badge>
                        <div>
                            <p class="text-sm font-medium">SKTM — Dedi K.</p>
                            <p class="text-xs text-base-content/60">Disetujui, 09:30</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <x-ui.badge type="success" size="xs">✓</x-ui.badge>
                        <div>
                            <p class="text-sm font-medium">Domisili — Rani S.</p>
                            <p class="text-xs text-base-content/60">Disetujui, 09:15</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <x-ui.badge type="error" size="xs">✕</x-ui.badge>
                        <div>
                            <p class="text-sm font-medium">Ket. Usaha — Haris</p>
                            <p class="text-xs text-base-content/60">Ditolak, 08:45</p>
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>

</x-layouts.app>
