/**
 * RW polygon layer — renders RW areas with colors, labels,
 * hatch patterns, hover/click interactions, and selection.
 *
 * @module map/layers/RwLayer
 */

import { apiGet } from "../utils/ApiClient";

/**
 * @typedef {Object} RwLayerCallbacks
 * @property {(rwName: string, rwData: object) => void} [onSelect]
 * @property {(rwDataList: Array) => void}               [onDataLoad]
 * @property {() => void}                                [onDeselect]
 */

export default class RwLayer {
    /**
     * @param {import('../MapEngine').default} engine
     * @param {RwLayerCallbacks} [callbacks]
     */
    constructor(engine, callbacks = {}) {
        /** @type {import('../MapEngine').default} */
        this.engine = engine;

        /** @type {RwLayerCallbacks} */
        this.callbacks = callbacks;

        /** @type {L.GeoJSON|null} */
        this.layer = null;

        /** @type {L.LayerGroup|null} */
        this.labelLayer = null;

        /** @type {Object<string, string>} RW name → color */
        this.colors = {};

        /** @type {Object<string, L.Layer>} RW name → Leaflet layer */
        this.layerMap = {};

        /** @type {Array<object>} Flat list of RW data for sidebar */
        this.dataList = [];

        /** @type {string|null} Currently selected RW name */
        this.selectedRw = null;

        /** @type {L.Layer|null} Currently highlighted layer */
        this._highlightedLayer = null;

        /** @type {boolean} */
        this.labelsVisible = true;

        /** @type {boolean} */
        this.visible = true;
    }

    // ── Data loading ────────────────────────────────────────

    /**
     * Fetch GeoJSON from url and render.
     *
     * @param {string} url
     * @returns {Promise<RwLayer>}
     */
    async load(url) {
        const data = await apiGet(url);
        if (data.error) {
            console.warn("[RwLayer] Data not ready:", data.error);
            return this;
        }
        this.render(data);
        return this;
    }

    // ── Rendering ───────────────────────────────────────────

    /**
     * Render RW polygons on the map.
     *
     * @param {object} geojson - GeoJSON FeatureCollection
     */
    render(geojson) {
        const self = this;
        this.labelLayer = L.layerGroup().addTo(this.engine.map);

        // Build color map from GeoJSON properties
        if (geojson.features) {
            geojson.features.forEach((f) => {
                if (f.properties?.RW && f.properties?.warna) {
                    self.colors[f.properties.RW] = f.properties.warna;
                }
            });
        }

        const layerOpts = {
            pane: "basePane",
            style: (feature) => {
                const rw = feature.properties.RW;
                const color =
                    feature.properties.warna || self.colors[rw] || "#6b7280";
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
                this._onEachFeature(feature, layer),
        };

        if (this.engine.svgRenderer) {
            layerOpts.renderer = this.engine.svgRenderer;
        }

        this.layer = L.geoJSON(geojson, layerOpts).addTo(this.engine.map);

        // Apply hatch patterns
        if (this.engine.patterns) {
            this.engine.patterns.applyRwPatterns(this.colors, this.layerMap);
        }

        // Notify consumer
        if (this.callbacks.onDataLoad) {
            this.callbacks.onDataLoad(this.dataList);
        }
    }

    // ── Interaction ─────────────────────────────────────────

    /**
     * Programmatically select an RW by name.
     *
     * @param {string} rwName - e.g. "RW 01"
     */
    select(rwName) {
        if (this.selectedRw === rwName) {
            this.deselect();
            return;
        }

        // Restore previous
        if (this._highlightedLayer) {
            this._restoreStyle(this._highlightedLayer);
        }

        const layer = this.layerMap[rwName];
        if (!layer) return;

        this._highlightedLayer = layer;
        this.selectedRw = rwName;
        this._highlight(layer);

        // Fly to bounds
        this.engine.flyToBounds(layer.getBounds(), {
            padding: [60, 60],
            maxZoom: (this.engine.map.getZoom() || 15) + 2,
        });

        // Notify
        const rwData = this.dataList.find((r) => r.name === rwName);
        if (this.callbacks.onSelect) {
            this.callbacks.onSelect(rwName, rwData ? { ...rwData } : {});
        }
    }

    /**
     * Clear current selection and reset view.
     */
    deselect() {
        if (this._highlightedLayer) {
            this._restoreStyle(this._highlightedLayer);
            this._highlightedLayer = null;
        }
        this.selectedRw = null;

        if (this.callbacks.onDeselect) {
            this.callbacks.onDeselect();
        }
    }

    // ── Layer toggling ──────────────────────────────────

    /**
     * Toggle the entire RW polygon layer (and labels) on/off.
     *
     * @param {boolean} [show]
     */
    toggle(show) {
        if (show === undefined) show = !this.visible;
        this.visible = show;

        if (!this.layer) return;

        if (show) {
            this.engine.map.addLayer(this.layer);
            if (this.labelsVisible && this.labelLayer) {
                this.engine.map.addLayer(this.labelLayer);
            }
        } else {
            this.engine.map.removeLayer(this.layer);
            if (this.labelLayer) {
                this.engine.map.removeLayer(this.labelLayer);
            }
        }
    }

    // ── Label toggling ──────────────────────────────────────

    /**
     * Toggle RW labels on/off.
     *
     * @param {boolean} [show]
     */
    toggleLabels(show) {
        if (show === undefined) show = !this.labelsVisible;
        this.labelsVisible = show;
        if (!this.labelLayer) return;

        if (show) {
            this.engine.map.addLayer(this.labelLayer);
        } else {
            this.engine.map.removeLayer(this.labelLayer);
        }
    }

    // ── Private ─────────────────────────────────────────────

    /** @private */
    _onEachFeature(feature, layer) {
        const props = feature.properties;
        if (!props.RW) return;

        this.layerMap[props.RW] = layer;

        this.dataList.push({
            name: props.RW,
            warna: props.warna || "#6b7280",
            total_penduduk: props.total_penduduk || 0,
            total_kk: props.total_kk || 0,
            total_rt: props.total_rt || 0,
            total_umkm: props.total_umkm || 0,
            laki_laki: props.laki_laki || 0,
            perempuan: props.perempuan || 0,
        });

        // Tooltip
        layer.bindTooltip(props.RW, {
            permanent: false,
            direction: "center",
            className: "rw-label",
            sticky: true,
        });

        // Permanent label
        const center = layer.getBounds().getCenter();
        const label = L.marker(center, {
            pane: "basePane",
            icon: L.divIcon({
                className: "rw-label",
                html: `<span>${props.RW}</span>`,
                iconSize: [50, 18],
                iconAnchor: [25, 9],
            }),
            interactive: false,
        });
        this.labelLayer.addLayer(label);

        // Hover
        layer.on("mouseover", () => {
            if (this._highlightedLayer === layer) return;
            this._highlight(layer);
        });
        layer.on("mouseout", () => {
            if (this._highlightedLayer === layer) return;
            this._restoreStyle(layer);
        });

        // Click → select
        layer.on("click", () => this.select(props.RW));
    }

    /**
     * Highlight a layer (solid fill, thicker border).
     * @private
     */
    _highlight(layer) {
        const rw = layer.feature?.properties?.RW;
        if (!rw) return;
        const color = this.colors[rw] || "#6b7280";
        layer.setStyle({
            weight: 4,
            fillColor: color,
            fillOpacity: 0.5,
            opacity: 1,
        });
        layer.bringToFront();
    }

    /**
     * Restore a layer to its base hatch style.
     * @private
     */
    _restoreStyle(layer) {
        const rw = layer.feature?.properties?.RW;
        if (!rw) return;
        const color = this.colors[rw] || "#6b7280";
        layer.setStyle({ weight: 2.5, opacity: 0.9, color });

        // Re-apply hatch pattern
        if (this.engine.patterns) {
            const patternId = "hatch-" + rw.replace(/\s/g, "-");
            this.engine.patterns.applyToLayer(layer, patternId);
        }
    }
}
