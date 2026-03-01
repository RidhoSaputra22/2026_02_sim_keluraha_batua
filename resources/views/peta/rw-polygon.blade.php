<x-layouts.app :title="'Kelola Polygon RW ' . str_pad($rw->nomor, 2, '0', STR_PAD_LEFT)">

    <x-slot:header>
        <x-layouts.page-header title="Kelola Polygon RW {{ str_pad($rw->nomor, 2, '0', STR_PAD_LEFT) }}"
            description="Gambar atau edit batas wilayah RW pada peta">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('peta.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Peta
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>


    <div x-data="rwPolygonEditor()" x-init="init()">

        {{-- RW Selector --}}
        <x-ui.card class="mb-4" :compact="true">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-4">
                <div class="flex-1">
                    <label class="label"><span class="label-text font-semibold">Pilih RW yang akan
                            diedit:</span></label>
                    <select class="select select-bordered select-sm w-full max-w-xs"
                        @change="window.location.href = $event.target.value">
                        @foreach($rwList as $r)
                        <option value="{{ route('peta.rw-polygon.edit', $r) }}"
                            {{ $r->id === $rw->id ? 'selected' : '' }}>
                            RW {{ str_pad($r->nomor, 2, '0', STR_PAD_LEFT) }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Color Picker --}}
                <div>
                    <label class="label"><span class="label-text font-semibold">Warna Polygon:</span></label>
                    <div class="flex items-center gap-2">
                        <input type="color" x-model="currentColor"
                            class="w-10 h-10 rounded cursor-pointer border border-base-300 p-0.5"
                            title="Pilih warna polygon">
                        <span class="text-xs font-mono text-base-content/60" x-text="currentColor"></span>
                        <button class="btn btn-xs btn-outline" @click="saveColor()" :disabled="savingColor">
                            <span x-show="!savingColor">Simpan Warna</span>
                            <span x-show="savingColor" class="loading loading-spinner loading-xs"></span>
                        </button>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="btn btn-primary btn-sm" @click="savePolygon()" :disabled="!hasChanges || saving">
                        <span x-show="!saving">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Polygon
                        </span>
                        <span x-show="saving" class="loading loading-spinner loading-xs"></span>
                    </button>
                    <button class="btn btn-error btn-sm btn-outline" @click="deletePolygon()"
                        :disabled="!hasExisting || saving">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus
                    </button>
                </div>
            </div>

            {{-- Status indicator --}}
            <div class="mt-3 flex items-center gap-2">
                <template x-if="hasExisting && !hasChanges">
                    <span class="badge badge-success badge-sm gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Polygon tersimpan
                    </span>
                </template>
                <template x-if="hasChanges">
                    <span class="badge badge-warning badge-sm gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Ada perubahan belum disimpan
                    </span>
                </template>
                <template x-if="!hasExisting && !hasChanges">
                    <span class="badge badge-ghost badge-sm">Belum ada polygon</span>
                </template>
            </div>
        </x-ui.card>

        {{-- Map --}}
        <x-ui.card>
            <div class="mb-3 flex items-center gap-2 text-sm text-base-content/60">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Gunakan toolbar di kiri atas peta untuk menggambar polygon. Klik titik-titik batas wilayah, lalu klik
                titik pertama untuk menutup polygon.
            </div>
            <x-ui.leaflet-draw id="rw-polygon-map" height="550px" color="#6366f1" />
        </x-ui.card>

        {{-- Alert messages --}}
        <div x-show="message" x-transition class="toast toast-top toast-end z-50">
            <div class="alert" :class="messageType === 'success' ? 'alert-success' : 'alert-error'">
                <span x-text="message"></span>
            </div>
        </div>
    </div>

    @push('scripts')
    @php
    $rwPolygonsForJs = collect($allRwPolygons)
        ->filter(fn($p) => $p->id !== $rw->id && $p->geojson)
        ->map(fn($p) => [
            'label' => 'RW ' . str_pad($p->nomor, 2, '0', STR_PAD_LEFT),
            'warna' => $p->warna ?? '#6b7280',
            'geojson' => json_decode($p->geojson),
        ])
        ->values()
        ->toArray();
    @endphp
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
    @vite('resources/js/map/index.js')

    <script>
    const RW_EDITOR = {
        currentRwId:     @json($rw->id),
        currentWarna:    @json($rw->warna ?? '#6366f1'),
        hasExisting:     @json((bool) $polygonGeojson),
        kelurahanGeojson: @json($kelurahanGeojson ? json_decode($kelurahanGeojson) : null),
        polygonGeojson:  @json($polygonGeojson ? json_decode($polygonGeojson) : null),
        allRwPolygons:   @json($rwPolygonsForJs),
        routes: {
            update:      @json(route('peta.rw-polygon.update', $rw)),
            delete:      @json(route('peta.rw-polygon.delete', $rw)),
            colorUpdate: @json(route('peta.rw-color.update', $rw)),
        },
    };

    function rwPolygonEditor() {
        return {
            _editor: null,
            hasChanges: false,
            hasExisting: RW_EDITOR.hasExisting,
            saving: false,
            savingColor: false,
            currentColor: RW_EDITOR.currentWarna,
            message: '',
            messageType: 'success',

            init() {
                this.$nextTick(() => this._bootstrap());
            },

            _bootstrap() {
                // Create editor via engine
                this._editor = new SimPeta.PolygonEditor('rw-polygon-map', {
                    color: this.currentColor,
                }).init();

                // Kelurahan boundary
                this._editor.addKelurahan(RW_EDITOR.kelurahanGeojson);

                // Other RW polygons (reference)
                this._editor.addRwReference(RW_EDITOR.allRwPolygons);

                // Existing polygon
                if (RW_EDITOR.polygonGeojson) {
                    this._editor.loadExisting(RW_EDITOR.polygonGeojson, this.currentColor);
                }

                // Draw events
                this._editor.onSinglePolygonChange((changed) => {
                    this.hasChanges = changed;
                });
            },

            async savePolygon() {
                const geojson = this._editor.getGeometry();
                if (!geojson) {
                    this._flash('Gambar polygon terlebih dahulu.', 'error');
                    return;
                }
                this.saving = true;
                try {
                    const data = await SimPeta.apiPut(RW_EDITOR.routes.update, {
                        geojson,
                        warna: this.currentColor,
                    });
                    if (data.success) {
                        this.hasChanges = false;
                        this.hasExisting = true;
                        this._flash(data.message, 'success');
                    } else {
                        this._flash(data.message || 'Gagal menyimpan.', 'error');
                    }
                } catch (err) {
                    this._flash('Terjadi kesalahan: ' + err.message, 'error');
                } finally {
                    this.saving = false;
                }
            },

            async deletePolygon() {
                if (!confirm('Hapus polygon RW ini?')) return;
                this.saving = true;
                try {
                    const data = await SimPeta.apiDelete(RW_EDITOR.routes.delete);
                    if (data.success) {
                        this._editor.clearDrawn();
                        this.hasChanges = false;
                        this.hasExisting = false;
                        this._flash(data.message, 'success');
                    } else {
                        this._flash(data.message || 'Gagal menghapus.', 'error');
                    }
                } catch (err) {
                    this._flash('Terjadi kesalahan: ' + err.message, 'error');
                } finally {
                    this.saving = false;
                }
            },

            async saveColor() {
                this.savingColor = true;
                try {
                    const data = await SimPeta.apiPut(RW_EDITOR.routes.colorUpdate, {
                        warna: this.currentColor,
                    });
                    if (data.success) {
                        this._flash(data.message, 'success');
                        this._editor.updateColor(this.currentColor);
                    } else {
                        this._flash(data.message || 'Gagal menyimpan warna.', 'error');
                    }
                } catch (err) {
                    this._flash('Terjadi kesalahan: ' + err.message, 'error');
                } finally {
                    this.savingColor = false;
                }
            },

            _flash(msg, type = 'success') {
                this.message = msg;
                this.messageType = type;
                setTimeout(() => { this.message = ''; }, 4000);
            },
        };
    }
    </script>
    @endpush

    @push('styles')
    <style>
    .rw-label {
        background: none !important;
        border: none !important;
        box-shadow: none !important;
        font-weight: 700;
        font-size: 11px;
        color: #1e293b;
        text-shadow: 1px 1px 2px white, -1px -1px 2px white;
        white-space: nowrap;
        pointer-events: none !important;
    }
    </style>
    @endpush

</x-layouts.app>
