{{-- Map container with loading overlay & toolbar --}}
<div class="flex-1 relative">
    <div id="map" class="w-full"></div>

    {{-- Loading overlay --}}
    <div x-show="loading" x-transition
        class="absolute inset-0 bg-base-100/80 flex items-center justify-center z-10 rounded-l-xl">
        <div class="text-center">
            <span class="loading loading-spinner loading-lg text-primary"></span>
            <p class="mt-2 text-sm text-base-content/60">Memuat peta...</p>
        </div>
    </div>

    {{-- Map toolbar buttons --}}
    <div class="absolute top-3 left-3 z-[1000] flex gap-1.5">
        <button class="btn btn-xs shadow-md" :class="showKelurahan ? 'btn-primary' : 'btn-ghost bg-base-100'"
            @click="toggleKelurahan()" title="Batas Kelurahan">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6z" />
            </svg>
            Batas
        </button>
        <button class="btn btn-xs shadow-md" :class="showLabels ? 'btn-primary' : 'btn-ghost bg-base-100'"
            @click="toggleLabels()" title="Label RW">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            Label
        </button>
        <button class="btn btn-xs btn-ghost bg-base-100 shadow-md"
            @click="resetZoom()"
            title="Kembali ke tampilan awal">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
        </button>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('peta.rw-polygon.edit', \App\Models\Rw::orderBy('nomor')->first() ?? 1) }}"
            class="btn btn-xs btn-ghost bg-base-100 shadow-md" title="Kelola Polygon RW">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Edit RW
        </a>
        @endif
    </div>
</div>
