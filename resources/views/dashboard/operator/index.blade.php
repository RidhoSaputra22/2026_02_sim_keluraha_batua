<x-layouts.app :title="'Dashboard Operator'">

    <x-slot:header>
        <x-layouts.page-header
            title="Dashboard Operator"
            description="Kelola data penduduk dan layanan persuratan"
        />
    </x-slot:header>

    {{-- Statistics --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-ui.card class="bg-primary/5">
            <x-ui.stat title="Surat Hari Ini" value="{{ $suratHariIni ?? 12 }}" description="Total permohonan">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>

        <x-ui.card class="bg-warning/5">
            <x-ui.stat title="Draft Surat" value="{{ $suratDraft ?? 3 }}" description="Perlu dilengkapi">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>

        <x-ui.card class="bg-info/5">
            <x-ui.stat title="Menunggu Cetak" value="{{ $suratMenungguCetak ?? 4 }}" description="Siap cetak">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>

        <x-ui.card class="bg-success/5">
            <x-ui.stat title="Selesai Hari Ini" value="{{ $suratSelesaiHariIni ?? 8 }}" description="Sudah diproses">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Aksi Cepat Operator --}}
        <div class="lg:col-span-1">
            <x-ui.card title="Aksi Cepat">
                <div class="space-y-2">
                    <x-ui.button type="primary" size="sm" class="w-full justify-start gap-2" href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Buat Permohonan Surat
                    </x-ui.button>
                    <x-ui.button type="secondary" size="sm" class="w-full justify-start gap-2" href="#" :outline="true">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        Input Penduduk Baru
                    </x-ui.button>
                    <x-ui.button type="accent" size="sm" class="w-full justify-start gap-2" href="#" :outline="true">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Input Kartu Keluarga
                    </x-ui.button>
                    <x-ui.button type="info" size="sm" class="w-full justify-start gap-2" href="#" :outline="true">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak Surat
                    </x-ui.button>
                    <x-ui.button type="neutral" size="sm" class="w-full justify-start gap-2" href="#" :outline="true">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Import Data Penduduk
                    </x-ui.button>
                </div>
            </x-ui.card>

            {{-- Data Summary --}}
            <x-ui.card title="Ringkasan Data" class="mt-6">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm">Total Penduduk</span>
                        <span class="font-bold">{{ number_format($totalPenduduk ?? 12450) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm">Total KK</span>
                        <span class="font-bold">{{ number_format($totalKK ?? 3120) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm">Penduduk Baru (Bulan Ini)</span>
                        <x-ui.badge type="info" size="sm">+{{ $pendudukBaruBulanIni ?? 15 }}</x-ui.badge>
                    </div>
                </div>
            </x-ui.card>
        </div>

        {{-- Antrian Surat yang Perlu Diproses --}}
        <div class="lg:col-span-2">
            <x-ui.card title="Surat Perlu Diproses">
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Jenis Surat</th>
                                <th>Pemohon</th>
                                <th>NIK</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="hover">
                                <td>1</td>
                                <td>SKTM</td>
                                <td>Ahmad Yani</td>
                                <td class="font-mono text-xs">7371xxxx0001</td>
                                <td>10 Feb 2026</td>
                                <td><x-ui.badge type="warning" size="sm">Draft</x-ui.badge></td>
                                <td>
                                    <x-ui.button type="primary" size="xs" href="#">Proses</x-ui.button>
                                </td>
                            </tr>
                            <tr class="hover">
                                <td>2</td>
                                <td>Domisili</td>
                                <td>Siti Rahma</td>
                                <td class="font-mono text-xs">7371xxxx0023</td>
                                <td>10 Feb 2026</td>
                                <td><x-ui.badge type="info" size="sm">Siap Cetak</x-ui.badge></td>
                                <td>
                                    <x-ui.button type="info" size="xs" href="#">Cetak</x-ui.button>
                                </td>
                            </tr>
                            <tr class="hover">
                                <td>3</td>
                                <td>Ket. Usaha</td>
                                <td>Budi Santoso</td>
                                <td class="font-mono text-xs">7371xxxx0045</td>
                                <td>09 Feb 2026</td>
                                <td><x-ui.badge type="warning" size="sm">Draft</x-ui.badge></td>
                                <td>
                                    <x-ui.button type="primary" size="xs" href="#">Proses</x-ui.button>
                                </td>
                            </tr>
                            <tr class="hover">
                                <td>4</td>
                                <td>SKTM</td>
                                <td>Rina Melati</td>
                                <td class="font-mono text-xs">7371xxxx0067</td>
                                <td>09 Feb 2026</td>
                                <td><x-ui.badge type="error" size="sm">Perbaikan</x-ui.badge></td>
                                <td>
                                    <x-ui.button type="warning" size="xs" href="#">Perbaiki</x-ui.button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <x-slot:actions>
                    <x-ui.button type="ghost" size="sm" href="#">Lihat Semua Antrian →</x-ui.button>
                </x-slot:actions>
            </x-ui.card>
        </div>
    </div>

</x-layouts.app>
