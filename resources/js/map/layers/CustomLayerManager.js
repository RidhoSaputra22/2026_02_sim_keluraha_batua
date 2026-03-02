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
        if (!mapLayer || !info || !this.engine?.map) return;

        info.visible = !info.visible;

        if (info.visible) {
            this.engine.map.addLayer(mapLayer);
        } else {
            // IMPORTANT: close tooltips/popups for all child layers
            try {
                mapLayer.eachLayer((l) => {
                    try {
                        this._cleanupChildLayer(l);
                    } catch (e) { }
                });
            } catch (e) { }

            this.engine.map.removeLayer(mapLayer);
        }
    }

    destroy() {
        const map = this.engine?.map || null;

        Object.values(this._mapLayers).forEach((mapLayer) => {
            try {
                mapLayer.eachLayer((l) => this._cleanupChildLayer(l));
            } catch (e) { }

            if (map) {
                try {
                    map.removeLayer(mapLayer);
                } catch (e) { }
            }
        });

        this._mapLayers = {};
        this.layers = [];
    }

    /**
     * Ensure all visible custom layers are rendered above base layers.
     */
    bringToFront() {
        if (!this.engine?.map) return;

        // Iterate in reverse so the first layer (top of sidebar) is brought to front last → on top
        for (let i = this.layers.length - 1; i >= 0; i--) {
            const mapLayer = this._mapLayers[this.layers[i].id];
            if (!mapLayer) continue;

            try {
                mapLayer.bringToFront && mapLayer.bringToFront();
            } catch (e) { }

            try {
                mapLayer.eachLayer((child) => {
                    try {
                        child.bringToFront && child.bringToFront();
                    } catch (e) { }
                });
            } catch (e) { }
        }
    }

    // ── Private ─────────────────────────────────────────────

    /** @private */
    _renderAll(layersData) {
        if (!this.engine?.map) {
            this.layers = [];
            this._mapLayers = {};
            return;
        }

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

        // Render in reverse order so the first layer (top of sidebar) is added last → on top
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
                        {
                            sticky: true,
                            direction: "top",
                            opacity: 0.95,
                        },
                    );

                    if (desc) {
                        layer.bindPopup(`<strong>${nama}</strong><br>${desc}`);
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
                            layer.bringToFront && layer.bringToFront();
                        } catch (e) { }
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

            if (!this.engine?.map) {
                mapLayer.eachLayer((l) => this._cleanupChildLayer(l));
                return;
            }

            mapLayer.addTo(this.engine.map);
            this._mapLayers[layerData.id] = mapLayer;
        }

        this.bringToFront();
    }

    /** @private */
    _cleanupChildLayer(layer) {
        try {
            layer.off && layer.off();
            layer.closeTooltip && layer.closeTooltip();
            layer.unbindTooltip && layer.unbindTooltip();
            layer.closePopup && layer.closePopup();
            layer.unbindPopup && layer.unbindPopup();
        } catch (e) { }
    }


}
