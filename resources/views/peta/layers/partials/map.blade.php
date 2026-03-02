{{-- LEFT: Map editor with polygon drawing --}}
<div class="flex-1 relative">
    <div id="layer-map" class="w-full"></div>

    {{-- Loading overlay --}}
    <div x-show="loading" x-transition
        class="absolute inset-0 bg-base-100/80 flex items-center justify-center z-10 rounded-l-xl">
        <div class="text-center">
            <span class="loading loading-spinner loading-lg text-primary"></span>
            <p class="mt-2 text-sm text-base-content/60">Memuat peta...</p>
        </div>
    </div>



    {{-- Active layer indicator on map --}}
    <div class="absolute bottom-3 left-3 z-[1000]" x-show="activeLayer" x-transition>
        <div class="bg-base-100 rounded-lg shadow-lg px-3 py-2 flex items-center gap-2 text-xs">
            <div class="layer-color-dot" :style="'background-color:' + (activeLayer?.warna || '#ccc')"></div>
            <span class="font-semibold" x-text="'Editing: ' + (activeLayer?.nama || '')"></span>
            <span class="badge badge-xs badge-primary" x-text="activePolygonCount + ' polygon'"></span>
        </div>
    </div>

    {{-- No layer selected hint --}}
    <div class="absolute bottom-3 left-3 z-[1000]" x-show="!activeLayer && !loading" x-transition>
        <div class="bg-base-100/90 rounded-lg shadow-lg px-3 py-2 text-xs text-base-content/60">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 inline mr-1" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Pilih layer di sidebar untuk mulai mengedit polygon
        </div>
    </div>
</div>
