{{-- Leaflet JS (CDN) + SimPeta engine (Vite bundle) --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@vite('resources/js/map/index.js')

@php
// Pass route URLs to JS — keeps Blade directives out of JS function bodies.
$petaRoutes = [
'geojsonRw' => route('peta.geojson.rw'),
'geojsonKelurahan' => route('peta.geojson.kelurahan'),
'geojsonLayers' => route('peta.geojson.layers'),
'stats' => route('peta.stats'),
];
@endphp

<script>
// ── Server-provided routes ───────────────────────────────
const PETA_ROUTES = @json($petaRoutes);

// ── Global helpers (available before SimPeta bundle loads) ──
function formatNumber(num) {
    if (!num && num !== 0) return '-';
    return new Intl.NumberFormat('id-ID').format(num);
}

function sortRwList(list) {
    return [...list].sort((a, b) =>
        parseInt(a.name.replace('RW ', '')) - parseInt(b.name.replace('RW ', ''))
    );
}

// ── Alpine.js component ──────────────────────────────────
function petaApp() {
    return {
        // ─── Reactive state (visible to Alpine templates) ──
        loading: true,
        selectedRw: null,
        selectedStats: {},
        showKelurahan: true,
        showLabels: true,
        showRwLayer: true,
        rwDataList: [],
        rwColors: {},
        customLayers: [],
        globalStats: {
            total_penduduk: 0,
            total_kk: 0,
            total_rw: 0,
            total_rt: 0,
            total_umkm: 0,
            laki_laki: 0,
            perempuan: 0,
        },

        // ─── Engine instances (internal) ───────────────────
        _engine: null,
        _rwLayer: null,
        _kelLayer: null,
        _clm: null,

        // ─── Computed ──────────────────────────────────────
        get sortedRwList() {
            return sortRwList(this.rwDataList);
        },

        // ─── Lifecycle ─────────────────────────────────────
        init() {
            if (this._engine) return;
            this.$nextTick(() => this._bootstrap());
        },

        destroy() {
            if (this._engine) this._engine.destroy();
        },

        // ─── Bootstrap ─────────────────────────────────────
        async _bootstrap() {
            // Wait for the Vite module to register window.SimPeta
            if (typeof SimPeta === 'undefined') {
                await new Promise(resolve => {
                    const check = setInterval(() => {
                        if (typeof SimPeta !== 'undefined') {
                            clearInterval(check);
                            resolve();
                        }
                    }, 20);
                });
            }

            // 1. Core engine
            this._engine = new SimPeta.MapEngine('map', {
                useSvgRenderer: true,
                zoomPosition: 'bottomleft',
            }).init();

            // 2. RW layer with callbacks
            this._rwLayer = new SimPeta.RwLayer(this._engine, {
                onSelect: (name, data) => {
                    this.selectedRw = name;
                    this.selectedStats = data;
                    if (this._kelLayer) this._kelLayer.bringToFront();
                    this.$nextTick(() => {
                        const el = this.$el.querySelector('.rw-list-item.active');
                        if (el) el.scrollIntoView({
                            behavior: 'smooth',
                            block: 'nearest'
                        });
                    });
                },
                onDeselect: () => {
                    this.selectedRw = null;
                    this.selectedStats = {};
                    if (this._kelLayer && this._kelLayer.bounds) {
                        this._engine.flyToBounds(this._kelLayer.bounds);
                    }
                },
                onDataLoad: (list) => {
                    this.rwDataList = list;
                    this.rwColors = Object.assign({}, this._rwLayer.colors);
                },
            });

            // 3. Kelurahan layer
            this._kelLayer = new SimPeta.KelurahanLayer(this._engine);

            // 4. Custom layer manager
            this._clm = new SimPeta.CustomLayerManager(this._engine);

            // 5. Load data in parallel
            this.loading = true;
            try {
                const results = await Promise.all([
                    this._rwLayer.load(PETA_ROUTES.geojsonRw),
                    this._kelLayer.load(PETA_ROUTES.geojsonKelurahan),
                    // this._clm.load(PETA_ROUTES.geojsonLayers),
                    SimPeta.apiGet(PETA_ROUTES.stats),
                ]);

                this.globalStats = results[2];
                this.customLayers = await this._clm.load(PETA_ROUTES.geojsonLayers);

                // Fit to kelurahan bounds & constrain
                if (this._kelLayer.bounds) {
                    this._engine.fitBounds(this._kelLayer.bounds);
                    this._engine.constrainToBounds(this._kelLayer.bounds);
                }

                // Custom layers (after base layers)
            } catch (err) {
                console.error('[petaApp] Load error:', err);
            } finally {
                this.loading = false;
                this.$nextTick(() => this._engine.invalidateSize());
            }
        },

        // ─── Actions (called from Blade templates) ─────────
        selectRw(rwName) {
            if (this._rwLayer) this._rwLayer.select(rwName);
        },

        resetView() {
            if (this._rwLayer) this._rwLayer.deselect();
        },

        resetZoom() {
            if (this._kelLayer && this._kelLayer.bounds) {
                this._engine.flyToBounds(this._kelLayer.bounds);
            }
        },

        toggleKelurahan() {
            this.showKelurahan = !this.showKelurahan;
            if (this._kelLayer) this._kelLayer.toggle(this.showKelurahan);
        },

        toggleLabels() {
            this.showLabels = !this.showLabels;
            if (this._rwLayer) this._rwLayer.toggleLabels(this.showLabels);
        },

        toggleRwLayer() {
            this.showRwLayer = !this.showRwLayer;
            if (this._rwLayer) this._rwLayer.toggle(this.showRwLayer);
            // When hiding RW layer, also hide labels; when showing, restore label state
            if (!this.showRwLayer) {
                this.showLabels = false;
            }
        },

        toggleCustomLayer(layerId) {
            if (this._clm) this._clm.toggle(layerId);
        },
    };
}
</script>