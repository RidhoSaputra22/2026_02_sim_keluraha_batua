<x-layouts.app :title="'Portal Warga'">

    <x-slot:header>
        <x-layouts.page-header title="Portal Warga" description="Layanan mandiri untuk permohonan dan tracking surat" />
    </x-slot:header>

    {{-- Statistics --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-ui.card class="bg-warning/5">
            <x-ui.stat title="Permohonan Aktif" value="{{ $permohonanAktif ?? 2 }}" description="Sedang diproses">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-warning" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>

        <x-ui.card class="bg-success/5">
            <x-ui.stat title="Selesai" value="{{ $permohonanSelesai ?? 5 }}" description="Siap diambil / diunduh">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-success" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>

        <x-ui.card class="bg-error/5">
            <x-ui.stat title="Ditolak" value="{{ $permohonanDitolak ?? 0 }}" description="Perlu perbaikan">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-error" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>

        <x-ui.card class="bg-primary/5">
            <x-ui.stat title="Total Permohonan" value="{{ $totalPermohonan ?? 7 }}" description="Semua permohonan">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>
    </div>

    {{-- Ajukan Permohonan & Tracking --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        {{-- Ajukan Baru --}}
        <x-ui.card title="Ajukan Permohonan Baru" class="lg:col-span-1">
            <p class="text-sm text-base-content/70 mb-4">Pilih jenis surat yang ingin Anda ajukan</p>
            <div class="space-y-2">
                <x-ui.button type="primary" class="w-full justify-start gap-2" href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Surat Keterangan (SKTM/Domisili)
                </x-ui.button>
                <x-ui.button type="secondary" class="w-full justify-start gap-2" :outline="true" href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Surat Keterangan Usaha
                </x-ui.button>
                <x-ui.button type="accent" class="w-full justify-start gap-2" :outline="true" href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    Pengantar Nikah
                </x-ui.button>
                <x-ui.button type="ghost" class="w-full justify-start gap-2" href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Jenis Surat Lainnya
                </x-ui.button>
            </div>
        </x-ui.card>

        {{-- Tracking Permohonan --}}
        <div class="lg:col-span-2">
            <x-ui.card title="Permohonan Saya">
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Jenis Surat</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="hover">
                                <td class="font-medium">SKTM</td>
                                <td class="text-sm">10 Feb 2026</td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <span class="loading loading-spinner loading-xs text-warning"></span>
                                        <x-ui.badge type="warning" size="sm">Verifikasi</x-ui.badge>
                                    </div>
                                </td>
                                <td class="text-xs text-base-content/70">Menunggu verifikasi Kasi</td>
                                <td>
                                    <x-ui.button type="ghost" size="xs" href="#">Lacak</x-ui.button>
                                </td>
                            </tr>
                            <tr class="hover">
                                <td class="font-medium">Domisili</td>
                                <td class="text-sm">08 Feb 2026</td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <span class="loading loading-spinner loading-xs text-info"></span>
                                        <x-ui.badge type="info" size="sm">TTD</x-ui.badge>
                                    </div>
                                </td>
                                <td class="text-xs text-base-content/70">Menunggu tanda tangan Lurah</td>
                                <td>
                                    <x-ui.button type="ghost" size="xs" href="#">Lacak</x-ui.button>
                                </td>
                            </tr>
                            <tr class="hover">
                                <td class="font-medium">Ket. Usaha</td>
                                <td class="text-sm">02 Feb 2026</td>
                                <td>
                                    <x-ui.badge type="success" size="sm">Selesai</x-ui.badge>
                                </td>
                                <td class="text-xs text-base-content/70">Siap diambil / unduh</td>
                                <td>
                                    <x-ui.button type="primary" size="xs" href="#">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Unduh
                                    </x-ui.button>
                                </td>
                            </tr>
                            <tr class="hover">
                                <td class="font-medium">SKTM</td>
                                <td class="text-sm">25 Jan 2026</td>
                                <td>
                                    <x-ui.badge type="success" size="sm">Selesai</x-ui.badge>
                                </td>
                                <td class="text-xs text-base-content/70">Sudah diambil</td>
                                <td>
                                    <x-ui.button type="primary" size="xs" href="#">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Unduh
                                    </x-ui.button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        </div>
    </div>

    {{-- Timeline & Info --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Timeline aktif --}}
        <x-ui.card title="Tracking — SKTM (10 Feb 2026)">
            <ul class="steps steps-vertical w-full">
                <li class="step step-success" data-content="✓">
                    <div class="text-left ml-2">
                        <p class="text-sm font-medium">Permohonan Diajukan</p>
                        <p class="text-xs text-base-content/60">10 Feb 2026, 08:00</p>
                    </div>
                </li>
                <li class="step step-success" data-content="✓">
                    <div class="text-left ml-2">
                        <p class="text-sm font-medium">Diterima Operator</p>
                        <p class="text-xs text-base-content/60">10 Feb 2026, 08:15 — Operator A</p>
                    </div>
                </li>
                <li class="step step-warning" data-content="●">
                    <div class="text-left ml-2">
                        <p class="text-sm font-medium">Verifikasi Kasi</p>
                        <p class="text-xs text-base-content/60">Menunggu...</p>
                    </div>
                </li>
                <li class="step" data-content="○">
                    <div class="text-left ml-2">
                        <p class="text-sm font-medium text-base-content/40">Tanda Tangan Lurah</p>
                    </div>
                </li>
                <li class="step" data-content="○">
                    <div class="text-left ml-2">
                        <p class="text-sm font-medium text-base-content/40">Cetak & Selesai</p>
                    </div>
                </li>
            </ul>
        </x-ui.card>

        {{-- Info & Bantuan --}}
        <x-ui.card title="Informasi & Bantuan">
            <div class="space-y-4">
                <x-ui.alert type="info" class="text-sm">
                    <p><strong>Jam Layanan:</strong> Senin - Jumat, 08:00 - 15:00 WITA</p>
                </x-ui.alert>

                <div class="space-y-3">
                    <h4 class="font-semibold text-sm">Persyaratan Umum</h4>
                    <ul class="text-sm space-y-1 list-disc list-inside text-base-content/80">
                        <li>Fotokopi KTP / KK</li>
                        <li>Surat Pengantar dari RT/RW</li>
                        <li>Berkas pendukung sesuai jenis surat</li>
                    </ul>
                </div>

                <div class="space-y-3">
                    <h4 class="font-semibold text-sm">Kontak Kelurahan</h4>
                    <div class="text-sm text-base-content/80 space-y-1">
                        <p>📍 Jl. Batua Raya No. 1, Kel. Batua, Kec. Manggala</p>
                        <p>📞 (0411) 123-4567</p>
                        <p>✉️ kelurahan.batua@makassar.go.id</p>
                    </div>
                </div>
            </div>
        </x-ui.card>
    </div>

</x-layouts.app>