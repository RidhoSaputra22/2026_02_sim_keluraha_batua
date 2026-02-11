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
                value="{{ number_format($totalPenduduk) }}"
                description="Data terakhir"
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
                value="{{ number_format($totalKK) }}"
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
                value="{{ $totalSuratBulanIni }}"
                description="Diproses"
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
                value="{{ $suratMenunggu }}"
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
                            @forelse($recentSurat as $surat)
                            <tr class="hover">
                                <td class="font-mono text-sm">{{ $surat->nomor_surat }}</td>
                                <td>{{ $surat->jenis->nama ?? '-' }}</td>
                                <td>{{ $surat->pemohon->nama ?? $surat->nama_dalam_surat ?? '-' }}</td>
                                <td>{{ $surat->tanggal_surat?->format('d M Y') ?? '-' }}</td>
                                <td>
                                    @php
                                        $statusColor = match($surat->status_esign) {
                                            'signed' => 'success',
                                            'proses' => 'info',
                                            'draft' => 'warning',
                                            'reject' => 'error',
                                            default => 'ghost',
                                        };
                                        $statusLabel = match($surat->status_esign) {
                                            'signed' => 'Selesai',
                                            'proses' => 'Proses',
                                            'draft' => 'Draft',
                                            'reject' => 'Ditolak',
                                            default => '-',
                                        };
                                    @endphp
                                    <x-ui.badge type="{{ $statusColor }}" size="sm">{{ $statusLabel }}</x-ui.badge>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-base-content/60">Belum ada data surat.</td>
                            </tr>
                            @endforelse
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
                        <span class="font-semibold">{{ $mutasiLahir }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-error rounded-full"></div>
                            <span class="text-sm">Kematian</span>
                        </div>
                        <span class="font-semibold">{{ $mutasiMeninggal }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-info rounded-full"></div>
                            <span class="text-sm">Datang</span>
                        </div>
                        <span class="font-semibold">{{ $mutasiDatang }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-warning rounded-full"></div>
                            <span class="text-sm">Pindah</span>
                        </div>
                        <span class="font-semibold">{{ $mutasiPindah }}</span>
                    </div>
                </div>
            </x-ui.card>

            {{-- Quick Actions --}}
            <x-ui.card title="Aksi Cepat">
                <div class="space-y-2">
                    <x-ui.button type="primary" size="sm" class="w-full justify-start gap-2" href="{{ route('admin.penduduk.create') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        Tambah Penduduk
                    </x-ui.button>
                    <x-ui.button type="secondary" size="sm" class="w-full justify-start gap-2" href="{{ route('admin.keluarga.create') }}" :outline="true">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Kartu Keluarga
                    </x-ui.button>
                    <x-ui.button type="accent" size="sm" class="w-full justify-start gap-2" href="{{ route('admin.users.create') }}" :outline="true">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Pengguna
                    </x-ui.button>
                </div>
            </x-ui.card>
        </div>
    </div>

    {{-- Bottom row: Surat per jenis & Data Usaha --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Surat per Jenis --}}
        <x-ui.card title="Rekap Surat per Jenis (Bulan Ini)">
            <div class="space-y-3">
                @php
                    $totalSurat = $suratPerJenis->sum('surats_count') ?: 1;
                @endphp
                @forelse($suratPerJenis as $jenis)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span>{{ $jenis->nama }}</span>
                            <span class="font-medium">{{ $jenis->surats_count }}</span>
                        </div>
                        <progress class="progress progress-primary w-full" value="{{ round(($jenis->surats_count / $totalSurat) * 100) }}" max="100"></progress>
                    </div>
                @empty
                    <p class="text-sm text-base-content/60 text-center py-4">Belum ada data surat bulan ini.</p>
                @endforelse
            </div>
        </x-ui.card>

        {{-- Data Usaha Ringkasan --}}
        <x-ui.card title="Data Usaha / UMKM">
            <div class="stats stats-vertical w-full">
                <div class="stat px-0">
                    <div class="stat-title">Total Usaha Terdaftar</div>
                    <div class="stat-value text-primary text-2xl">{{ $totalUsaha }}</div>
                    <div class="stat-desc">di seluruh wilayah kelurahan</div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 mt-4">
                <div class="bg-base-200 rounded-lg p-3 text-center">
                    <div class="text-xl font-bold text-base-content">{{ $usahaAktif }}</div>
                    <div class="text-xs text-base-content/60">Aktif</div>
                </div>
                <div class="bg-base-200 rounded-lg p-3 text-center">
                    <div class="text-xl font-bold text-base-content">{{ $usahaTidakAktif }}</div>
                    <div class="text-xs text-base-content/60">Tidak Aktif</div>
                </div>
            </div>
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="#">Lihat Detail →</x-ui.button>
            </x-slot:actions>
        </x-ui.card>
    </div>

    {{-- Admin-specific: System Overview --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- User Management Overview --}}
        <x-ui.card title="Manajemen Pengguna">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm">Total Pengguna</span>
                    <span class="font-bold text-lg">{{ $totalUsers }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm">Pengguna Aktif</span>
                    <x-ui.badge type="success" size="sm">{{ $activeUsers }}</x-ui.badge>
                </div>
                <div class="divider my-1"></div>
                @foreach($usersPerRole as $role)
                <div class="flex items-center justify-between text-sm">
                    <span>{{ \App\Models\Role::roleLabels()[$role->name] ?? ucfirst($role->name) }}</span>
                    <span class="font-medium">{{ $role->users_count }}</span>
                </div>
                @endforeach
            </div>
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('admin.users.index') }}">Kelola Pengguna →</x-ui.button>
            </x-slot:actions>
        </x-ui.card>

        {{-- Wilayah Overview --}}
        <x-ui.card title="Data Wilayah">
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-base-200 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-primary">{{ $totalRW }}</div>
                    <div class="text-sm text-base-content/60">RW</div>
                </div>
                <div class="bg-base-200 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-secondary">{{ $totalRT }}</div>
                    <div class="text-sm text-base-content/60">RT</div>
                </div>
            </div>
            @if($totalRT > 0)
            <div class="mt-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span>Penduduk/RT (rata-rata)</span>
                    <span class="font-medium">{{ $totalRT > 0 ? round($totalPenduduk / $totalRT) : 0 }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span>KK/RT (rata-rata)</span>
                    <span class="font-medium">{{ $totalRT > 0 ? round($totalKK / $totalRT) : 0 }}</span>
                </div>
            </div>
            @endif
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('admin.wilayah.index') }}">Kelola Wilayah →</x-ui.button>
            </x-slot:actions>
        </x-ui.card>

        {{-- Recent Users --}}
        <x-ui.card title="Pengguna Terbaru">
            <div class="space-y-3">
                @forelse($recentUsers as $user)
                <div class="flex items-start gap-3">
                    <div class="w-2 h-2 bg-{{ $user->is_active ? 'success' : 'error' }} rounded-full mt-2 shrink-0"></div>
                    <div>
                        <p class="text-sm font-medium">{{ $user->name }}</p>
                        <p class="text-xs text-base-content/60">{{ $user->role->name ?? '-' }} — {{ $user->created_at?->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <p class="text-sm text-base-content/60 text-center py-4">Belum ada pengguna.</p>
                @endforelse
            </div>
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('admin.audit-log') }}">Audit Log →</x-ui.button>
            </x-slot:actions>
        </x-ui.card>
    </div>

</x-layouts.app>
