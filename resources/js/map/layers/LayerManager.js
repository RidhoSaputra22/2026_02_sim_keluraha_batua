/**
 * Unified layer manager — loads ALL layers from a single endpoint
 * and renders them on appropriate panes with z-ordering.
 *
 * Layer types:
 *   - kelurahan  → boundary line (kelurahanPane, z 340)
 *   - rw         → RW polygons with interaction (rwPane, z 350)
 *   - custom     → overlay facility layers (customLayerPane, z 400)
 *
 * @module map/layers/LayerManager
 */

import { apiGet } from "../utils/ApiClient";
import { formatNumber } from "../utils/helpers";

/**
 * @typedef {Object} LayerManagerCallbacks
 * @property {(rwName: string, rwData: object) => void} [onRwSelect]
 * @property {(rwDataList: Array) => void}               [onRwDataLoad]
 * @property {() => void}                                [onRwDeselect]
 * @property {(layers: Array) => void}                   [onLayersReady]
 */

export default class LayerManager {
    /**
     * @param {import('../MapEngine').default} engine
     * @param {LayerManagerCallbacks} [callbacks]
     */
    constructor(engine, callbacks = {}) {
        /** @type {import('../MapEngine').default} */
        this.engine = engine;

        /** @type {LayerManagerCallbacks} */
        this.callbacks = callbacks;

        // ── Kelurahan state ──────────────────────────────
        /** @type {L.GeoJSON|null} */
        this.kelurahanLayer = null;

        /** @type {L.LatLngBounds|null} */
        this.kelurahanBounds = null;

        /** @type {boolean} */
        this.showKelurahan = true;

        // ── RW state ────────────────────────────────────
        /** @type {L.GeoJSON|null} */
        this.rwLayer = null;

        /** @type {L.LayerGroup|null} */
        this.rwLabelLayer = null;

        /** @type {Object<string, string>} RW name → color */
        this.rwColors = {};

        /** @type {Object<string, L.Layer>} RW name → Leaflet layer */
        this.rwLayerMap = {};

        /** @type {Array<object>} Flat list of RW data for sidebar */
        this.rwDataList = [];

        /** @type {string|null} Currently selected RW name */
        this.selectedRw = null;

        /** @type {L.Layer|null} */
        this._highlightedRw = null;

        /** @type {boolean} */
        this.showRwLabels = true;

        /** @type {boolean} */
        this.showRw = true;

        // ── Custom layers state ─────────────────────────
        /**
         * Metadata list for sidebar rendering.
         * @type {Array<{id:number, nama:string, slug:string, warna:string, visible:boolean, polygonCount:number}>}
         */
        this.customLayers = [];

        /** @type {Object<number, L.GeoJSON>} id → Leaflet layer */
        this._customMapLayers = {};

        /**
         * All layers metadata (kelurahan + rw + custom).
         * @type {Array}
         */
        this.allLayers = [];
    }

    // ═══════════════════════════════════════════════════════
    // Data loading
    // ═══════════════════════════════════════════════════════

    /**
     * Load ALL layers from a single endpoint and render them.
     *
     * @param {string} url - Route returning JSON array of layer data
     * @returns {Promise<LayerManager>}
     */
    async load(url) {
        try {
            const data = await apiGet(url);
            this.renderAll(data);
        } catch (e) {
            console.error("[LayerManager] Load error:", e);
        }
        return this;
    }

    /**
     * Render pre-fetched layer data (all types).
     *
     * @param {Array} layersData - Array of layer objects with geojson & layer_type
     */
    renderAll(layersData) {
        if (!this.engine?.map) return;

        this.allLayers = layersData.map((l) => ({
            ...l,
            visible: true,
            polygonCount: l.geojson?.features?.length ?? 0,
        }));

        // Render in sort_order (kelurahan first, then rw, then custom)
        const kelData = layersData.find((l) => l.layer_type === "kelurahan");
        const rwData = layersData.find((l) => l.layer_type === "rw");
        const customData = layersData.filter(
            (l) => l.layer_type !== "kelurahan" && l.layer_type !== "rw",
        );

        if (kelData?.geojson?.features?.length > 0) {
            this._renderKelurahan(kelData);
        }

        if (rwData?.geojson?.features?.length > 0) {
            this._renderRw(rwData);
        }

        if (customData.length > 0) {
            this._renderCustomLayers(customData);
        }

        if (this.callbacks.onLayersReady) {
            this.callbacks.onLayersReady(this.allLayers);
        }
    }

    // ═══════════════════════════════════════════════════════
    // RW interaction
    // ═══════════════════════════════════════════════════════

    /**
     * Programmatically select an RW by name.
     *
     * @param {string} rwName - e.g. "RW 01"
     */
    selectRw(rwName) {
        if (this.selectedRw === rwName) {
            this.deselectRw();
            return;
        }

        if (this._highlightedRw) {
            this._restoreRwStyle(this._highlightedRw);
        }

        const layer = this.rwLayerMap[rwName];
        if (!layer) return;

        this._highlightedRw = layer;
        this.selectedRw = rwName;

        try {
            this.engine?.map?.closeTooltip?.();
        } catch (_) {}
        try {
            layer.closeTooltip?.();
        } catch (_) {}

        this._highlightRw(layer);

        if (this.engine?.map) {
            const boundsOpts = {
                padding: [60, 60],
                maxZoom: (this.engine.map.getZoom() || 15) + 2,
            };
            if (typeof this.engine.fitBounds === "function") {
                this.engine.fitBounds(layer.getBounds(), boundsOpts);
            } else if (typeof this.engine.map.fitBounds === "function") {
                this.engine.map.fitBounds(layer.getBounds(), boundsOpts);
            }
        }

        const rwData = this.rwDataList.find((r) => r.name === rwName);
        if (this.callbacks.onRwSelect) {
            this.callbacks.onRwSelect(rwName, rwData ? { ...rwData } : {});
        }
    }

    /**
     * Clear current RW selection and reset view.
     */
    deselectRw() {
        if (this._highlightedRw) {
            this._restoreRwStyle(this._highlightedRw);
            this._highlightedRw = null;
        }
        this.selectedRw = null;

        if (this.callbacks.onRwDeselect) {
            this.callbacks.onRwDeselect();
        }
    }

    // ═══════════════════════════════════════════════════════
    // Toggle methods
    // ═══════════════════════════════════════════════════════

    /**
     * Toggle kelurahan boundary visibility.
     *
     * @param {boolean} [show]
     */
    toggleKelurahan(show) {
        if (show === undefined) show = !this.showKelurahan;
        this.showKelurahan = show;
        if (!this.kelurahanLayer) return;

        if (show) {
            this.engine.map.addLayer(this.kelurahanLayer);
        } else {
            this.engine.map.removeLayer(this.kelurahanLayer);
        }
    }

    /**
     * Toggle the entire RW polygon layer (and labels) on/off.
     *
     * @param {boolean} [show]
     */
    toggleRw(show) {
        if (show === undefined) show = !this.showRw;
        this.showRw = show;
        if (!this.rwLayer) return;

        if (show) {
            this.engine.map.addLayer(this.rwLayer);
            if (this.showRwLabels && this.rwLabelLayer) {
                this.engine.map.addLayer(this.rwLabelLayer);
            }
        } else {
            this.engine.map.removeLayer(this.rwLayer);
            if (this.rwLabelLayer) {
                this.engine.map.removeLayer(this.rwLabelLayer);
            }
        }
    }

    /**
     * Toggle RW labels on/off.
     *
     * @param {boolean} [show]
     */
    toggleRwLabels(show) {
        if (show === undefined) show = !this.showRwLabels;
        this.showRwLabels = show;
        if (!this.rwLabelLayer) return;

        if (show) {
            this.engine.map.addLayer(this.rwLabelLayer);
        } else {
            this.engine.map.removeLayer(this.rwLabelLayer);
        }
    }

    /**
     * Toggle a custom layer on/off.
     *
     * @param {number} layerId
     */
    toggleCustomLayer(layerId) {
        const mapLayer = this._customMapLayers[layerId];
        const info = this.customLayers.find((l) => l.id === layerId);
        if (!mapLayer || !info || !this.engine?.map) return;

        info.visible = !info.visible;

        // Also update allLayers
        const allInfo = this.allLayers.find((l) => l.id === layerId);
        if (allInfo) allInfo.visible = info.visible;

        if (info.visible) {
            this.engine.map.addLayer(mapLayer);
        } else {
            try {
                mapLayer.eachLayer((l) => this._cleanupChildLayer(l));
            } catch (_) {}
            this.engine.map.removeLayer(mapLayer);
        }
    }

    /**
     * Bring custom layers to front (useful after RW highlight).
     */
    bringCustomToFront() {
        if (!this.engine?.map) return;
        for (let i = this.customLayers.length - 1; i >= 0; i--) {
            const mapLayer = this._customMapLayers[this.customLayers[i].id];
            if (!mapLayer) continue;
            try {
                mapLayer.bringToFront?.();
                mapLayer.eachLayer?.((child) => {
                    try {
                        child.bringToFront?.();
                    } catch (_) {}
                });
            } catch (_) {}
        }
    }

    /**
     * Bring kelurahan boundary to front.
     */
    bringKelurahanToFront() {
        if (this.kelurahanLayer) this.kelurahanLayer.bringToFront();
    }

    /**
     * Destroy all layers and clean up.
     */
    destroy() {
        const map = this.engine?.map || null;

        // Custom
        Object.values(this._customMapLayers).forEach((mapLayer) => {
            try {
                mapLayer.eachLayer((l) => this._cleanupChildLayer(l));
            } catch (_) {}
            if (map) {
                try {
                    map.removeLayer(mapLayer);
                } catch (_) {}
            }
        });
        this._customMapLayers = {};
        this.customLayers = [];

        // RW
        if (this.rwLayer && map) {
            try {
                map.removeLayer(this.rwLayer);
            } catch (_) {}
        }
        if (this.rwLabelLayer && map) {
            try {
                map.removeLayer(this.rwLabelLayer);
            } catch (_) {}
        }
        this.rwLayer = null;
        this.rwLabelLayer = null;
        this.rwLayerMap = {};
        this.rwDataList = [];
        this.rwColors = {};

        // Kelurahan
        if (this.kelurahanLayer && map) {
            try {
                map.removeLayer(this.kelurahanLayer);
            } catch (_) {}
        }
        this.kelurahanLayer = null;
        this.kelurahanBounds = null;
        this.allLayers = [];
    }

    // ═══════════════════════════════════════════════════════
    // Private: Kelurahan rendering
    // ═══════════════════════════════════════════════════════

    /** @private */
    _renderKelurahan(layerData) {
        const opts = {
            pane: "kelurahanPane",
            style: {
                color: layerData.warna || "#1e293b",
                weight: layerData.stroke_width || 3,
                fillOpacity: layerData.fill_opacity ?? 0.02,
                fillColor: "#64748b",
                dashArray: "10, 6",
            },
            interactive: false,
        };

        if (this.engine.svgRenderer) {
            opts.renderer = this.engine.svgRenderer;
        }

        this.kelurahanLayer = L.geoJSON(layerData.geojson, opts).addTo(
            this.engine.map,
        );
        this.kelurahanBounds = this.kelurahanLayer.getBounds();
    }

    // ═══════════════════════════════════════════════════════
    // Private: RW rendering
    // ═══════════════════════════════════════════════════════

    /** @private */
    _renderRw(layerData) {
        this.rwLabelLayer = L.layerGroup().addTo(this.engine.map);

        // Build color map
        if (layerData.geojson.features) {
            layerData.geojson.features.forEach((f) => {
                if (f.properties?.RW && f.properties?.warna) {
                    this.rwColors[f.properties.RW] = f.properties.warna;
                }
            });
        }

        const layerOpts = {
            pane: "rwPane",
            style: (feature) => {
                const rw = feature.properties.RW;
                const color =
                    feature.properties.warna ||
                    this.rwColors[rw] ||
                    "#6b7280";
                return {
                    color,
                    weight: 2.5,
                    opacity: 0.9,
                    fillOpacity: 0.3,
                    fillColor: color,
                };
            },
            filter: (feature) => feature.properties?.RW != null,
            onEachFeature: (feature, layer) =>
                this._onEachRwFeature(feature, layer),
        };

        if (this.engine.svgRenderer) {
            layerOpts.renderer = this.engine.svgRenderer;
        }

        this.rwLayer = L.geoJSON(layerData.geojson, layerOpts).addTo(
            this.engine.map,
        );

        // Apply hatch patterns
        if (this.engine.patterns) {
            this.engine.patterns.applyRwPatterns(
                this.rwColors,
                this.rwLayerMap,
            );
        }

        if (this.callbacks.onRwDataLoad) {
            this.callbacks.onRwDataLoad(this.rwDataList);
        }
    }

    /** @private */
    _onEachRwFeature(feature, layer) {
        const props = feature.properties;
        const nama = props.RW || "RW tak dikenal";
        const desc = props.deskripsi || "";
        if (!props.RW) return;

        this.rwLayerMap[props.RW] = layer;

        const stats = [
            props.total_penduduk != null
                ? `Penduduk: <strong>${formatNumber(props.total_penduduk)}</strong> jiwa`
                : null,
            props.total_kk != null
                ? `KK: <strong>${formatNumber(props.total_kk)}</strong>`
                : null,
            props.total_umkm != null
                ? `UMKM: <strong>${formatNumber(props.total_umkm)}</strong>`
                : null,
        ].filter(Boolean);

        const tooltipContent =
            `<strong>${nama}</strong>` +
            (desc ? `<br>${desc}` : "") +
            (stats.length
                ? `<hr style="margin:4px 0;border-color:rgba(0,0,0,.15)">` +
                  `<div style="line-height:1.5">${stats.join("<br>")}</div>`
                : "");

        layer.bindTooltip(tooltipContent, {
            sticky: true,
            direction: "top",
            opacity: 0.95,
        });

        const popupContent =
            `<div class="text-sm">` +
            `<strong class="text-base">${nama}</strong>` +
            `<hr class="my-1 border-base-300">` +
            `<div class="space-y-1">` +
            (props.total_penduduk != null
                ? `<div>Penduduk: <strong>${formatNumber(props.total_penduduk)}</strong> jiwa</div>`
                : "") +
            (props.total_kk != null
                ? `<div>KK: <strong>${formatNumber(props.total_kk)}</strong></div>`
                : "") +
            (props.total_umkm != null
                ? `<div>UMKM: <strong>${formatNumber(props.total_umkm)}</strong></div>`
                : "") +
            (props.laki_laki != null && props.perempuan != null
                ? `<div>L/P: <strong>${formatNumber(props.laki_laki)}</strong> / <strong>${formatNumber(props.perempuan)}</strong></div>`
                : "") +
            (props.total_rt != null
                ? `<div>Jumlah RT: <strong>${formatNumber(props.total_rt)}</strong></div>`
                : "") +
            `</div></div>`;

        layer.bindPopup(popupContent);

        this.rwDataList.push({
            name: props.RW,
            warna: props.warna || "#6b7280",
            total_penduduk: props.total_penduduk || 0,
            total_kk: props.total_kk || 0,
            total_rt: props.total_rt || 0,
            total_umkm: props.total_umkm || 0,
            laki_laki: props.laki_laki || 0,
            perempuan: props.perempuan || 0,
        });

        // Permanent label
        const center = layer.getBounds().getCenter();
        const label = L.marker(center, {
            pane: "rwPane",
            icon: L.divIcon({
                className: "rw-label",
                html: `<span>${props.RW}</span>`,
                iconSize: [50, 18],
                iconAnchor: [25, 9],
            }),
            interactive: false,
        });
        this.rwLabelLayer.addLayer(label);

        // Hover
        layer.on("mouseover", () => {
            if (this._highlightedRw === layer) return;
            this._highlightRw(layer);
        });
        layer.on("mouseout", () => {
            if (this._highlightedRw === layer) return;
            this._restoreRwStyle(layer);
        });

        // Click → select
        layer.on("click", () => this.selectRw(props.RW));
    }

    /** @private */
    _highlightRw(layer) {
        const rw = layer.feature?.properties?.RW;
        if (!rw) return;
        const color = this.rwColors[rw] || "#6b7280";
        layer.setStyle({
            weight: 4,
            fillColor: color,
            fillOpacity: 0.5,
            opacity: 1,
        });
        layer.bringToFront();
    }

    /** @private */
    _restoreRwStyle(layer) {
        const rw = layer.feature?.properties?.RW;
        if (!rw) return;
        const color = this.rwColors[rw] || "#6b7280";
        layer.setStyle({ weight: 2.5, opacity: 0.9, color });

        if (this.engine.patterns) {
            const patternId = "hatch-" + rw.replace(/\s/g, "-");
            this.engine.patterns.applyToLayer(layer, patternId);
        }

        layer.bringToBack();
    }

    // ═══════════════════════════════════════════════════════
    // Private: Custom layers rendering
    // ═══════════════════════════════════════════════════════

    /** @private */
    _renderCustomLayers(layersData) {
        if (!this.engine?.map) {
            this.customLayers = [];
            this._customMapLayers = {};
            return;
        }

        this.customLayers = layersData.map((l) => ({
            id: l.id,
            nama: l.nama,
            slug: l.slug,
            warna: l.warna,
            fill_opacity: l.fill_opacity,
            stroke_width: l.stroke_width,
            pattern_type: l.pattern_type,
            visible: true,
            polygonCount: l.geojson.features.length,
        }));

        // Render in reverse order so first layer ends up on top
        for (let i = layersData.length - 1; i >= 0; i--) {
            const layerData = layersData[i];
            if (layerData.geojson.features.length === 0) continue;

            const layerOpts = {
                pane: "customLayerPane",
                style: {
                    color: layerData.warna,
                    weight: layerData.stroke_width,
                    fillOpacity: layerData.fill_opacity,
                    fillColor: layerData.warna,
                },
                onEachFeature: (feature, layer) => {
                    const nama = feature.properties.nama || layerData.nama;
                    const desc = feature.properties.deskripsi || "";

                    layer.bindTooltip(
                        `<strong>${nama}</strong>${desc ? `<br>${desc}` : ""}`,
                        { sticky: true, direction: "top", opacity: 0.95 },
                    );

                    if (desc) {
                        layer.bindPopup(
                            `<strong>${nama}</strong><br>${desc}`,
                        );
                    }

                    // Hover highlight
                    layer.on("mouseover", () => {
                        layer.setStyle({
                            color: layerData.warna,
                            weight: Number(layerData.stroke_width || 2) + 1,
                            fillColor: layerData.warna,
                            fillOpacity: Math.min(
                                Number(layerData.fill_opacity || 0.3) + 0.18,
                                0.75,
                            ),
                            opacity: 1,
                        });
                        if (layer._path) {
                            layer._path.style.fill = layerData.warna;
                        }
                        try {
                            layer.bringToFront?.();
                        } catch (_) {}
                    });

                    layer.on("mouseout", () => {
                        layer.setStyle({
                            color: layerData.warna,
                            weight: layerData.stroke_width,
                            fillColor: layerData.warna,
                            fillOpacity: layerData.fill_opacity,
                            opacity: 0.9,
                        });
                        if (
                            layerData.pattern_type !== "solid" &&
                            this.engine.patterns
                        ) {
                            this.engine.patterns.applyToLayer(
                                layer,
                                "custom-" + layerData.slug,
                            );
                        }
                    });
                },
            };

            if (this.engine.svgRenderer) {
                layerOpts.renderer = this.engine.svgRenderer;
            }

            const mapLayer = L.geoJSON(layerData.geojson, layerOpts);

            if (layerData.pattern_type !== "solid" && this.engine.patterns) {
                this.engine.patterns.applyCustomLayerPattern(
                    layerData.slug,
                    layerData.pattern_type,
                    layerData.warna,
                    layerData.fill_opacity,
                    mapLayer,
                );
            }

            if (!this.engine?.map) {
                mapLayer.eachLayer((l) => this._cleanupChildLayer(l));
                return;
            }

            mapLayer.addTo(this.engine.map);
            this._customMapLayers[layerData.id] = mapLayer;
        }

        this.bringCustomToFront();
    }

    /** @private */
    _cleanupChildLayer(layer) {
        try {
            layer.off?.();
            layer.closeTooltip?.();
            layer.unbindTooltip?.();
            layer.closePopup?.();
            layer.unbindPopup?.();
        } catch (_) {}
    }
}
