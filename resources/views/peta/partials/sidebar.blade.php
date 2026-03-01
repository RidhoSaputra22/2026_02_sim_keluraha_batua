{{-- Right sidebar: header, stats, RW list, legend footer --}}
<div
    class="peta-sidebar w-full lg:w-80 xl:w-96 bg-base-100 border-t lg:border-t-0 lg:border-l border-base-200 flex flex-col">

    {{-- Header --}}
    <div class="p-4 border-b border-base-200 flex-shrink-0">
        <div class="flex items-center justify-between">
            <h3 class="font-bold text-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                </svg>
                <span x-show="!selectedRw">Statistik Kelurahan</span>
                <span x-show="selectedRw" x-text="selectedRw"></span>
            </h3>
            <x-ui.button type="ghost" size="xs" :isSubmit="false" class="btn-circle" title="Kembali" x-show="selectedRw"
                @click="resetView()">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </x-ui.button>
        </div>
    </div>

    {{-- Stats --}}
    <div class="p-4 border-b border-base-200 flex-shrink-0">
        {{-- Global stats --}}
        <template x-if="!selectedRw">
            @include('peta.partials.stats-global')
        </template>

        {{-- Selected RW stats --}}
        <template x-if="selectedRw">
            @include('peta.partials.stats-selected')
        </template>
    </div>

    {{-- RW list --}}
    <div class="flex-1 overflow-y-auto">
        <div class="px-4 py-2 shrink-0">
            <p class="text-xs font-semibold text-base-content/40 uppercase tracking-wider">Daftar Wilayah RW</p>
        </div>
        @include('peta.partials.rw-list')
    </div>

    {{-- Footer legend --}}
    <div class="p-3 border-t border-base-200 flex-shrink-0">
        <div class="flex items-center gap-2 text-xs text-base-content/50">
            <span class="inline-block w-5 h-0 border-t-2 border-dashed border-base-content/60"></span>
            <span>Batas Kelurahan Batua</span>
        </div>
    </div>
</div>
