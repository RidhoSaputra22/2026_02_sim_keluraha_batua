<x-layouts.app :title="'Portal Warga'">

    <x-slot:header>
        <x-layouts.page-header title="Portal Warga" description="Layanan mandiri untuk permohonan dan tracking surat">
            <x-slot:actions>
                <x-ui.button type="primary" size="sm" href="{{ route('warga.permohonan.create') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Ajukan Permohonan
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    {{-- Flash Messages --}}
    @if(session('success'))
        <x-ui.alert type="success" class="mb-4">{{ session('success') }}</x-ui.alert>
    @endif
    @if(session('error'))
        <x-ui.alert type="error" class="mb-4">{{ session('error') }}</x-ui.alert>
    @endif

    {{-- Statistics --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-ui.card class="bg-warning/5">
            <x-ui.stat title="Permohonan Aktif" value="{{ $permohonanAktif }}" description="Sedang diproses">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>

        <x-ui.card class="bg-success/5">
            <x-ui.stat title="Selesai" value="{{ $permohonanSelesai }}" description="Siap diambil / diunduh">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>

        <x-ui.card class="bg-error/5">
            <x-ui.stat title="Ditolak" value="{{ $permohonanDitolak }}" description="Perlu perbaikan">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>

        <x-ui.card class="bg-primary/5">
            <x-ui.stat title="Total Permohonan" value="{{ $totalPermohonan }}" description="Semua permohonan">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>
    </div>

    {{-- Quick Actions & Recent Permohonan --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        {{-- Quick Actions --}}
        <x-ui.card title="Menu Layanan" class="lg:col-span-1">
            <p class="text-sm text-base-content/70 mb-4">Akses cepat layanan yang tersedia</p>
            <div class="space-y-2">
                <x-ui.button type="primary" class="w-full justify-start gap-2" href="{{ route('warga.permohonan.create') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Ajukan Permohonan Baru
                </x-ui.button>
                <x-ui.button type="secondary" class="w-full justify-start gap-2" :outline="true" href="{{ route('warga.permohonan.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Permohonan Saya
                </x-ui.button>
                <x-ui.button type="accent" class="w-full justify-start gap-2" :outline="true" href="{{ route('warga.riwayat.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Riwayat & Tracking
                </x-ui.button>
            </div>
        </x-ui.card>

        {{-- Recent Permohonan Table --}}
        <div class="lg:col-span-2">
            <x-ui.card title="Permohonan Terbaru">
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Jenis Surat</th>
                                <th>Perihal</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suratTerbaru as $surat)
                                <tr class="hover">
                                    <td class="font-medium">{{ $surat->jenis->nama ?? '-' }}</td>
                                    <td class="text-sm max-w-48 truncate">{{ $surat->perihal }}</td>
                                    <td class="text-sm">{{ $surat->tgl_input?->format('d M Y') }}</td>
                                    <td>
                                        @php
                                            $badgeType = match($surat->status_esign) {
                                                'draft' => 'warning',
                                                'proses' => 'info',
                                                'signed' => 'success',
                                                'reject' => 'error',
                                                default => 'ghost',
                                            };
                                            $statusLabel = match($surat->status_esign) {
                                                'draft' => 'Menunggu Verifikasi',
                                                'proses' => 'Menunggu TTD',
                                                'signed' => 'Selesai',
                                                'reject' => 'Ditolak',
                                                default => '-',
                                            };
                                        @endphp
                                        <x-ui.badge :type="$badgeType" size="sm">{{ $statusLabel }}</x-ui.badge>
                                    </td>
                                    <td>
                                        <x-ui.button type="ghost" size="xs" href="{{ route('warga.permohonan.show', $surat) }}">
                                            Detail
                                        </x-ui.button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-base-content/60 py-8">
                                        Belum ada permohonan. <a href="{{ route('warga.permohonan.create') }}" class="link link-primary">Ajukan sekarang</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($suratTerbaru->count() > 0)
                    <div class="mt-3 text-right">
                        <x-ui.button type="link" size="sm" href="{{ route('warga.permohonan.index') }}">Lihat Semua &rarr;</x-ui.button>
                    </div>
                @endif
            </x-ui.card>
        </div>
    </div>

    {{-- Info Section --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-ui.card title="Persyaratan Umum">
            <div class="space-y-4">
                <x-ui.alert type="info" class="text-sm">
                    <p><strong>Jam Layanan:</strong> Senin - Jumat, 08:00 - 15:00 WITA</p>
                </x-ui.alert>
                <div class="space-y-3">
                    <h4 class="font-semibold text-sm">Dokumen yang Diperlukan</h4>
                    <ul class="text-sm space-y-1 list-disc list-inside text-base-content/80">
                        <li>Fotokopi KTP / KK</li>
                        <li>Surat Pengantar dari RT/RW</li>
                        <li>Berkas pendukung sesuai jenis surat</li>
                    </ul>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card title="Kontak Kelurahan">
            <div class="space-y-4">
                <div class="text-sm text-base-content/80 space-y-2">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-base-content/50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <span>Jl. Batua Raya No. 1, Kel. Batua, Kec. Manggala</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-base-content/50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                        <span>(0411) 123-4567</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-base-content/50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        <span>kelurahan.batua@makassar.go.id</span>
                    </div>
                </div>
                <x-ui.alert type="warning" class="text-sm">
                    <p>Pastikan data yang diajukan sudah benar. Permohonan dengan data tidak lengkap akan ditolak.</p>
                </x-ui.alert>
            </div>
        </x-ui.card>
    </div>

</x-layouts.app>
