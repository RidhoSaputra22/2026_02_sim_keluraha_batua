/**
 * Geometry utility functions for polygon boolean operations.
 *
 * Uses Turf.js for computing difference (enclave) and cut on GeoJSON polygons.
 *
 * @module map/utils/GeoUtils
 */

import * as turf from "@turf/turf";

/**
 * Compute the difference between a target polygon and a cutter polygon.
 * The result is the target polygon with the cutter area removed (creating an enclave/hole).
 *
 * @param {object} targetGeojson - GeoJSON geometry (Polygon or MultiPolygon) of the target
 * @param {object} cutterGeojson - GeoJSON geometry (Polygon or MultiPolygon) of the cutter
 * @returns {object|null} Resulting GeoJSON geometry, or null if result is empty/invalid
 */
export function diffPolygon(targetGeojson, cutterGeojson) {
    if (!targetGeojson || !cutterGeojson) return null;

    try {
        const targetFeature = _toFeature(targetGeojson);
        const cutterFeature = _toFeature(cutterGeojson);

        if (!targetFeature || !cutterFeature) return null;

        const result = turf.difference(
            turf.featureCollection([targetFeature, cutterFeature]),
        );

        if (!result || !result.geometry) return null;

        return result.geometry;
    } catch (e) {
        console.warn("[GeoUtils] diffPolygon failed:", e);
        return null;
    }
}

/**
 * Cut a polygon by removing the overlapping portion with a cutter.
 * Semantically identical to diffPolygon but named for clarity in the "cut" workflow.
 *
 * @param {object} targetGeojson - GeoJSON geometry of the target
 * @param {object} cutterGeojson - GeoJSON geometry of the cutter
 * @returns {object|null} Resulting GeoJSON geometry, or null if result is empty/invalid
 */
export function cutPolygon(targetGeojson, cutterGeojson) {
    return diffPolygon(targetGeojson, cutterGeojson);
}

/**
 * Check if two GeoJSON geometries intersect (overlap).
 *
 * @param {object} geomA - GeoJSON geometry (Polygon or MultiPolygon)
 * @param {object} geomB - GeoJSON geometry (Polygon or MultiPolygon)
 * @returns {boolean} True if geometries intersect
 */
export function polygonsIntersect(geomA, geomB) {
    if (!geomA || !geomB) return false;
    try {
        const featureA = _toFeature(geomA);
        const featureB = _toFeature(geomB);
        if (!featureA || !featureB) return false;
        return turf.booleanIntersects(featureA, featureB);
    } catch (_) {
        return false;
    }
}

/**
 * Convert a Leaflet layer to a GeoJSON geometry.
 *
 * @param {L.Layer} layer - Leaflet layer with toGeoJSON() method
 * @returns {object|null} GeoJSON geometry or null
 */
export function layerToGeometry(layer) {
    if (!layer || typeof layer.toGeoJSON !== "function") return null;
    try {
        const geojson = layer.toGeoJSON();
        return geojson?.geometry || null;
    } catch (_) {
        return null;
    }
}

/**
 * Replace a Leaflet layer's geometry with a new GeoJSON geometry,
 * returning the new L.Layer (old one should be removed by caller).
 *
 * @param {object} newGeometry - GeoJSON geometry
 * @param {object} style - Leaflet path style options
 * @param {string} [pane='editPane'] - Leaflet pane
 * @returns {L.Layer|null} New Leaflet layer or null
 */
export function geometryToLayer(newGeometry, style = {}, pane = "editPane") {
    if (!newGeometry || typeof L === "undefined") return null;

    try {
        let resultLayer = null;
        L.geoJSON(
            { type: "Feature", properties: {}, geometry: newGeometry },
            {
                pane,
                style: () => style,
                onEachFeature: (_, lyr) => {
                    resultLayer = lyr;
                },
            },
        );
        return resultLayer;
    } catch (e) {
        console.warn("[GeoUtils] geometryToLayer failed:", e);
        return null;
    }
}

// ── Private helpers ────────────────────────────────────────

/**
 * Ensure a geometry is wrapped as a Feature for Turf.js.
 *
 * @param {object} geojson - GeoJSON geometry or Feature
 * @returns {object|null} Turf Feature
 * @private
 */
function _toFeature(geojson) {
    if (!geojson) return null;

    if (geojson.type === "Feature") {
        return geojson;
    }

    if (
        geojson.type === "Polygon" ||
        geojson.type === "MultiPolygon"
    ) {
        return turf.feature(geojson);
    }

    return null;
}
