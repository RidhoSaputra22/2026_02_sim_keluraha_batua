/**
 * Core map engine — initialises Leaflet, manages base layers,
 * zoom controls, resize handling, and the shared SVG renderer.
 *
 * All other modules receive a MapEngine instance to work with.
 *
 * @module map/MapEngine
 */

import PatternRenderer from "./renderers/PatternRenderer";

/**
 * @typedef {Object} MapEngineOptions
 * @property {[number,number]} [center=[-5.155, 119.466]]
 * @property {number}          [zoom=15]
 * @property {boolean}         [zoomControl=false]      - Leaflet default zoom control
 * @property {string}          [zoomPosition='bottomleft']
 * @property {boolean}         [useSvgRenderer=true]    - Use shared SVG renderer for patterns
 * @property {boolean}         [baseLayers=true]        - Add OSM + Satellite switcher
 * @property {boolean}         [autoResize=true]        - Observe container resize
 */

export default class MapEngine {
    /**
     * @param {string}          containerId - DOM id of the map container
     * @param {MapEngineOptions} [options]
     */
    constructor(containerId, options = {}) {
        /** @type {string} */
        this.containerId = containerId;

        /** @type {MapEngineOptions} */
        this.options = {
            center: [-5.155, 119.466],
            zoom: 15,
            zoomControl: false,
            zoomPosition: "bottomleft",
            useSvgRenderer: true,
            baseLayers: true,
            autoResize: true,
            ...options,
        };

        /** @type {L.Map|null} */
        this.map = null;

        /** @type {L.SVG|null} */
        this.svgRenderer = null;

        /** @type {PatternRenderer|null} */
        this.patterns = null;

        /** @type {ResizeObserver|null} */
        this._resizeObserver = null;
    }

    // ── Lifecycle ───────────────────────────────────────────

    /**
     * Initialise the Leaflet map. Safe to call multiple times
     * (guards against double-init).
     *
     * @returns {MapEngine} this (for chaining)
     */
    init() {
        const container = document.getElementById(this.containerId);
        if (!container || container._leaflet_id) {
            console.warn(
                `[MapEngine] Container #${this.containerId} already initialised or missing.`,
            );
            return this;
        }

        // Shared SVG renderer — all layers must use this so pattern
        // <defs> live in a single <svg> and url(#id) resolves.
        if (this.options.useSvgRenderer) {
            this.svgRenderer = L.svg({ padding: 0.5 });
        }

        this.map = L.map(this.containerId, {
            center: this.options.center,
            zoom: this.options.zoom,
            zoomControl: this.options.zoomControl,
            scrollWheelZoom: true,
            doubleClickZoom: true,
            touchZoom: true,
            boxZoom: true,
            keyboard: true,
            dragging: true,
            ...(this.svgRenderer ? { renderer: this.svgRenderer } : {}),
        });

        // Custom panes for z-ordering:
        // basePane (z 350) → kelurahan boundary & RW polygons (always below)
        // customLayerPane (z 400) → custom overlay layers (always above base)
        this.map.createPane("basePane");
        this.map.getPane("basePane").style.zIndex = 350;
        this.map.createPane("customLayerPane");
        this.map.getPane("customLayerPane").style.zIndex = 400;

        // Zoom control at the desired position
        if (!this.options.zoomControl) {
            L.control
                .zoom({ position: this.options.zoomPosition })
                .addTo(this.map);
        }

        // Base tile layers + switcher
        if (this.options.baseLayers) {
            this._addBaseLayers();
        }

        // PatternRenderer (needs svgRenderer)
        if (this.svgRenderer) {
            this.patterns = new PatternRenderer(this.svgRenderer);
        }

        // Resize observer
        if (this.options.autoResize) {
            this._observeResize(container);
        }

        return this;
    }

    /**
     * Destroy the map instance and clean up observers.
     */
    destroy() {
        if (this._resizeObserver) {
            this._resizeObserver.disconnect();
            this._resizeObserver = null;
        }
        if (this.map) {
            this.map.remove();
            this.map = null;
        }
    }

    /**
     * Force a map size recalculation.
     */
    invalidateSize() {
        if (this.map) this.map.invalidateSize();
    }

    // ── Bounds helpers ──────────────────────────────────────

    /**
     * Fit map to given bounds.
     *
     * @param {L.LatLngBounds} bounds
     * @param {object} [options]
     */
    fitBounds(bounds, options = { padding: [20, 20] }) {
        if (this.map && bounds) this.map.fitBounds(bounds, options);
    }

    /**
     * Fly to bounds with animation.
     *
     * @param {L.LatLngBounds} bounds
     * @param {object} [options]
     */
    flyToBounds(bounds, options = {}) {
        if (this.map && bounds) {
            this.map.flyToBounds(bounds, {
                padding: [20, 20],
                duration: 0.5,
                ...options,
            });
        }
    }

    /**
     * Constrain map to stay within given bounds (padded) and set zoom limits.
     *
     * @param {L.LatLngBounds} bounds
     * @param {number} [padFraction=0.15]
     * @param {number} [extraZoomIn=3]
     */
    constrainToBounds(bounds, padFraction = 0.15, extraZoomIn = 3) {
        if (!this.map || !bounds) return;
        const currentZoom = this.map.getZoom();
        this.map.setMaxBounds(bounds.pad(padFraction));
        this.map.setMinZoom(currentZoom - 1);
        this.map.setMaxZoom(currentZoom + extraZoomIn);
    }

    // ── Private ─────────────────────────────────────────────

    /** @private */
    _addBaseLayers() {
        const osm = L.tileLayer(
            "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
            {
                attribution:
                    '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>',
                maxZoom: 19,
            },
        );

        const satellite = L.tileLayer(
            "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}",
            {
                attribution: "&copy; Esri",
                maxZoom: 19,
            },
        );

        osm.addTo(this.map);

        L.control
            .layers({ Peta: osm, Satelit: satellite }, null, {
                position: "topright",
            })
            .addTo(this.map);
    }

    /** @private */
    _observeResize(container) {
        if (!window.ResizeObserver) return;
        this._resizeObserver = new ResizeObserver(() => {
            if (this.map) this.map.invalidateSize();
        });
        this._resizeObserver.observe(container);
    }
}
