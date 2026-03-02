{{-- SECTION 1: STAT CARDS (6 cards) --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-8">

    {{-- Total Penduduk --}}
    <div class="card bg-base-100 shadow-sm border border-base-200 hover:shadow-md transition-shadow">
        <div class="card-body p-4">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span class="text-xs text-base-content/50 font-medium">Penduduk</span>
            </div>
            <p class="text-2xl font-bold text-primary">{{ number_format($totalPenduduk) }}</p>
            <p class="text-xs text-base-content/40">jiwa terdaftar</p>
        </div>
    </div>

    {{-- Kartu Keluarga --}}
    <div class="card bg-base-100 shadow-sm border border-base-200 hover:shadow-md transition-shadow">
        <div class="card-body p-4">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-secondary/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <span class="text-xs text-base-content/50 font-medium">KK</span>
            </div>
            <p class="text-2xl font-bold text-secondary">{{ number_format($totalKK) }}</p>
            <p class="text-xs text-base-content/40">kartu keluarga</p>
        </div>
    </div>

    {{-- RW / RT --}}
    <div class="card bg-base-100 shadow-sm border border-base-200 hover:shadow-md transition-shadow">
        <div class="card-body p-4">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-accent/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                </div>
                <span class="text-xs text-base-content/50 font-medium">RW / RT</span>
            </div>
            <p class="text-2xl font-bold text-accent">{{ $totalRW }} <span class="text-sm font-normal text-base-content/40">/ {{ $totalRT }}</span></p>
            <p class="text-xs text-base-content/40">wilayah</p>
        </div>
    </div>

    {{-- UMKM --}}
    <div class="card bg-base-100 shadow-sm border border-base-200 hover:shadow-md transition-shadow">
        <div class="card-body p-4">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-info/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <span class="text-xs text-base-content/50 font-medium">UMKM</span>
            </div>
            <p class="text-2xl font-bold text-info">{{ number_format($totalUsaha) }}</p>
            <p class="text-xs text-base-content/40">{{ $usahaAktif }} aktif</p>
        </div>
    </div>

    {{-- Mutasi Bulan Ini --}}
    <div class="card bg-base-100 shadow-sm border border-base-200 hover:shadow-md transition-shadow">
        <div class="card-body p-4">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-warning/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
                <span class="text-xs text-base-content/50 font-medium">Mutasi</span>
            </div>
            <p class="text-2xl font-bold text-warning">{{ $mutasiLahir + $mutasiMeninggal + $mutasiDatang + $mutasiPindah }}</p>
            <p class="text-xs text-base-content/40">{{ now()->translatedFormat('F Y') }}</p>
        </div>
    </div>

    {{-- Pengguna --}}
    <div class="card bg-base-100 shadow-sm border border-base-200 hover:shadow-md transition-shadow">
        <div class="card-body p-4">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-success/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <span class="text-xs text-base-content/50 font-medium">Users</span>
            </div>
            <p class="text-2xl font-bold text-success">{{ $activeUsers }} <span class="text-sm font-normal text-base-content/40">/ {{ $totalUsers }}</span></p>
            <p class="text-xs text-base-content/40">aktif</p>
        </div>
    </div>

</div>
