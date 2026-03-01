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

        {{-- Toolbar --}}
        <x-ui.card class="mb-4" :compact="true">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">

                {{-- RW Selector --}}
                <div class="md:col-span-3">
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-semibold">Pilih RW</span>
                        </label>
                        <select class="select select-bordered select-sm w-full"
                            @change="window.location.href = $event.target.value">
                            @foreach($rwList as $r)
                            <option value="{{ route('peta.rw-polygon.edit', $r) }}"
                                {{ $r->id === $rw->id ? 'selected' : '' }}>
                                RW {{ str_pad($r->nomor, 2, '0', STR_PAD_LEFT) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Color Picker --}}
                <div class="md:col-span-4">
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-semibold">Warna Polygon</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <input type="color" x-model="currentColor"
                                class="w-9 h-9 rounded-lg cursor-pointer border-2 border-base-300 p-0.5 hover:border-primary transition-colors"
                                title="Pilih warna polygon">
                            <span class="text-xs font-mono bg-base-200 px-2 py-1 rounded" x-text="currentColor"></span>
                            <x-ui.button type="ghost" size="sm" :outline="true" :isSubmit="false" @click="saveColor()"
                                x-bind:disabled="savingColor">
                                <span x-show="!savingColor">Simpan Warna</span>
                                <span x-show="savingColor" class="loading loading-spinner loading-xs"></span>
                            </x-ui.button>
                        </div>
                    </div>
                </div>

                {{-- Status + Actions --}}
                <div class="md:col-span-5">
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-semibold">Status & Aksi</span>
                        </label>
                        <div class="flex items-center gap-2 flex-wrap">
                            {{-- Status Badge --}}
                            <template x-if="hasExisting && !hasChanges">
                                <x-ui.badge type="success" size="sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Tersimpan
                                </x-ui.badge>
                            </template>
                            <template x-if="hasChanges">
                                <x-ui.badge type="warning" size="sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01" />
                                    </svg>
                                    Belum disimpan
                                </x-ui.badge>
                            </template>
                            <template x-if="!hasExisting && !hasChanges">
                                <x-ui.badge type="ghost" size="sm">Belum ada polygon</x-ui.badge>
                            </template>

                            <div class="flex-1"></div>

                            {{-- Action Buttons --}}
                            <x-ui.button type="primary" size="sm" :isSubmit="false" @click="savePolygon()"
                                x-bind:disabled="!hasChanges || saving">
                                <template x-if="!saving">
                                    <span class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        Simpan
                                    </span>
                                </template>
                                <span x-show="saving" class="loading loading-spinner loading-xs"></span>
                            </x-ui.button>
                            <x-ui.button type="error" size="sm" :outline="true" :isSubmit="false"
                                @click="deletePolygon()" x-bind:disabled="!hasExisting || saving">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus
                            </x-ui.button>
                        </div>
                    </div>
                </div>
            </div>
        </x-ui.card>

        {{-- Map --}}
        <x-ui.card>
            <x-ui.alert type="info" class="mb-3 py-2 text-sm">
                Gunakan toolbar di kiri atas peta untuk menggambar polygon. Klik titik-titik batas wilayah, lalu klik
                titik pertama untuk menutup polygon.
            </x-ui.alert>
            <x-ui.leaflet-draw id="rw-polygon-map" height="550px" color="#6366f1" />
        </x-ui.card>

        {{-- Toast notification --}}
        <div x-show="message" x-transition.opacity.duration.300ms class="toast toast-top toast-end z-[9999]">
            <div class="alert shadow-lg" :class="messageType === 'success' ? 'alert-success' : 'alert-error'">
                <svg x-show="messageType === 'success'" xmlns="http://www.w3.org/2000/svg"
                    class="stroke-current shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <svg x-show="messageType === 'error'" xmlns="http://www.w3.org/2000/svg"
                    class="stroke-current shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
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
    $rwId = $rw->id;
    $rwWarna = $rw->warna ?? '#6366f1';
    @endphp
    {{-- Leaflet & Leaflet.Draw already loaded by <x-ui.leaflet-draw> component --}}
    @vite('resources/js/map/index.js')

    <script>
    const RW_EDITOR = {
        currentRwId: @json($rwId),
        currentWarna: @json($rwWarna),
        hasExisting: @json((bool) $polygonGeojson),
        kelurahanGeojson: @json($kelurahanGeojson ? json_decode($kelurahanGeojson) : null),
        polygonGeojson: @json($polygonGeojson ? json_decode($polygonGeojson) : null),
        allRwPolygons: @json($rwPolygonsForJs),
        routes: {
            update: @json(route('peta.rw-polygon.update', $rw)),
            delete: @json(route('peta.rw-polygon.delete', $rw)),
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
