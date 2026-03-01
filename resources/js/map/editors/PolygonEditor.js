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
        if (typeof L === "undefined") {
            console.warn("[PolygonEditor] Leaflet (L) not loaded yet.");
            return this;
        }

        const container = document.getElementById(this.containerId);
        if (!container) {
            console.warn(
                `[PolygonEditor] Container #${this.containerId} not found.`,
            );
            return this;
        }
        if (container._leaflet_id) {
            console.warn(
                `[PolygonEditor] Container #${this.containerId} already initialised.`,
            );
            return this;
        }

        this.map = L.map(this.containerId, {
            center: [-5.155, 119.466],
            zoom: 15,
            zoomControl: true,
        });

        // Custom panes for z-ordering:
        // basePane (z 350) → RW overlay always at bottom
        // customLayerPane (z 400) → custom display layers in middle
        // editPane (z 450) → actively edited drawn items on top
        this.map.createPane("basePane");
        this.map.getPane("basePane").style.zIndex = 350;
        this.map.createPane("customLayerPane");
        this.map.getPane("customLayerPane").style.zIndex = 400;
        this.map.createPane("editPane");
        this.map.getPane("editPane").style.zIndex = 450;

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

        // Drawn items (use editPane so they render above base + custom layers)
        this.drawnItems = new L.FeatureGroup([], {
            pane: "editPane",
        }).addTo(this.map);

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

        const sanitised = this._sanitiseGeojson(geojson);
        if (!sanitised) return;

        try {
            this.kelurahanLayer = L.geoJSON(sanitised, {
                pane: "basePane",
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
        } catch (e) {
            console.warn(
                "[PolygonEditor] Failed to add kelurahan boundary:",
                e,
            );
        }
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
            const sanitised = this._sanitiseGeojson(rw.geojson);
            if (!sanitised) return;

            try {
                const color = rw.warna || "#6b7280";
                const layer = L.geoJSON(sanitised, {
                    pane: "basePane",
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
            } catch (e) {
                console.warn(
                    `[PolygonEditor] Failed to add RW reference: ${rw.label}`,
                    e,
                );
            }
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
                pane: "basePane",
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
                            pane: "basePane",
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
        if (!geojson || !this.drawnItems || !this.map) return;

        // Sanitise GeoJSON: remove null / incomplete coordinate pairs
        // that MySQL ST_AsGeoJSON occasionally emits for degenerate rings.
        const sanitised = this._sanitiseGeojson(geojson);
        if (!sanitised) return;

        const c = color || this.options.color;
        const existing = L.geoJSON(sanitised, {
            style: { color: c, weight: 3, fillOpacity: 0.35, fillColor: c },
        });
        existing.eachLayer((l) => {
            // Only add layers that actually have valid coordinates
            try {
                if (l.getLatLngs) {
                    // Fix over-nested _latlngs that Leaflet creates from
                    // MultiPolygon (3 levels instead of 2). Leaflet.Draw's
                    // edit handler crashes on the extra nesting.
                    this._flattenLatLngs(l);

                    const lls = l.getLatLngs();
                    if (lls && this._hasValidLatLngs(lls)) {
                        this.drawnItems.addLayer(l);
                    }
                }
            } catch (_) {
                // skip invalid layer
            }
        });
        if (this.drawnItems.getLayers().length > 0) {
            this.map.fitBounds(this.drawnItems.getBounds(), {
                padding: [30, 30],
            });
        }
    }

    /**
     * Deep-sanitise a GeoJSON geometry, removing null / invalid coordinates.
     *
     * @param {object} geojson
     * @returns {object|null}
     */
    _sanitiseGeojson(geojson) {
        if (!geojson || !geojson.type) return null;

        try {
            const clone = JSON.parse(JSON.stringify(geojson));

            // Flatten MultiPolygon → Polygon when there's only one polygon.
            // Leaflet.Draw 1.0.4 cannot edit MultiPolygon geometries because
            // _latlngs gets an extra nesting level that _getMiddleLatLng
            // doesn't expect, causing "can't access property lat of null".
            if (
                clone.type === "MultiPolygon" &&
                clone.coordinates.length === 1
            ) {
                clone.type = "Polygon";
                clone.coordinates = clone.coordinates[0];
            }

            if (clone.type === "Polygon") {
                clone.coordinates = this._sanitiseRings(clone.coordinates);
                if (!clone.coordinates.length) return null;
            } else if (clone.type === "MultiPolygon") {
                // Multiple polygons — sanitise each, keep as MultiPolygon
                clone.coordinates = clone.coordinates
                    .map((poly) => this._sanitiseRings(poly))
                    .filter((poly) => poly.length > 0);
                if (!clone.coordinates.length) return null;
            }
            return clone;
        } catch (_) {
            return null;
        }
    }

    /**
     * Remove null/invalid coords from a polygon's ring array.
     *
     * @param {Array} rings - [[lng, lat], ...][]
     * @returns {Array}
     */
    _sanitiseRings(rings) {
        if (!Array.isArray(rings)) return [];
        return rings
            .map((ring) =>
                Array.isArray(ring)
                    ? ring.filter(
                          (coord) =>
                              Array.isArray(coord) &&
                              coord.length >= 2 &&
                              coord[0] != null &&
                              coord[1] != null &&
                              isFinite(coord[0]) &&
                              isFinite(coord[1]),
                      )
                    : [],
            )
            .filter((ring) => ring.length >= 3);
    }

    /**
     * Recursively check that a LatLngs structure has no null values.
     *
     * @param {*} lls
     * @returns {boolean}
     */
    _hasValidLatLngs(lls) {
        if (!lls) return false;
        if (Array.isArray(lls)) {
            if (lls.length === 0) return false;
            // nested array of rings
            if (Array.isArray(lls[0])) {
                return lls.every((inner) => this._hasValidLatLngs(inner));
            }
            // array of LatLng objects
            return lls.every((ll) => ll && ll.lat != null && ll.lng != null);
        }
        return lls.lat != null && lls.lng != null;
    }

    /**
     * Fix over-nested _latlngs on a Leaflet layer.
     *
     * Leaflet creates _latlngs = [[[LatLng, ...]]] (3 levels) for
     * MultiPolygon, but Leaflet.Draw 1.0.4's edit handler only works
     * with [[LatLng, ...]] (2 levels). This method detects and flattens
     * the extra nesting so editing doesn't crash.
     *
     * @param {L.Layer} layer
     */
    _flattenLatLngs(layer) {
        if (!layer._latlngs || !Array.isArray(layer._latlngs)) return;

        const lls = layer._latlngs;
        // Detect 3-level nesting: [[[LatLng, ...]]] where lls[0][0]
        // is an array of LatLng objects (has .lat).
        if (
            lls.length >= 1 &&
            Array.isArray(lls[0]) &&
            lls[0].length >= 1 &&
            Array.isArray(lls[0][0]) &&
            lls[0][0].length >= 1 &&
            lls[0][0][0] &&
            typeof lls[0][0][0].lat === "number"
        ) {
            // Flatten [[[ring1], [ring2]]] → [[ring1], [ring2]]
            layer._latlngs = lls[0];
            // Re-set bounds
            if (layer._bounds && layer._convertLatLngs) {
                layer._bounds = new L.LatLngBounds();
                layer._latlngs.forEach((ring) =>
                    ring.forEach((ll) => layer._bounds.extend(ll)),
                );
            }
        }
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
            pane: "editPane",
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
        if (!this.map) {
            console.warn(
                "[PolygonEditor] map is null — skipping event binding.",
            );
            return;
        }
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
        if (!this.map) {
            console.warn(
                "[PolygonEditor] map is null — skipping event binding.",
            );
            return;
        }
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
        if (this.map && layer && layer.getBounds) {
            this.map.fitBounds(layer.getBounds(), { padding: [50, 50] });
        }
    }
}
