/**
 * Polygon editor — wraps Leaflet.Draw for drawing / editing
 * polygons (used by both RW polygon editor and custom layer editor).
 *
 * @module map/editors/PolygonEditor
 */

import { apiGet, apiPut, apiPost, apiDelete } from "../utils/ApiClient";
import { diffPolygon, layerToGeometry, geometryToLayer, polygonsIntersect } from "../utils/GeoUtils";

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

        /** @type {L.LayerGroup|null} */
        this.referenceLayer = null;

        /** @type {L.GeoJSON|null} */
        this.kelurahanLayer = null;

        /** @type {boolean} */
        this.hasChanges = false;

        /** @type {Record<number|string, L.GeoJSON>} */
        this.displayLayers = {};

        /** @type {boolean} */
        this._tooltipsSuspended = false;

        /** @type {Array<L.Layer>} */
        this._stashedLayers = [];

        /** @type {L.FeatureGroup|null} */
        this._stashedDisplayGroup = null;

        /** @type {'diff'|'cut'|null} */
        this._specialMode = null;

        /** @type {L.Layer|null} */
        this._specialModeTarget = null;

        /** @type {Function|null} */
        this._specialModeCallback = null;
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

        this._patchLeafletTooltipGuards();

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
            // Keep transitions deterministic while layer/edit states change rapidly.
            zoomAnimation: false,
            fadeAnimation: false,
            markerZoomAnimation: false,
        });

        // Custom panes for z-ordering:
        // kelurahanPane (z 340) → kelurahan boundary at bottom
        // rwPane (z 350) → RW overlay above kelurahan
        // customLayerPane (z 400) → custom display layers in middle
        // editPane (z 450) → actively edited drawn items on top
        this.map.createPane("kelurahanPane");
        this.map.getPane("kelurahanPane").style.zIndex = 340;
        this.map.createPane("rwPane");
        this.map.getPane("rwPane").style.zIndex = 350;
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
     * Guard Leaflet tooltip internals from null map references during
     * rapid layer/control switching.
     */
    _patchLeafletTooltipGuards() {
        if (typeof L === "undefined" || !L.Tooltip || !L.Tooltip.prototype) {
            return;
        }

        const proto = L.Tooltip.prototype;
        if (proto.__simKelurahanPatchedTooltipGuards) return;

        const originalAnimateZoom = proto._animateZoom;
        const originalUpdatePosition = proto._updatePosition;

        if (typeof originalAnimateZoom === "function") {
            proto._animateZoom = function (e) {
                if (!this._map) return;
                return originalAnimateZoom.call(this, e);
            };
        }

        if (typeof originalUpdatePosition === "function") {
            proto._updatePosition = function () {
                if (!this._map) return;
                return originalUpdatePosition.call(this);
            };
        }

        proto.__simKelurahanPatchedTooltipGuards = true;
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
                pane: "kelurahanPane",
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
                    pane: "rwPane",
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
     * Load existing GeoJSON collection into the editor.
     *
     * @param {object} geojsonCollection
     * @param {function} [onPolygonClick] - Callback when an existing polygon is clicked
     * @returns {Array<{id:number|string, nama:string, layer:L.Layer}>}
     */
    loadExistingCollection(geojsonCollection, onPolygonClick = null) {
        const list = [];
        if (!geojsonCollection || !geojsonCollection.features) return list;

        const sanitisedCollection = {
            ...geojsonCollection,
            features: geojsonCollection.features
                .map((feature) => {
                    const sanitisedGeometry = this._sanitiseGeojson(
                        feature?.geometry,
                    );
                    if (!sanitisedGeometry) return null;

                    return {
                        ...feature,
                        geometry: sanitisedGeometry,
                    };
                })
                .filter(Boolean),
        };

        if (!sanitisedCollection.features.length) return list;

        L.geoJSON(sanitisedCollection, {
            pane: "editPane",
            style: (feature) => {
                const c = feature?.properties?.warna || this.options.color;
                return {
                    color: c,
                    weight: this.options.strokeWidth,
                    fillOpacity: this.options.fillOpacity,
                    fillColor: c,
                };
            },
            onEachFeature: (feature, layer) => {
                try {
                    if (!layer.getLatLngs) return;

                    this._flattenLatLngs(layer);

                    const lls = layer.getLatLngs();
                    if (!lls || !this._hasValidLatLngs(lls)) return;

                    if (onPolygonClick) {
                        layer.on("click", (e) => {
                            if (e.originalEvent) e.originalEvent.stopPropagation();
                            L.DomEvent.stopPropagation(e);
                            onPolygonClick(feature?.properties?.id, layer);
                        });
                    }

                    this.drawnItems.addLayer(layer);
                    list.push({
                        id: feature?.properties?.id,
                        nama: feature?.properties?.nama,
                        layer,
                    });
                } catch (_) {
                    // Skip invalid feature silently
                }
            },
        });

        if (list.length > 0) {
            this.map.fitBounds(this.drawnItems.getBounds(), {
                padding: [30, 30],
            });
        }

        return list;
    }

    // ── Layer Editor Helpers ──────────────────────────────

    /**
     * Store a read-only display layer by id.
     *
     * @param {number|string} layerId
     * @param {L.GeoJSON} mapLayer
     */
    storeDisplayLayer(layerId, mapLayer) {
        this.displayLayers[layerId] = mapLayer;
    }

    /**
     * Get a read-only display layer.
     *
     * @param {number|string} layerId
     * @returns {L.GeoJSON|null}
     */
    getDisplayLayer(layerId) {
        return this.displayLayers[layerId] || null;
    }

    /**
     * Remove a read-only display layer safely.
     *
     * @param {number|string} layerId
     */
    removeDisplayLayer(layerId) {
        const ml = this.displayLayers[layerId];
        if (ml && this.map) {
            if (typeof ml.eachLayer === "function") {
                ml.eachLayer((child) => {
                    if (child && typeof child.closeTooltip === "function")
                        child.closeTooltip();
                    if (child && typeof child.unbindTooltip === "function")
                        child.unbindTooltip();
                });
            }
            this.map.removeLayer(ml);
        }
        delete this.displayLayers[layerId];
    }

    /**
     * Temporarily unbind tooltips on all read-only display layers.
     */
    suspendDisplayTooltips() {
        if (this._tooltipsSuspended) return;

        Object.values(this.displayLayers).forEach((group) => {
            if (!group || typeof group.eachLayer !== "function") return;
            group.eachLayer((child) => {
                if (!child) return;
                const tooltip =
                    typeof child.getTooltip === "function"
                        ? child.getTooltip()
                        : null;
                if (tooltip && typeof tooltip.getContent === "function") {
                    child._simTooltipText = tooltip.getContent();
                }
                if (typeof child.closeTooltip === "function") child.closeTooltip();
                if (typeof child.unbindTooltip === "function")
                    child.unbindTooltip();
            });
        });

        this._tooltipsSuspended = true;
    }

    /**
     * Restore previously suspended tooltips.
     */
    restoreDisplayTooltips() {
        if (!this._tooltipsSuspended) return;

        Object.values(this.displayLayers).forEach((group) => {
            if (!group || typeof group.eachLayer !== "function") return;
            group.eachLayer((child) => {
                if (!child) return;
                if (typeof child.getTooltip === "function" && child.getTooltip())
                    return;
                if (!child._simTooltipText) return;
                if (typeof child.bindTooltip === "function") {
                    child.bindTooltip(child._simTooltipText, { sticky: true });
                }
            });
        });

        this._tooltipsSuspended = false;
    }

    /**
     * Disable all active Leaflet.Draw handlers.
     */
    disableDrawModes() {
        if (!this.drawControl) return;

        const toolbars = this.drawControl._toolbars || {};
        Object.values(toolbars).forEach((toolbar) => {
            if (!toolbar || !toolbar._modes) return;
            Object.values(toolbar._modes).forEach((mode) => {
                const handler = mode?.handler;
                if (!handler || typeof handler.disable !== "function") return;
                if (typeof handler.enabled === "function") {
                    if (handler.enabled()) handler.disable();
                } else {
                    handler.disable();
                }
            });
        });
    }

    /**
     * Rebuild draw control based on selected layer style.
     *
     * @param {{warna:string, stroke_width:number, fill_opacity:number}} layer
     */
    replaceDrawControl(layer) {
        if (!this.map || !layer) return;

        this.removeDrawControl();

        this.drawControl = new L.Control.Draw({
            position: "topleft",
            draw: {
                polygon: {
                    allowIntersection: false,
                    shapeOptions: {
                        color: layer.warna,
                        weight: layer.stroke_width,
                        fillOpacity: layer.fill_opacity,
                    },
                },
                polyline: false,
                circle: false,
                circlemarker: false,
                marker: false,
                rectangle: {
                    shapeOptions: {
                        color: layer.warna,
                        weight: layer.stroke_width,
                        fillOpacity: layer.fill_opacity,
                    },
                },
            },
            edit: {
                featureGroup: this.drawnItems,
                remove: true,
            },
        });

        this.map.addControl(this.drawControl);
    }

    /**
     * Remove current draw control safely.
     */
    removeDrawControl() {
        if (!this.map || !this.drawControl) return;
        this.disableDrawModes();
        this.map.removeControl(this.drawControl);
        this.drawControl = null;
    }

    /**
     * Start editing only one polygon while showing others as read-only references.
     *
     * @param {L.Layer} targetLayer
     * @param {{warna:string, stroke_width:number, fill_opacity:number}} layerStyle
     * @param {((originalLayer: L.Layer) => void)|null} [onStashedClick] - Called when a stashed polygon is clicked
     */
    startSinglePolygonEdit(targetLayer, layerStyle, onStashedClick = null) {
        if (!this.map || !this.drawnItems || !targetLayer) return;

        this.suspendDisplayTooltips();

        this._stashedLayers = [];
        this.drawnItems.eachLayer((layer) => {
            if (layer !== targetLayer) this._stashedLayers.push(layer);
        });
        this._stashedLayers.forEach((layer) => this.drawnItems.removeLayer(layer));

        this._stashedDisplayGroup = L.featureGroup([], {
            pane: "customLayerPane",
        }).addTo(this.map);

        this._stashedLayers.forEach((layer) => {
            const geojsonFeature = layer.toGeoJSON();
            const c = geojsonFeature?.properties?.warna || layerStyle.warna;
            const isInteractive = typeof onStashedClick === "function";
            const cloned = L.geoJSON(geojsonFeature, {
                pane: "customLayerPane",
                style: {
                    color: c,
                    weight: layerStyle.stroke_width,
                    fillOpacity: layerStyle.fill_opacity * 0.5,
                    fillColor: c,
                    dashArray: "4, 4",
                },
                interactive: isInteractive,
                onEachFeature: isInteractive
                    ? (feature, lyr) => {
                          lyr.on("click", (e) => {
                              L.DomEvent.stopPropagation(e);
                              onStashedClick(layer);
                          });
                      }
                    : undefined,
            });
            this._stashedDisplayGroup.addLayer(cloned);
        });

        this.replaceDrawControl(layerStyle);
        this.fitBoundsNoAnim(targetLayer.getBounds(), [80, 80]);
    }

    /**
     * Stop single polygon edit and restore normal edit mode.
     *
     * @param {{warna:string, stroke_width:number, fill_opacity:number}|null} layerStyle
     * @param {boolean} [restoreLayers=true]
     */
    stopSinglePolygonEdit(layerStyle = null, restoreLayers = true) {
        this.disableDrawModes();

        if (this._stashedDisplayGroup && this.map) {
            this.map.removeLayer(this._stashedDisplayGroup);
            this._stashedDisplayGroup = null;
        }

        if (restoreLayers && this.drawnItems && this._stashedLayers?.length) {
            this._stashedLayers.forEach((layer) => this.drawnItems.addLayer(layer));
        }
        this._stashedLayers = [];

        if (layerStyle) {
            this.replaceDrawControl(layerStyle);
        }

        this.restoreDisplayTooltips();
    }

    /**
     * Fit bounds without animation.
     *
     * @param {L.LatLngBounds} bounds
     * @param {[number, number]} [padding=[50,50]]
     */
    fitBoundsNoAnim(bounds, padding = [50, 50]) {
        if (!this.map || !bounds) return;
        this.map.stop();
        this.map.fitBounds(bounds, {
            padding,
            animate: false,
        });
    }

    /**
     * Set map view without animation.
     *
     * @param {[number, number]} center
     * @param {number} zoom
     */
    setViewNoAnim(center, zoom) {
        if (!this.map) return;
        this.map.stop();
        this.map.setView(center, zoom, { animate: false });
    }

    /**
     * Render a read-only display layer and register it internally.
     *
     * @param {{id:number|string,nama:string,warna:string,stroke_width:number,fill_opacity:number}} layer
     * @param {object} geojson
     * @returns {L.GeoJSON|null}
     */
    renderDisplayLayer(layer, geojson, onPolygonClick = null) {
        if (!this.map || !layer || !geojson?.features?.length) return null;

        const mapLayer = L.geoJSON(geojson, {
            pane: "customLayerPane",
            style: (feature) => ({
                color: feature?.properties?.warna || layer.warna,
                weight: layer.stroke_width,
                fillOpacity: layer.fill_opacity,
                fillColor: feature?.properties?.warna || layer.warna,
            }),
            onEachFeature: (feature, lyr) => {
                const nama = feature?.properties?.nama || layer.nama;
                lyr._simTooltipText = nama;
                lyr.bindTooltip(nama, { sticky: true });
                if (onPolygonClick) {
                    lyr.on("click", (e) => {
                        L.DomEvent.stopPropagation(e);
                        onPolygonClick(feature?.properties?.id, layer.id);
                    });
                }
            },
        });

        mapLayer.addTo(this.map);
        this.storeDisplayLayer(layer.id, mapLayer);
        return mapLayer;
    }

    /**
     * Build UI polygon list from a GeoJSON FeatureCollection.
     *
     * @param {object} geojson
     * @returns {Array<{id:number|string,nama:string,_featureIndex:number}>}
     */
    extractPolygonList(geojson) {
        if (!geojson?.features?.length) return [];
        return geojson.features.map((feature, idx) => ({
            id: feature?.properties?.id,
            nama: feature?.properties?.nama || `Polygon ${idx + 1}`,
            warna: feature?.properties?.warna || null,
            _featureIndex: idx,
        }));
    }

    /**
     * Convert editor polygon list into GeoJSON FeatureCollection.
     *
     * @param {Array<{id:number|string,nama:string,layer:L.Layer}>} polygonList
     * @returns {{type:'FeatureCollection',features:Array}}
     */
    toFeatureCollection(polygonList) {
        const features = [];
        (polygonList || []).forEach((poly) => {
            if (!poly?.layer) return;
            features.push({
                type: "Feature",
                properties: {
                    id: poly.id,
                    nama: poly.nama,
                    warna: poly.warna || null,
                },
                geometry: poly.layer.toGeoJSON().geometry,
            });
        });

        return {
            type: "FeatureCollection",
            features,
        };
    }

    // ── API helpers (Layer Polygon CRUD) ─────────────────

    /**
     * Create polygon under a layer.
     *
     * @param {string} polygonBaseUrl
     * @param {number|string} layerId
     * @param {object} geometry
     * @param {string} nama
     */
    async createLayerPolygon(polygonBaseUrl, layerId, geometry, nama) {
        return apiPost(`${polygonBaseUrl}/${layerId}/polygon`, {
            geojson: geometry,
            nama,
        });
    }

    /**
     * Update polygon geometry or metadata.
     *
     * @param {string} polygonBaseUrl
     * @param {number|string} layerId
     * @param {number|string} polygonId
     * @param {object} payload
     */
    async updateLayerPolygon(polygonBaseUrl, layerId, polygonId, payload) {
        return apiPut(`${polygonBaseUrl}/${layerId}/polygon/${polygonId}`, payload);
    }

    /**
     * Delete polygon by id.
     *
     * @param {string} polygonBaseUrl
     * @param {number|string} layerId
     * @param {number|string} polygonId
     */
    async deleteLayerPolygon(polygonBaseUrl, layerId, polygonId) {
        return apiDelete(`${polygonBaseUrl}/${layerId}/polygon/${polygonId}`);
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
        if (!this.drawnItems) return null;
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
            this.fitBoundsNoAnim(layer.getBounds(), [50, 50]);
        }
    }

    /**
     * Reorder polygons within a layer on the server.
     *
     * @param {string} polygonBaseUrl
     * @param {number|string} layerId
     * @param {Array<number>} order - Array of polygon IDs in desired order
     */
    async reorderPolygons(polygonBaseUrl, layerId, order) {
        return apiPost(`${polygonBaseUrl}/${layerId}/polygon-reorder`, { order });
    }

    // ── Special Modes (Global Diff / Enclave) ─────────────────

    /**
     * Get the current special mode.
     * @returns {'diff'|null}
     */
    getSpecialMode() {
        return this._specialMode;
    }

    /**
     * Enter global diff mode (enclave creation).
     * Draw a polygon that will be subtracted from ALL overlapping sub-polygons.
     *
     * @param {Array<{id:number, layer:L.Layer}>} polygonEntries - All polygon entries in the active layer
     * @param {{warna:string, stroke_width:number, fill_opacity:number}} layerStyle
     * @param {(results: Array<{id:number, resultGeom:object|null, newLayer:L.Layer|null, oldLayer:L.Layer}>) => void} onComplete
     */
    startGlobalDiffMode(polygonEntries, layerStyle, onComplete) {
        if (!this.map || !polygonEntries || polygonEntries.length === 0) return;

        // Stop any existing special mode
        this.stopSpecialMode();

        this._specialMode = 'diff';
        this._specialModeTargets = polygonEntries; // all polygon entries
        this._specialModeCallback = onComplete;

        // Highlight all polygons with dashed amber style
        polygonEntries.forEach(entry => {
            if (entry.layer && entry.layer.setStyle) {
                entry.layer.setStyle({
                    color: '#f59e0b',
                    weight: 3,
                    fillOpacity: 0.2,
                    dashArray: '8, 4',
                });
            }
        });

        // Listen for new polygon creation
        this._specialModeDrawHandler = (e) => {
            this._handleGlobalDiffDraw(e.layer, layerStyle);
        };
        this.map.on(L.Draw.Event.CREATED, this._specialModeDrawHandler);
    }

    /**
     * Exit diff mode and restore normal editing.
     */
    stopSpecialMode() {
        if (!this._specialMode) return;

        // Remove the special mode draw event listener
        if (this.map && this._specialModeDrawHandler) {
            this.map.off(L.Draw.Event.CREATED, this._specialModeDrawHandler);
            this._specialModeDrawHandler = null;
        }

        this._specialMode = null;
        this._specialModeTargets = null;
        this._specialModeCallback = null;
    }

    /**
     * Handle a polygon drawn in global diff mode.
     * Diffs the cutter against ALL polygons and returns results for each affected one.
     *
     * @param {L.Layer} cutterLayer - The drawn polygon used as cutter
     * @param {object} layerStyle - The active layer's style config
     * @private
     */
    _handleGlobalDiffDraw(cutterLayer, layerStyle) {
        if (!this._specialMode || !this._specialModeTargets) return;

        const cutterGeom = layerToGeometry(cutterLayer);
        if (!cutterGeom) {
            console.warn('[PolygonEditor] Invalid cutter geometry.');
            return;
        }

        const results = [];

        for (const entry of this._specialModeTargets) {
            if (!entry.layer) continue;

            const targetGeom = layerToGeometry(entry.layer);
            if (!targetGeom) continue;

            // Skip if the target polygon does not intersect with the cutter
            if (!polygonsIntersect(targetGeom, cutterGeom)) {
                continue;
            }

            const resultGeom = diffPolygon(targetGeom, cutterGeom);

            // If null → polygon was fully consumed
            if (!resultGeom) {
                results.push({
                    id: entry.id,
                    resultGeom: null,
                    newLayer: null,
                    oldLayer: entry.layer,
                });
                continue;
            }

            // Get the polygon's own style
            const polyColor = entry.layer.options?.color || layerStyle.warna;
            const targetStyle = {
                color: polyColor,
                weight: entry.layer.options?.weight || layerStyle.stroke_width || this.options.weight,
                fillOpacity: entry.layer.options?.fillOpacity || layerStyle.fill_opacity || this.options.fillOpacity,
                fillColor: entry.layer.options?.fillColor || polyColor,
            };

            // Create new layer from result geometry
            const newLayer = geometryToLayer(resultGeom, targetStyle, 'editPane');
            if (!newLayer) continue;

            // Copy feature properties
            const origFeature = entry.layer.feature;
            if (origFeature) {
                newLayer.feature = { ...origFeature, geometry: resultGeom };
            }

            // Replace the target layer in drawnItems
            if (this.drawnItems) {
                this.drawnItems.removeLayer(entry.layer);
                this.drawnItems.addLayer(newLayer);
            }

            results.push({
                id: entry.id,
                resultGeom,
                newLayer,
                oldLayer: entry.layer,
            });
        }

        // Update targets for next draw operation
        this._specialModeTargets = this._specialModeTargets.map(entry => {
            const result = results.find(r => r.id === entry.id && r.newLayer);
            return result ? { ...entry, layer: result.newLayer } : entry;
        }).filter(entry => {
            // Remove fully consumed polygons
            const consumed = results.find(r => r.id === entry.id && r.resultGeom === null);
            return !consumed;
        });

        // Notify callback with results
        if (this._specialModeCallback && results.length > 0) {
            this._specialModeCallback(results);
        } else if (results.length === 0) {
            console.info('[PolygonEditor] Enclave did not overlap any polygon.');
        }
    }
}
