/**
 * Custom layer manager — loads additional polygon layers
 * and manages their visibility.
 *
 * @module map/layers/CustomLayerManager
 */

import { apiGet } from "../utils/ApiClient";

export default class CustomLayerManager {
    /**
     * @param {import('../MapEngine').default} engine
     */
    constructor(engine) {
        /** @type {import('../MapEngine').default} */
        this.engine = engine;

        /**
         * Metadata list for Alpine / sidebar rendering.
         * @type {Array<{id:number, nama:string, slug:string, warna:string, visible:boolean, polygonCount:number}>}
         */
        this.layers = [];

        /** @type {Object<number, L.GeoJSON>} id → Leaflet layer */
        this._mapLayers = {};
    }

    /**
     * Fetch all active custom layers and render them.
     *
     * @param {string} url - Route returning JSON array of layer data
     * @returns {Promise<Array>} The metadata list
     */
    async load(url) {
        try {
            const data = await apiGet(url);
            this._renderAll(data);
        } catch (e) {
            console.error("[CustomLayerManager] Load error:", e);
        }
        return this.layers;
    }

    /**
     * Toggle a custom layer on/off.
     *
     * @param {number} layerId
     */
    toggle(layerId) {
        const mapLayer = this._mapLayers[layerId];
        const info = this.layers.find((l) => l.id === layerId);
        if (!mapLayer || !info) return;

        info.visible = !info.visible;
        if (info.visible) {
            this.engine.map.addLayer(mapLayer);
        } else {
            this.engine.map.removeLayer(mapLayer);
        }
    }

    // ── Private ─────────────────────────────────────────────

    /** @private */
    _renderAll(layersData) {
        this.layers = layersData.map((l) => ({
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

        layersData.forEach((layerData) => {
            if (layerData.geojson.features.length === 0) return;

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
                    layer.bindTooltip(nama, { sticky: true });
                    if (desc) {
                        layer.bindPopup(`<strong>${nama}</strong><br>${desc}`);
                    }
                },
            };

            // CRITICAL: share SVG renderer so pattern url(#id) resolves
            if (this.engine.svgRenderer) {
                layerOpts.renderer = this.engine.svgRenderer;
            }

            const mapLayer = L.geoJSON(layerData.geojson, layerOpts);

            // Apply non-solid pattern
            if (layerData.pattern_type !== "solid" && this.engine.patterns) {
                this.engine.patterns.applyCustomLayerPattern(
                    layerData.slug,
                    layerData.pattern_type,
                    layerData.warna,
                    layerData.fill_opacity,
                    mapLayer,
                );
            }

            mapLayer.addTo(this.engine.map);
            this._mapLayers[layerData.id] = mapLayer;
        });
    }
}
