@props([
    'endpoint',
    'title' => 'Peta Kelurahan Batua Raya',
    'subtitle' => 'Pantau batas kelurahan, persebaran RW, dan statistik wilayah langsung dari data peta publik.',
])

<div class="rounded-[2rem] border border-primary/10 bg-white p-6 shadow-xl shadow-primary/5 md:p-8">
    <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="text-3xl font-extrabold text-slate-900 md:text-4xl">{{ $title }}</h2>
            <p class="mt-3 max-w-3xl text-base leading-7 text-slate-500">{{ $subtitle }}</p>
        </div>

        <div class="flex flex-wrap gap-2">
            <span class="inline-flex items-center gap-2 rounded-full bg-primary/10 px-4 py-2 text-sm font-semibold text-primary">
                <span class="material-icons text-base">public</span>
                API Peta Publik
            </span>
            <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700">
                <span class="material-icons text-base">layers</span>
                Layer Kelurahan & RW
            </span>
        </div>
    </div>

    <div data-guest-kelurahan-map data-endpoint="{{ $endpoint }}" class="guest-map-shell">
        <div class="grid gap-6 xl:grid-cols-[minmax(0,1.7fr)_360px]">
            <div class="relative overflow-hidden rounded-[1.75rem] border border-slate-200 bg-background-light">
                <div class="absolute left-4 top-4 z-[500] flex flex-wrap gap-2">
                    <button type="button" data-toggle-layer="kelurahan" class="guest-map-toggle is-active">
                        Batas Kelurahan
                    </button>
                    <button type="button" data-toggle-layer="rw" class="guest-map-toggle is-active">
                        Layer RW
                    </button>
                    <button type="button" data-reset-view class="guest-map-toggle guest-map-toggle-secondary">
                        Reset View
                    </button>
                </div>

                <div data-map-loading
                    class="absolute inset-0 z-[450] flex items-center justify-center bg-white/80 backdrop-blur-sm">
                    <div class="text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-primary/10 text-primary">
                            <span class="material-icons animate-pulse text-3xl">map</span>
                        </div>
                        <p class="mt-4 text-lg font-semibold text-slate-900">Memuat peta kelurahan...</p>
                        <p class="mt-1 text-sm text-slate-500">Mengambil boundary dan polygon RW dari endpoint publik.</p>
                    </div>
                </div>

                <div data-map-error
                    class="pointer-events-none absolute inset-x-4 bottom-4 z-[520] hidden rounded-2xl border border-red-200 bg-white/95 p-4 text-sm text-red-600 shadow-lg shadow-red-100">
                </div>

                <div data-map-canvas class="h-[420px] w-full md:h-[560px]"></div>
            </div>

            <div class="space-y-4">
                <div class="rounded-[1.5rem] border border-primary/10 bg-background-light p-5">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-400">Ringkasan Peta</p>
                            <h3 class="mt-2 text-xl font-bold text-slate-900">Statistik Wilayah</h3>
                        </div>
                        <span data-updated-at
                            class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-500">Memuat...</span>
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-3">
                        <div class="guest-map-stat-card">
                            <p class="guest-map-stat-label">Total RW</p>
                            <p class="guest-map-stat-value" data-summary-value="total_rw">0</p>
                        </div>
                        <div class="guest-map-stat-card">
                            <p class="guest-map-stat-label">Total RT</p>
                            <p class="guest-map-stat-value" data-summary-value="total_rt">0</p>
                        </div>
                        <div class="guest-map-stat-card">
                            <p class="guest-map-stat-label">Penduduk</p>
                            <p class="guest-map-stat-value" data-summary-value="total_penduduk">0</p>
                        </div>
                        <div class="guest-map-stat-card">
                            <p class="guest-map-stat-label">UMKM</p>
                            <p class="guest-map-stat-value" data-summary-value="total_umkm">0</p>
                        </div>
                    </div>

                    <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-1">
                        <div class="rounded-2xl border border-white/70 bg-white p-4">
                            <p class="text-sm text-slate-500">Laki-laki</p>
                            <p class="mt-2 text-lg font-bold text-slate-900" data-summary-value="laki_laki">0</p>
                        </div>
                        <div class="rounded-2xl border border-white/70 bg-white p-4">
                            <p class="text-sm text-slate-500">Perempuan</p>
                            <p class="mt-2 text-lg font-bold text-slate-900" data-summary-value="perempuan">0</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-[1.5rem] border border-primary/10 bg-white p-5">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-400">Eksplorasi RW</p>
                            <h3 class="mt-2 text-xl font-bold text-slate-900">Daftar Wilayah</h3>
                        </div>
                        <span data-rw-count class="rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">
                            0 wilayah
                        </span>
                    </div>

                    <div data-rw-list class="mt-5 max-h-[320px] space-y-2 overflow-y-auto pr-1">
                        <div class="rounded-2xl border border-dashed border-slate-200 p-4 text-sm text-slate-500">
                            Data RW sedang dimuat...
                        </div>
                    </div>
                </div>

                <div class="rounded-[1.5rem] border border-primary/10 bg-background-light p-5">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-400">Legend</p>

                    <div class="mt-4 space-y-3 text-sm text-slate-600">
                        <div class="flex items-center gap-3">
                            <span class="h-3 w-8 rounded-full border-2 border-dashed border-primary"></span>
                            <span>Batas resmi kelurahan</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="h-3 w-8 rounded-full bg-primary/30 ring-1 ring-primary/30"></span>
                            <span>Polygon wilayah RW</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="material-icons text-base text-slate-400">ads_click</span>
                            <span>Klik RW di peta atau daftar untuk fokus ke wilayah.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@once
    @vite('resources/js/guest/kelurahan-map.js')
@endonce
