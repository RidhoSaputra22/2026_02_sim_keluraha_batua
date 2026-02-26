<x-layouts.app :title="'Dashboard'">

    {{-- Page Header --}}
    <x-slot:header>
        <x-layouts.page-header title="Dashboard" description="Ringkasan data dan aktivitas Kelurahan Batua" />
    </x-slot:header>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">

        {{-- Total Penduduk --}}
        <div class="card bg-base-100 shadow border border-primary/10">
            <div class="card-body p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-base-content/60 font-medium">Total Penduduk</p>
                        <p class="text-3xl font-bold text-base-content mt-1">{{ number_format($totalPenduduk) }}</p>
                        <p class="text-xs text-base-content/50 mt-1">Data terakhir diperbarui</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kartu Keluarga --}}
        <div class="card bg-base-100 shadow border border-secondary/10">
            <div class="card-body p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-base-content/60 font-medium">Kartu Keluarga</p>
                        <p class="text-3xl font-bold text-base-content mt-1">{{ number_format($totalKK) }}</p>
                        <p class="text-xs text-base-content/50 mt-1">KK terdaftar aktif</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-secondary/10 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-secondary" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mutasi Bulan Ini --}}
        <div class="card bg-base-100 shadow border border-accent/10">
            <div class="card-body p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-base-content/60 font-medium">Mutasi Bulan Ini</p>
                        <p class="text-3xl font-bold text-base-content mt-1">{{ $mutasiLahir + $mutasiMeninggal + $mutasiDatang + $mutasiPindah }}</p>
                        <p class="text-xs text-base-content/50 mt-1">{{ now()->translatedFormat('F Y') }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-accent" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pengguna Aktif --}}
        <div class="card bg-base-100 shadow border border-warning/20">
            <div class="card-body p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-base-content/60 font-medium">Pengguna Aktif</p>
                        <p class="text-3xl font-bold text-base-content mt-1">{{ $activeUsers }}</p>
                        <p class="text-xs text-base-content/50 mt-1">dari {{ $totalUsers }} total pengguna</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-warning/10 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-warning" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Mutasi Penduduk & Aksi Cepat --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        {{-- Mutasi Penduduk --}}
        <div class="lg:col-span-2">
            <x-ui.card title="Mutasi Penduduk (Bulan Ini)">
                <div class="space-y-2">
                    @php
                    $mutasiItems = [
                    ['label' => 'Kelahiran', 'value' => $mutasiLahir, 'color' => 'text-success', 'bg' =>
                    'bg-success/10', 'icon' => '
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />'],
                    ['label' => 'Kematian', 'value' => $mutasiMeninggal, 'color' => 'text-error', 'bg' => 'bg-error/10',
                    'icon' => '
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />'],
                    ['label' => 'Datang', 'value' => $mutasiDatang, 'color' => 'text-info', 'bg' => 'bg-info/10', 'icon'
                    => '
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 16l-4-4m0 0l4-4m-4 4h14" />'],
                    ['label' => 'Pindah', 'value' => $mutasiPindah, 'color' => 'text-warning', 'bg' => 'bg-warning/10',
                    'icon' => '
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 7l5 5m0 0l-5 5m5-5H6" />'],
                    ];
                    @endphp
                    @foreach($mutasiItems as $item)
                    <div class="flex items-center justify-between rounded-lg px-3 py-2 {{ $item['bg'] }}">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $item['color'] }}" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                {!! $item['icon'] !!}
                            </svg>
                            <span class="text-sm font-medium">{{ $item['label'] }}</span>
                        </div>
                        <span class="text-lg font-bold {{ $item['color'] }}">{{ $item['value'] }}</span>
                    </div>
                    @endforeach
                    <div class="pt-1 border-t border-base-300">
                        <div class="flex items-center justify-between text-xs text-base-content/50 px-1">
                            <span>Total perubahan</span>
                            <span
                                class="font-semibold text-base-content">{{ $mutasiLahir + $mutasiMeninggal + $mutasiDatang + $mutasiPindah }}</span>
                        </div>
                    </div>
                </div>
            </x-ui.card>

        </div>

        {{-- Aksi Cepat --}}
        <div>
            <x-ui.card title="Aksi Cepat">
                <div class="space-y-2">
                    <x-ui.button type="primary" size="sm" class="w-full justify-start gap-2"
                        href="{{ route('kependudukan.penduduk.create') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        Tambah Penduduk
                    </x-ui.button>
                    <x-ui.button type="secondary" size="sm" class="w-full justify-start gap-2"
                        href="{{ route('kependudukan.keluarga.create') }}" :outline="true">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Kartu Keluarga
                    </x-ui.button>
                    <x-ui.button type="ghost" size="sm" class="w-full justify-start gap-2"
                        href="{{ route('admin.users.create') }}" :outline="true">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        Tambah Pengguna
                    </x-ui.button>
                </div>
            </x-ui.card>
        </div>

    </div>

    {{-- Data Usaha & Wilayah --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        {{-- Data Usaha Ringkasan --}}
        <x-ui.card title="Data Usaha / UMKM">
            <div class="flex items-center gap-4 mb-5">
                <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-primary" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-base-content/60">Total Usaha Terdaftar</p>
                    <p class="text-4xl font-bold text-primary">{{ $totalUsaha }}</p>
                    <p class="text-xs text-base-content/50">di seluruh wilayah kelurahan</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-xl bg-success/10 p-4 text-center">
                    <div class="text-2xl font-bold text-success">{{ $usahaAktif }}</div>
                    <div class="text-xs text-success/70 font-medium mt-1">Aktif</div>
                    @if($totalUsaha > 0)
                    <div class="text-xs text-base-content/40 mt-0.5">{{ round(($usahaAktif / $totalUsaha) * 100) }}%
                    </div>
                    @endif
                </div>
                <div class="rounded-xl bg-base-200 p-4 text-center">
                    <div class="text-2xl font-bold text-base-content/60">{{ $usahaTidakAktif }}</div>
                    <div class="text-xs text-base-content/50 font-medium mt-1">Tidak Aktif</div>
                    @if($totalUsaha > 0)
                    <div class="text-xs text-base-content/40 mt-0.5">
                        {{ round(($usahaTidakAktif / $totalUsaha) * 100) }}%</div>
                    @endif
                </div>
            </div>
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('usaha.index') }}">Lihat Detail →</x-ui.button>
            </x-slot:actions>
        </x-ui.card>

        {{-- Wilayah Overview --}}
        <x-ui.card title="Data Wilayah">
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="rounded-xl bg-primary/10 p-4 text-center">
                    <div class="text-3xl font-bold text-primary">{{ $totalRW }}</div>
                    <div class="text-xs text-primary/70 font-semibold mt-1">RW</div>
                </div>
                <div class="rounded-xl bg-secondary/10 p-4 text-center">
                    <div class="text-3xl font-bold text-secondary">{{ $totalRT }}</div>
                    <div class="text-xs text-secondary/70 font-semibold mt-1">RT</div>
                </div>
            </div>
            @if($totalRT > 0)
            <div class="space-y-2 text-sm">
                <div class="flex justify-between items-center py-1.5 border-b border-base-200">
                    <span class="text-base-content/60">Penduduk / RT</span>
                    <span class="font-semibold">~{{ round($totalPenduduk / $totalRT) }} jiwa</span>
                </div>
                <div class="flex justify-between items-center py-1.5">
                    <span class="text-base-content/60">KK / RT</span>
                    <span class="font-semibold">~{{ round($totalKK / $totalRT) }} KK</span>
                </div>
            </div>
            @endif
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('master.wilayah.index') }}">Kelola Wilayah →
                </x-ui.button>
            </x-slot:actions>
        </x-ui.card>

    </div>

    {{-- System Overview --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- User Management Overview --}}
        <x-ui.card title="Manajemen Pengguna">
            <div class="space-y-2 mb-4">
                <div class="flex items-center justify-between p-2 rounded-lg bg-base-200">
                    <span class="text-sm">Total Pengguna</span>
                    <span class="font-bold text-base">{{ $totalUsers }}</span>
                </div>
                <div class="flex items-center justify-between p-2 rounded-lg bg-success/10">
                    <span class="text-sm">Pengguna Aktif</span>
                    <x-ui.badge type="success" size="sm">{{ $activeUsers }}</x-ui.badge>
                </div>
            </div>
            <div class="divider text-xs text-base-content/40 my-2">Per Peran</div>
            <div class="space-y-1.5">
                @foreach($usersPerRole as $role)
                <div class="flex items-center justify-between text-sm px-1">
                    <span
                        class="text-base-content/70">{{ \App\Models\Role::roleLabels()[$role->name] ?? ucfirst($role->name) }}</span>
                    <div class="flex items-center gap-2">
                        <div class="w-16 bg-base-200 rounded-full h-1.5">
                            <div class="bg-primary h-1.5 rounded-full"
                                style="width: {{ $totalUsers > 0 ? round(($role->users_count / $totalUsers) * 100) : 0 }}%">
                            </div>
                        </div>
                        <span class="font-semibold w-4 text-right">{{ $role->users_count }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('admin.users.index') }}">Kelola Pengguna →
                </x-ui.button>
            </x-slot:actions>
        </x-ui.card>

        {{-- Recent Users --}}
        <x-ui.card title="Pengguna Terbaru">
            <div class="space-y-3">
                @forelse($recentUsers as $user)
                <div class="flex items-center gap-3">
                    <div class="avatar placeholder shrink-0">
                        <div class="w-8 rounded-full bg-neutral text-neutral-content text-xs">
                            <span>{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">{{ $user->name }}</p>
                        <p class="text-xs text-base-content/50">
                            {{ \App\Models\Role::roleLabels()[$user->role?->name] ?? ucfirst($user->role?->name ?? '-') }}
                        </p>
                    </div>
                    <div class="shrink-0">
                        <span
                            class="inline-flex items-center gap-1 text-xs {{ $user->is_active ? 'text-success' : 'text-error' }}">
                            <span
                                class="w-1.5 h-1.5 rounded-full {{ $user->is_active ? 'bg-success' : 'bg-error' }}"></span>
                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
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
