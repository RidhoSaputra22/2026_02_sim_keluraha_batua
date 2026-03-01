/**
 * Kelurahan boundary layer — renders the outer boundary as a dashed line.
 *
 * @module map/layers/KelurahanLayer
 */

import { apiGet } from "../utils/ApiClient";

export default class KelurahanLayer {
    /**
     * @param {import('../MapEngine').default} engine
     */
    constructor(engine) {
        /** @type {import('../MapEngine').default} */
        this.engine = engine;

        /** @type {L.GeoJSON|null} */
        this.layer = null;

        /** @type {L.LatLngBounds|null} */
        this.bounds = null;

        /** @type {boolean} */
        this.visible = true;
    }

    /**
     * Load GeoJSON from url and render the boundary.
     *
     * @param {string} url
     * @returns {Promise<KelurahanLayer>}
     */
    async load(url) {
        const data = await apiGet(url);
        if (data.error) {
            console.warn("[KelurahanLayer] Data not ready:", data.error);
            return this;
        }
        this.render(data);
        return this;
    }

    /**
     * Render from existing GeoJSON data.
     *
     * @param {object} geojson
     */
    render(geojson) {
        const opts = {
            style: {
                color: "#1e293b",
                weight: 3,
                fillOpacity: 0.02,
                fillColor: "#64748b",
                dashArray: "10, 6",
            },
            interactive: false,
        };

        if (this.engine.svgRenderer) {
            opts.renderer = this.engine.svgRenderer;
        }

        this.layer = L.geoJSON(geojson, opts).addTo(this.engine.map);
        this.bounds = this.layer.getBounds();
    }

    /**
     * Toggle visibility on the map.
     *
     * @param {boolean} [show]
     */
    toggle(show) {
        if (show === undefined) show = !this.visible;
        this.visible = show;
        if (!this.layer) return;

        if (show) {
            this.engine.map.addLayer(this.layer);
        } else {
            this.engine.map.removeLayer(this.layer);
        }
    }

    /**
     * Bring boundary to front (useful after RW highlight).
     */
    bringToFront() {
        if (this.layer) this.layer.bringToFront();
    }
}
