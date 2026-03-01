/**
 * Polygon editor — wraps Leaflet.Draw for drawing / editing
 * polygons (used by both RW polygon editor and custom layer editor).
 *
 * @module map/editors/PolygonEditor
 */

import { apiGet, apiPut, apiPost, apiDelete } from "../utils/ApiClient";

/**
 * @typedef {Object} PolygonEditorOptions
 * @property {string}  color       - Draw / existing polygon color
 * @property {number}  [weight=3]
 * @property {number}  [fillOpacity=0.35]
 * @property {number}  [strokeWidth=3]
 * @property {boolean} [rectangle=false]  - Allow rectangle drawing
 */

export default class PolygonEditor {
    /**
     * @param {string} containerId - DOM id for the map
     * @param {PolygonEditorOptions} options
     */
    constructor(containerId, options = {}) {
        this.containerId = containerId;

        this.options = {
            color: "#6366f1",
            weight: 3,
            fillOpacity: 0.35,
            strokeWidth: 3,
            rectangle: false,
            ...options,
        };

        /** @type {L.Map|null} */
        this.map = null;

        /** @type {L.FeatureGroup|null} */
        this.drawnItems = null;

        /** @type {L.Control.Draw|null} */
        this.drawControl = null;

        /** @type {L.GeoJSON|null} RW reference overlay */
        this.rwOverlay = null;

        /** @type {L.LayerGroup|null} */
        this.referenceLayer = null;

        /** @type {L.GeoJSON|null} */
        this.kelurahanLayer = null;

        /** @type {boolean} */
        this.hasChanges = false;
    }

    // ── Init ────────────────────────────────────────────────

    /**
     * Initialise the map and draw controls.
     *
     * @returns {PolygonEditor} this
     */
    init() {
        const container = document.getElementById(this.containerId);
        if (!container || container._leaflet_id) return this;

        this.map = L.map(this.containerId, {
            center: [-5.155, 119.466],
            zoom: 15,
            zoomControl: true,
        });

        // Base layers
        const osm = L.tileLayer(
            "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
            {
                attribution: "&copy; OSM",
                maxZoom: 19,
            },
        );
        const satellite = L.tileLayer(
            "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}",
            { attribution: "&copy; Esri", maxZoom: 19 },
        );
        osm.addTo(this.map);
        L.control
            .layers({ Peta: osm, Satelit: satellite }, null, {
                position: "topright",
            })
            .addTo(this.map);

        // Drawn items
        this.drawnItems = new L.FeatureGroup().addTo(this.map);

        // Draw control
        this.drawControl = new L.Control.Draw({
            position: "topleft",
            draw: {
                polygon: {
                    allowIntersection: false,
                    shapeOptions: {
                        color: this.options.color,
                        weight: this.options.weight,
                        fillOpacity: this.options.fillOpacity,
                    },
                },
                polyline: false,
                circle: false,
                circlemarker: false,
                marker: false,
                rectangle: this.options.rectangle
                    ? {
                          shapeOptions: {
                              color: this.options.color,
                              weight: this.options.strokeWidth,
                              fillOpacity: this.options.fillOpacity,
                          },
                      }
                    : false,
            },
            edit: {
                featureGroup: this.drawnItems,
                remove: true,
            },
        });
        this.map.addControl(this.drawControl);

        return this;
    }

    /**
     * Destroy the map instance.
     */
    destroy() {
        if (this.map) {
            this.map.remove();
            this.map = null;
        }
    }

    // ── Kelurahan boundary ──────────────────────────────────

    /**
     * Add a kelurahan boundary for reference.
     *
     * @param {object} geojson
     */
    addKelurahan(geojson) {
        if (!geojson || !this.map) return;

        this.kelurahanLayer = L.geoJSON(geojson, {
            style: {
                color: "#1e293b",
                weight: 3,
                fillOpacity: 0.02,
                fillColor: "#64748b",
                dashArray: "10, 6",
            },
        }).addTo(this.map);

        this.map.fitBounds(this.kelurahanLayer.getBounds(), {
            padding: [20, 20],
        });
    }

    // ── RW reference overlay ────────────────────────────────

    /**
     * Add other RW polygons as faded reference.
     *
     * @param {Array<{label:string, warna:string, geojson:object}>} polygons
     */
    addRwReference(polygons) {
        if (!this.map) return;

        this.referenceLayer = L.layerGroup().addTo(this.map);

        polygons.forEach((rw) => {
            const color = rw.warna || "#6b7280";
            const layer = L.geoJSON(rw.geojson, {
                style: {
                    color,
                    weight: 1.5,
                    fillOpacity: 0.1,
                    fillColor: color,
                    dashArray: "4, 4",
                },
                interactive: false,
            });
            this.referenceLayer.addLayer(layer);

            const center = layer.getBounds().getCenter();
            this.referenceLayer.addLayer(
                L.marker(center, {
                    icon: L.divIcon({
                        className: "rw-label",
                        html: `<span style="opacity:0.5">${rw.label}</span>`,
                        iconSize: [50, 18],
                        iconAnchor: [25, 9],
                    }),
                    interactive: false,
                }),
            );
        });
    }

    /**
     * Load RW polygons from GeoJSON API url as a reference overlay
     * (used by the custom layer editor).
     *
     * @param {string} url
     */
    async loadRwOverlay(url) {
        if (!this.map) return;

        this.rwOverlay = L.layerGroup().addTo(this.map);

        try {
            const data = await apiGet(url);

            const rwLayer = L.geoJSON(data, {
                style: (feature) => ({
                    color: feature.properties.warna || "#6b7280",
                    weight: 1.5,
                    fillOpacity: 0.08,
                    fillColor: feature.properties.warna || "#6b7280",
                    dashArray: "4, 4",
                }),
                filter: (f) => f.properties && f.properties.RW,
                onEachFeature: (feature, layer) => {
                    if (!feature.properties.RW) return;
                    const center = layer.getBounds().getCenter();
                    this.rwOverlay.addLayer(
                        L.marker(center, {
                            icon: L.divIcon({
                                className: "rw-label-ref",
                                html: `<span>${feature.properties.RW}</span>`,
                                iconSize: [50, 18],
                                iconAnchor: [25, 9],
                            }),
                            interactive: false,
                        }),
                    );
                },
            });
            this.rwOverlay.addLayer(rwLayer);

            // Fit to RW bounds if no drawn items
            if (this.drawnItems && this.drawnItems.getLayers().length === 0) {
                this.map.fitBounds(rwLayer.getBounds(), { padding: [20, 20] });
            }
        } catch (e) {
            console.error("[PolygonEditor] Failed to load RW overlay:", e);
        }
    }

    /**
     * Toggle RW overlay visibility.
     *
     * @param {boolean} show
     */
    toggleRwOverlay(show) {
        if (!this.rwOverlay || !this.map) return;
        if (show) {
            this.map.addLayer(this.rwOverlay);
        } else {
            this.map.removeLayer(this.rwOverlay);
        }
    }

    // ── Existing polygon loading ────────────────────────────

    /**
     * Load a single existing polygon into the drawable layer
     * (for RW polygon editor — single polygon mode).
     *
     * @param {object} geojson
     * @param {string} [color] - Override color
     */
    loadExisting(geojson, color) {
        if (!geojson || !this.drawnItems) return;

        const c = color || this.options.color;
        const existing = L.geoJSON(geojson, {
            style: { color: c, weight: 3, fillOpacity: 0.35, fillColor: c },
        });
        existing.eachLayer((l) => this.drawnItems.addLayer(l));
        this.map.fitBounds(this.drawnItems.getBounds(), { padding: [30, 30] });
    }

    /**
     * Load multiple existing polygons into the drawable layer
     * (for custom layer editor — multi polygon mode).
     * Returns metadata about each polygon.
     *
     * @param {object} geojsonCollection - GeoJSON FeatureCollection
     * @returns {Array<{id: number, nama: string, layer: L.Layer}>}
     */
    loadExistingCollection(geojsonCollection) {
        const list = [];
        if (!geojsonCollection?.features?.length) return list;

        L.geoJSON(geojsonCollection, {
            style: {
                color: this.options.color,
                weight: this.options.strokeWidth,
                fillOpacity: this.options.fillOpacity,
                fillColor: this.options.color,
            },
            onEachFeature: (feature, layer) => {
                this.drawnItems.addLayer(layer);
                list.push({
                    id: feature.properties.id,
                    nama: feature.properties.nama,
                    layer,
                });
            },
        });

        if (list.length > 0) {
            this.map.fitBounds(this.drawnItems.getBounds(), {
                padding: [30, 30],
            });
        }

        return list;
    }

    // ── Event binding ───────────────────────────────────────

    /**
     * Single-polygon mode events (RW editor):
     * - Created → clear + add
     * - Edited / Deleted → flag changes
     *
     * @param {(hasChanges: boolean) => void} onChange
     */
    onSinglePolygonChange(onChange) {
        this.map.on(L.Draw.Event.CREATED, (e) => {
            this.drawnItems.clearLayers();
            this.drawnItems.addLayer(e.layer);
            this.hasChanges = true;
            onChange(true);
        });
        this.map.on(L.Draw.Event.EDITED, () => {
            this.hasChanges = true;
            onChange(true);
        });
        this.map.on(L.Draw.Event.DELETED, () => {
            this.hasChanges = true;
            onChange(true);
        });
    }

    /**
     * Multi-polygon mode events (layer editor):
     * - Created → save to server
     * - Edited → update geometries
     * - Deleted → delete from server
     *
     * @param {object} handlers
     * @param {(layer: L.Layer) => void} handlers.onCreated
     * @param {(layer: L.Layer) => void} handlers.onEdited
     * @param {(layer: L.Layer) => void} handlers.onDeleted
     */
    onMultiPolygonChange(handlers) {
        this.map.on(L.Draw.Event.CREATED, (e) => {
            this.drawnItems.addLayer(e.layer);
            if (handlers.onCreated) handlers.onCreated(e.layer);
        });
        this.map.on(L.Draw.Event.EDITED, (e) => {
            e.layers.eachLayer((layer) => {
                if (handlers.onEdited) handlers.onEdited(layer);
            });
        });
        this.map.on(L.Draw.Event.DELETED, (e) => {
            e.layers.eachLayer((layer) => {
                if (handlers.onDeleted) handlers.onDeleted(layer);
            });
        });
    }

    // ── Getters ─────────────────────────────────────────────

    /**
     * Get GeoJSON geometry of the first drawn layer.
     * @returns {object|null}
     */
    getGeometry() {
        const layers = this.drawnItems.getLayers();
        if (layers.length === 0) return null;
        return layers[0].toGeoJSON().geometry;
    }

    /**
     * Get all drawn layers.
     * @returns {Array<L.Layer>}
     */
    getLayers() {
        return this.drawnItems ? this.drawnItems.getLayers() : [];
    }

    /**
     * Clear all drawn items.
     */
    clearDrawn() {
        if (this.drawnItems) this.drawnItems.clearLayers();
    }

    /**
     * Update the color of all drawn items.
     *
     * @param {string} color
     */
    updateColor(color) {
        this.options.color = color;
        if (this.drawnItems) {
            this.drawnItems.eachLayer((l) => {
                l.setStyle({ color, fillColor: color });
            });
        }
    }

    /**
     * Zoom to a specific layer's bounds.
     *
     * @param {L.Layer} layer
     */
    zoomToLayer(layer) {
        if (layer && layer.getBounds) {
            this.map.fitBounds(layer.getBounds(), { padding: [50, 50] });
        }
    }
}
