window.formatNumber = function formatNumber(num) {
    if (!num && num !== 0) return '-';
    return new Intl.NumberFormat('id-ID').format(num);
};

window.sortRwList = function sortRwList(list) {
    return [...list].sort((a, b) =>
        parseInt(a.name.replace('RW ', '')) - parseInt(b.name.replace('RW ', ''))
    );
};

window.petaApp = function petaApp() {
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
        allLayers: [],
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
        _layers: null,

        // ─── Computed ──────────────────────────────────────
        get sortedRwList() {
            return window.sortRwList(this.rwDataList);
        },

        get customLayers() {
            return this._layers ? this._layers.customLayers : [];
        },

        // ─── Lifecycle ─────────────────────────────────────
        init() {
            if (this._engine) return;
            this.$nextTick(() => this._bootstrap());
        },

        destroy() {
            if (this._layers) this._layers.destroy();
            if (this._engine) this._engine.destroy();
        },

        // ─── Bootstrap ─────────────────────────────────────
        async _bootstrap() {
            // Wait for the Vite module to register window.SimPeta
            if (typeof window.SimPeta === 'undefined') {
                await new Promise(resolve => {
                    const check = setInterval(() => {
                        if (typeof window.SimPeta !== 'undefined') {
                            clearInterval(check);
                            resolve();
                        }
                    }, 20);
                });
            }

            // 1. Core engine
            this._engine = new window.SimPeta.MapEngine('map', {
                useSvgRenderer: true,
                zoomPosition: 'bottomleft',
            }).init();

            // 2. Unified layer manager
            this._layers = new window.SimPeta.LayerManager(this._engine, {
                onRwSelect: (name, data) => {
                    this.selectedRw = name;
                    this.selectedStats = data;
                    if (this._layers) this._layers.bringCustomToFront();
                    this.$nextTick(() => {
                        const el = this.$el.querySelector('.rw-list-item.active');
                        if (el) el.scrollIntoView({
                            behavior: 'smooth',
                            block: 'nearest'
                        });
                    });
                },
                onRwDeselect: () => {
                    this.selectedRw = null;
                    this.selectedStats = {};
                    if (this._layers?.kelurahanBounds) {
                        this._engine.fitBounds(this._layers.kelurahanBounds);
                    }
                },
                onRwDataLoad: (list) => {
                    this.rwDataList = list;
                    this.rwColors = Object.assign({}, this._layers.rwColors);
                },
            });

            // 3. Load ALL layers + stats in parallel
            this.loading = true;
            try {
                // PETA_ROUTES is available on window since it's dynamically rendered by Blade
                const [layersData, statsData] = await Promise.all([
                    window.SimPeta.apiGet(window.PETA_ROUTES.geojsonLayers),
                    window.SimPeta.apiGet(window.PETA_ROUTES.stats),
                ]);

                this.globalStats = statsData;
                this._layers.renderAll(layersData);
                this.allLayers = this._layers.allLayers;

                // Fit to kelurahan bounds & constrain
                if (this._layers.kelurahanBounds) {
                    this._engine.fitBounds(this._layers.kelurahanBounds);
                    this._engine.constrainToBounds(this._layers.kelurahanBounds);
                }

            } catch (err) {
                console.error('[petaApp] Load error:', err);
            } finally {
                this.loading = false;
                this.$nextTick(() => this._engine.invalidateSize());
            }
        },

        // ─── Actions (called from Blade templates) ─────────
        selectRw(rwName) {
            if (this._layers) this._layers.selectRw(rwName);
        },

        resetView() {
            if (this._layers) this._layers.deselectRw();
        },

        resetZoom() {
            if (this._layers?.kelurahanBounds) {
                this._engine.fitBounds(this._layers.kelurahanBounds);
            }
        },

        toggleKelurahan() {
            this.showKelurahan = !this.showKelurahan;
            if (this._layers) this._layers.toggleKelurahan(this.showKelurahan);
        },

        toggleLabels() {
            this.showLabels = !this.showLabels;
            if (this._layers) this._layers.toggleRwLabels(this.showLabels);
        },

        toggleRwLayer() {
            this.showRwLayer = !this.showRwLayer;
            if (this._layers) this._layers.toggleRw(this.showRwLayer);
            if (this.showRwLayer && this._layers) this._layers.bringCustomToFront();
            if (!this.showRwLayer) this.showLabels = false;
        },

        toggleCustomLayer(layerId) {
            if (this._layers) this._layers.toggleCustomLayer(layerId);
        },
    };
};


