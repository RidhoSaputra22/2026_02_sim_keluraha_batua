<x-layouts.app :title="'Edit Layer: ' . $petaLayer->nama">

    <x-slot:header>
        <x-layouts.page-header title="Edit Layer: {{ $petaLayer->nama }}"
            description="Kelola pengaturan dan gambar polygon untuk layer ini">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('admin.peta-layer.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <div x-data="layerPolygonEditor()" x-init="init()">

        {{-- Layer Settings (accordion) --}}
        <div class="collapse collapse-arrow bg-base-100 shadow-xl mb-4">
            <input type="checkbox" />
            <div class="collapse-title font-semibold text-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Pengaturan Layer
            </div>
            <div class="collapse-content">
                <form method="POST" action="{{ route('admin.peta-layer.update', $petaLayer) }}">
                    @csrf @method('PUT')
                    @include('peta.layers._form')
                    <div class="flex justify-end gap-2 mt-4">
                        <x-ui.button type="primary" size="sm">Simpan Pengaturan</x-ui.button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Map Editor --}}
        <x-ui.card>
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-bold text-sm flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                    Editor Polygon
                    <span class="badge badge-sm" :class="polygonCount > 0 ? 'badge-primary' : 'badge-ghost'"
                        x-text="polygonCount + ' polygon'"></span>
                </h3>
                <div class="flex items-center gap-2">
                    <label class="label cursor-pointer gap-2">
                        <span class="label-text text-xs">Tampilkan RW</span>
                        <input type="checkbox" class="toggle toggle-xs toggle-primary" checked
                            @change="toggleRwOverlay($event.target.checked)">
                    </label>
                </div>
            </div>

            <div class="mb-3 text-xs text-base-content/60 flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 flex-shrink-0" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Gambar polygon untuk menandai area pada peta. Anda bisa membuat beberapa polygon dalam satu layer.
                Setiap polygon otomatis tersimpan.
            </div>

            <x-ui.leaflet-draw id="layer-polygon-map" height="550px" :color="$petaLayer->warna" />
        </x-ui.card>

        {{-- Polygon List --}}
        <x-ui.card class="mt-4" :compact="true">
            <h3 class="font-bold text-sm mb-3">Daftar Polygon</h3>
            <div class="space-y-2" id="polygon-list">
                <template x-for="(poly, index) in polygonList" :key="poly.id || index">
                    <div class="flex items-center gap-3 p-2 rounded-lg bg-base-200/50 hover:bg-base-200">
                        <div class="w-4 h-4 rounded-sm flex-shrink-0"
                            style="background-color: {{ $petaLayer->warna }};"></div>
                        <div class="flex-1 min-w-0">
                            <input type="text" class="input input-bordered input-xs w-full max-w-xs"
                                :value="poly.nama || 'Polygon ' + (index + 1)"
                                @change="updatePolygonName(poly, $event.target.value)" placeholder="Nama polygon...">
                        </div>
                        <button class="btn btn-ghost btn-xs btn-circle" @click="zoomToPolygon(poly)"
                            title="Fokus ke polygon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                        <button class="btn btn-error btn-xs btn-outline btn-circle" @click="deletePolygon(poly, index)"
                            title="Hapus polygon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </template>
                <template x-if="polygonList.length === 0">
                    <p class="text-sm text-base-content/50 text-center py-4">Belum ada polygon. Gunakan toolbar gambar
                        di peta untuk memulai.</p>
                </template>
            </div>
        </x-ui.card>

        {{-- Toast --}}
        <div x-show="message" x-transition class="toast toast-top toast-end z-50">
            <div class="alert" :class="messageType === 'success' ? 'alert-success' : 'alert-error'">
                <span x-text="message"></span>
            </div>
        </div>
    </div>

    @push('scripts')
    @php
    $layerRoutes = [
    'geojsonRw' => route('peta.geojson.rw'),
    'polygonStore' => route('admin.peta-layer.polygon.store', $petaLayer),
    'polygonBase' => url('admin/peta-layer/' . $petaLayer->id . '/polygon'),
    ];
    @endphp
    {{-- Leaflet & Leaflet.Draw already loaded by <x-ui.leaflet-draw> component --}}
    @vite('resources/js/map/index.js')

    <script>
    const LAYER_ROUTES = @json($layerRoutes);
    const LAYER_CONFIG = {
        color: @json($petaLayer - > warna),
        opacity: @json($petaLayer - > fill_opacity),
        strokeWidth: @json($petaLayer - > stroke_width),
        existingData: {
            !!$geojsonCollection!!
        },
    };

    function layerPolygonEditor() {
        return {
            _editor: null,
            polygonList: [],
            polygonCount: 0,
            message: '',
            messageType: 'success',

            init() {
                this._waitForDeps();
            },

            _waitForDeps() {
                if (window.SimPeta && window.L && window.L.Draw) {
                    this._bootstrap();
                } else {
                    requestAnimationFrame(() => this._waitForDeps());
                }
            },

            _bootstrap() {
                this._editor = new SimPeta.PolygonEditor('layer-polygon-map', {
                    color: LAYER_CONFIG.color,
                    fillOpacity: LAYER_CONFIG.opacity,
                    strokeWidth: LAYER_CONFIG.strokeWidth,
                    rectangle: true,
                }).init();

                // RW overlay for reference
                this._editor.loadRwOverlay(LAYER_ROUTES.geojsonRw);

                // Load existing polygons
                this.polygonList = this._editor.loadExistingCollection(LAYER_CONFIG.existingData);
                this.polygonCount = this.polygonList.length;

                // Draw events (multi-polygon mode)
                this._editor.onMultiPolygonChange({
                    onCreated: (layer) => this._saveNewPolygon(layer),
                    onEdited: (layer) => this._updateGeometry(layer),
                    onDeleted: (layer) => {
                        const poly = this.polygonList.find(p => p.layer === layer);
                        if (poly && poly.id) this._deleteFromServer(poly.id);
                        this.polygonList = this.polygonList.filter(p => p.layer !== layer);
                        this.polygonCount = this.polygonList.length;
                    },
                });
            },

            toggleRwOverlay(show) {
                this._editor.toggleRwOverlay(show);
            },

            zoomToPolygon(poly) {
                this._editor.zoomToLayer(poly.layer);
            },

            async _saveNewPolygon(layer) {
                try {
                    const geojson = layer.toGeoJSON().geometry;
                    const data = await SimPeta.apiPost(LAYER_ROUTES.polygonStore, {
                        geojson,
                        nama: 'Polygon ' + (this.polygonList.length + 1),
                    });
                    if (data.success) {
                        this.polygonList.push({
                            id: data.id,
                            nama: 'Polygon ' + this.polygonList.length,
                            layer
                        });
                        this.polygonCount = this.polygonList.length;
                        this._flash('Polygon berhasil disimpan.', 'success');
                    }
                } catch (e) {
                    this._flash('Gagal menyimpan polygon: ' + e.message, 'error');
                }
            },

            async _updateGeometry(layer) {
                const poly = this.polygonList.find(p => p.layer === layer);
                if (!poly || !poly.id) return;
                try {
                    await SimPeta.apiPut(LAYER_ROUTES.polygonBase + '/' + poly.id, {
                        geojson: layer.toGeoJSON().geometry,
                    });
                    this._flash('Polygon berhasil diperbarui.', 'success');
                } catch (e) {
                    this._flash('Gagal memperbarui polygon.', 'error');
                }
            },

            async updatePolygonName(poly, newName) {
                if (!poly.id) return;
                poly.nama = newName;
                try {
                    await SimPeta.apiPut(LAYER_ROUTES.polygonBase + '/' + poly.id, {
                        nama: newName
                    });
                } catch (e) {
                    console.error('Failed to update polygon name:', e);
                }
            },

            async deletePolygon(poly, index) {
                if (!confirm('Hapus polygon ini?')) return;
                if (poly.id) await this._deleteFromServer(poly.id);
                if (poly.layer) this._editor.drawnItems.removeLayer(poly.layer);
                this.polygonList.splice(index, 1);
                this.polygonCount = this.polygonList.length;
                this._flash('Polygon berhasil dihapus.', 'success');
            },

            async _deleteFromServer(id) {
                try {
                    await SimPeta.apiDelete(LAYER_ROUTES.polygonBase + '/' + id);
                } catch (e) {
                    console.error('Failed to delete polygon:', e);
                }
            },

            _flash(msg, type = 'success') {
                this.message = msg;
                this.messageType = type;
                setTimeout(() => {
                    this.message = '';
                }, 4000);
            },
        };
    }
    </script>
    @endpush

    @push('styles')
    <style>
    .rw-label-ref {
        background: none !important;
        border: none !important;
        box-shadow: none !important;
        font-weight: 600;
        font-size: 10px;
        color: #64748b;
        text-shadow: 1px 1px 2px white, -1px -1px 2px white;
        white-space: nowrap;
        pointer-events: none !important;
    }
    </style>
    @endpush

</x-layouts.app>
