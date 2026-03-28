@props([
    'endpoint',
    'title' => 'Peta Kelurahan Batua Raya',
    'subtitle' => 'Pantau batas kelurahan, persebaran RW, dan statistik wilayah langsung dari data peta publik.',
])

<div {{ $attributes->merge(['data-aos' => 'fade-up'])->class(['rounded-md border border-primary/10 bg-white p-6 shadow-xl shadow-primary/5 md:p-8']) }}>

    <div data-guest-kelurahan-map data-endpoint="{{ $endpoint }}" class="guest-map-shell">
        <div class="">
            <div class="relative overflow-hidden rounded-md border border-slate-200 bg-background-light">
                <div data-map-loading
                    class="absolute inset-0 z-[450] flex items-center justify-center bg-white/80 backdrop-blur-sm">
                    <div class="text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-md bg-primary/10 text-primary">
                            <span class="material-icons animate-pulse text-3xl">map</span>
                        </div>
                        <p class="mt-4 text-lg font-semibold text-slate-900">Memuat peta kelurahan...</p>
                        <p class="mt-1 text-sm text-slate-500">Mengambil boundary dan polygon RW dari endpoint publik.</p>
                    </div>
                </div>
                <div data-map-error
                    class="pointer-events-none absolute inset-x-4 bottom-4 z-[520] hidden rounded-md border border-red-200 bg-white/95 p-4 text-sm text-red-600 shadow-lg shadow-red-100">
                </div>
                <div data-map-canvas class="h-[420px] w-full md:h-[560px]"></div>
            </div>
        </div>
    </div>
</div>

@once
    @vite('resources/js/guest/kelurahan-map.js')
@endonce
