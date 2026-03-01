<x-layouts.app :title="'Kelola Layer Peta'">

    <x-slot:header>
        <x-layouts.page-header title="Kelola Layer Peta"
            description="Buat dan kelola layer peta kustom seperti daerah banjir, zona rawan, dan lainnya">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('peta.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Peta Utama
                </x-ui.button>
                <x-ui.button type="primary" size="sm" href="{{ route('admin.peta-layer.create') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Tambah Layer
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    {{-- Layer Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($layers as $layer)
        <x-ui.card :compact="true">
            <div class="flex items-start gap-3">
                {{-- Color indicator --}}
                <div class="flex-shrink-0 mt-1">
                    <div class="w-8 h-8 rounded-lg border border-base-300 shadow-inner"
                        style="background-color: {{ $layer->warna }}; opacity: {{ $layer->fill_opacity + 0.3 }};">
                    </div>
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-sm truncate">{{ $layer->nama }}</h3>
                        <x-ui.badge :type="$layer->is_active ? 'success' : 'ghost'" size="sm">
                            {{ $layer->is_active ? 'Aktif' : 'Nonaktif' }}
                        </x-ui.badge>
                    </div>

                    @if($layer->deskripsi)
                    <p class="text-xs text-base-content/60 mt-1 line-clamp-2">{{ $layer->deskripsi }}</p>
                    @endif

                    <div class="flex items-center gap-3 mt-2 text-xs text-base-content/50">
                        <span class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" /></svg>
                            {{ $layer->polygons_count }} polygon
                        </span>
                        <span>{{ \App\Models\PetaLayer::patternTypes()[$layer->pattern_type] ?? $layer->pattern_type }}</span>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-1 mt-3 pt-3 border-t border-base-200">
                <x-ui.button type="primary" size="xs" href="{{ route('admin.peta-layer.edit', $layer) }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>
                    Kelola Polygon
                </x-ui.button>

                <form method="POST" action="{{ route('admin.peta-layer.toggle-active', $layer) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-ghost btn-xs">
                        {{ $layer->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.peta-layer.destroy', $layer) }}"
                    onsubmit="return confirm('Hapus layer {{ $layer->nama }} beserta semua polygonnya?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-error btn-xs btn-outline btn-circle">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </form>
            </div>
        </x-ui.card>
        @empty
        <div class="col-span-full">
            <x-ui.card>
                <div class="text-center py-12">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-base-content/20 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>
                    <p class="text-base-content/50 mb-4">Belum ada layer peta. Buat layer pertama untuk mulai menandai area pada peta.</p>
                    <x-ui.button type="primary" size="sm" href="{{ route('admin.peta-layer.create') }}">
                        Buat Layer Pertama
                    </x-ui.button>
                </div>
            </x-ui.card>
        </div>
        @endforelse
    </div>

</x-layouts.app>
