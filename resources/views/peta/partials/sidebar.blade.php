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

    {{-- Custom Layers Toggle --}}
    <template x-if="customLayers.length > 0">
        <div class="px-4 py-3 border-t border-base-200 flex-shrink-0">
            <p class="text-xs font-semibold text-base-content/40 uppercase tracking-wider mb-2">Layer Kustom</p>
            <div class="space-y-1.5">
                <template x-for="cl in customLayers" :key="cl.id">
                    <label class="flex items-center gap-2 cursor-pointer hover:bg-base-200 rounded-lg px-2 py-1.5 transition-colors">
                        <input type="checkbox" class="checkbox checkbox-xs" :checked="cl.visible"
                            @change="toggleCustomLayer(cl.id)">
                        <span class="w-3 h-3 rounded-sm flex-shrink-0 border border-base-300"
                            :style="'background-color:' + cl.warna"></span>
                        <span class="text-xs flex-1 truncate" x-text="cl.nama"></span>
                        <span class="badge badge-ghost badge-xs" x-text="cl.polygonCount"></span>
                    </label>
                </template>
            </div>
        </div>
    </template>

    {{-- Footer legend --}}
    <div class="p-3 border-t border-base-200 flex-shrink-0">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs text-base-content/50">
                <span class="inline-block w-5 h-0 border-t-2 border-dashed border-base-content/60"></span>
                <span>Batas Kelurahan Batua</span>
            </div>
            @if(auth()->user()->isAdmin())
            <div class="flex gap-1">
                <a href="{{ route('admin.peta-layer.index') }}" class="btn btn-ghost btn-xs" title="Kelola Layer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
