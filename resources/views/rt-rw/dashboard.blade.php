<x-layouts.app :title="'Dashboard RT/RW'">

    <x-slot:header>
        <x-layouts.page-header title="Dashboard RT/RW"
            description="Monitoring dan pendataan warga di wilayah {{ $user->jabatan ?? 'RT/RW' }}" />
    </x-slot:header>

    {{-- Statistics --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        <x-ui.card class="bg-primary/5">
            <x-ui.stat title="Total Warga" value="{{ $totalWarga }}" description="Di wilayah Anda">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>

        <x-ui.card class="bg-secondary/5">
            <x-ui.stat title="Jumlah KK" value="{{ $totalKK }}" description="Kepala keluarga">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-secondary" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>

        <x-ui.card class="bg-info/5">
            <x-ui.stat title="Komposisi" value="{{ $lakiLaki }}L / {{ $perempuan }}P"
                description="Laki-laki / Perempuan">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-info" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
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
                                <th>RT/RW</th>
                                <th>JK</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentWarga as $warga)
                                <tr class="hover">
                                    <td class="font-mono text-xs">{{ $warga->nik }}</td>
                                    <td>{{ $warga->nama }}</td>
                                    <td class="text-xs">
                                        RT {{ str_pad($warga->rt?->nomor ?? '-', 3, '0', STR_PAD_LEFT) }}
                                        / RW {{ str_pad($warga->rt?->rw?->nomor ?? '-', 3, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td>
                                        @if ($warga->jenis_kelamin === 'L')
                                            <x-ui.badge type="info" size="xs">L</x-ui.badge>
                                        @else
                                            <x-ui.badge type="secondary" size="xs">P</x-ui.badge>
                                        @endif
                                    </td>
                                    <td>
                                        <x-ui.button type="ghost" size="xs"
                                            href="{{ route('kependudukan.penduduk.show', $warga) }}">
                                            Detail</x-ui.button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-base-content/50">Belum ada data warga.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <x-slot:actions>
                    <x-ui.button type="ghost" size="sm" href="{{ route('kependudukan.penduduk.index') }}">Lihat
                        Semua Warga &rarr;
                    </x-ui.button>
                </x-slot:actions>
            </x-ui.card>
        </div>

        {{-- Aksi Cepat & Komposisi --}}
        <div class="space-y-6">
            <x-ui.card title="Aksi Cepat">
                <div class="space-y-2">
                    <x-ui.button type="primary" class="w-full justify-start gap-2"
                        href="{{ route('kependudukan.penduduk.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Data Penduduk
                    </x-ui.button>
                    <x-ui.button type="secondary" class="w-full justify-start gap-2" :outline="true"
                        href="{{ route('kependudukan.keluarga.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Data Keluarga
                    </x-ui.button>
                    <x-ui.button type="accent" class="w-full justify-start gap-2" :outline="true"
                        href="{{ route('usaha.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Data Usaha
                    </x-ui.button>
                    <x-ui.button type="info" class="w-full justify-start gap-2" :outline="true"
                        href="{{ route('rtrw.laporan.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Laporan Wilayah
                    </x-ui.button>
                </div>
            </x-ui.card>

            <x-ui.card title="Komposisi Warga">
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm">Laki-laki</span>
                        <span class="font-semibold">{{ $lakiLaki }}</span>
                    </div>
                    <progress class="progress progress-info w-full" value="{{ $lakiLaki }}"
                        max="{{ $totalWarga ?: 1 }}"></progress>
                    <div class="flex justify-between items-center">
                        <span class="text-sm">Perempuan</span>
                        <span class="font-semibold">{{ $perempuan }}</span>
                    </div>
                    <progress class="progress progress-secondary w-full" value="{{ $perempuan }}"
                        max="{{ $totalWarga ?: 1 }}"></progress>
                </div>
            </x-ui.card>
        </div>
    </div>

    {{-- Mutasi Terbaru --}}
    <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
        <x-ui.card title="Mutasi Warga Terbaru">
            <div class="space-y-3">
                @forelse($recentMutasi as $mutasi)
                    <div class="flex items-center gap-3">
                        @if ($mutasi->jenis_mutasi === 'datang')
                            <x-ui.badge type="success" size="xs">Datang</x-ui.badge>
                        @elseif($mutasi->jenis_mutasi === 'pindah')
                            <x-ui.badge type="error" size="xs">Pindah</x-ui.badge>
                        @else
                            <x-ui.badge type="info"
                                size="xs">{{ ucfirst($mutasi->jenis_mutasi) }}</x-ui.badge>
                        @endif
                        <div>
                            <p class="text-sm font-medium">{{ $mutasi->penduduk?->nama ?? '-' }}</p>
                            <p class="text-xs text-base-content/60">{{ $mutasi->tanggal_mutasi?->format('d M Y') }}
                                &middot;
                                {{ $mutasi->keterangan ?? ($mutasi->alasan ?? '-') }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-base-content/50">Belum ada data mutasi.</p>
                @endforelse

                @if ($recentKelahiran->isNotEmpty())
                    <div class="divider my-1 text-xs">Kelahiran</div>
                    @foreach ($recentKelahiran as $lahir)
                        <div class="flex items-center gap-3">
                            <x-ui.badge type="info" size="xs">Lahir</x-ui.badge>
                            <div>
                                <p class="text-sm font-medium">{{ $lahir->nama_bayi }}</p>
                                <p class="text-xs text-base-content/60">{{ $lahir->tanggal_lahir?->format('d M Y') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                @endif

                @if ($recentKematian->isNotEmpty())
                    <div class="divider my-1 text-xs">Kematian</div>
                    @foreach ($recentKematian as $meninggal)
                        <div class="flex items-center gap-3">
                            <x-ui.badge type="error" size="xs">Meninggal</x-ui.badge>
                            <div>
                                <p class="text-sm font-medium">{{ $meninggal->penduduk?->nama ?? '-' }}</p>
                                <p class="text-xs text-base-content/60">
                                    {{ $meninggal->tanggal_meninggal?->format('d M Y') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </x-ui.card>
    </div>

</x-layouts.app>
