/**
 * Vitest global setup — mock Leaflet (L) and DOM essentials.
 */

import { vi } from "vitest";

// ── Leaflet Mock ────────────────────────────────────────────────

function createMockLayer(opts = {}) {
    const layer = {
        _path: document.createElementNS("http://www.w3.org/2000/svg", "path"),
        _latlngs: [],
        feature: opts.feature || null,
        options: opts,
        addTo: vi.fn().mockReturnThis(),
        remove: vi.fn().mockReturnThis(),
        setStyle: vi.fn().mockReturnThis(),
        bringToFront: vi.fn().mockReturnThis(),
        bringToBack: vi.fn().mockReturnThis(),
        bindTooltip: vi.fn().mockReturnThis(),
        bindPopup: vi.fn().mockReturnThis(),
        on: vi.fn().mockReturnThis(),
        off: vi.fn().mockReturnThis(),
        getLatLngs: vi.fn().mockReturnValue([
            [
                { lat: -5.15, lng: 119.46 },
                { lat: -5.16, lng: 119.47 },
                { lat: -5.17, lng: 119.46 },
            ],
        ]),
        toGeoJSON: vi.fn().mockReturnValue({
            type: "Feature",
            geometry: {
                type: "Polygon",
                coordinates: [
                    [
                        [119.46, -5.15],
                        [119.47, -5.16],
                        [119.46, -5.17],
                        [119.46, -5.15],
                    ],
                ],
            },
        }),
        getBounds: vi.fn().mockReturnValue({
            getCenter: () => ({ lat: -5.155, lng: 119.466 }),
            extend: vi.fn(),
            pad: vi.fn().mockReturnValue({
                getCenter: () => ({ lat: -5.155, lng: 119.466 }),
            }),
            isValid: () => true,
        }),
        eachLayer: vi.fn((cb) => {}),
    };
    return layer;
}

function createMockFeatureGroup(layers = [], opts = {}) {
    const storedLayers = [...layers];
    const group = {
        ...createMockLayer(opts),
        _layers: {},
        addLayer: vi.fn((l) => {
            storedLayers.push(l);
        }),
        removeLayer: vi.fn((l) => {
            const idx = storedLayers.indexOf(l);
            if (idx > -1) storedLayers.splice(idx, 1);
        }),
        clearLayers: vi.fn(() => {
            storedLayers.length = 0;
        }),
        getLayers: vi.fn(() => [...storedLayers]),
        eachLayer: vi.fn((cb) => {
            storedLayers.forEach(cb);
        }),
    };
    return group;
}

function createMockMap() {
    const panes = {};
    const addedLayers = new Set();

    return {
        _leaflet_id: null,
        setView: vi.fn().mockReturnThis(),
        setZoom: vi.fn().mockReturnThis(),
        getZoom: vi.fn().mockReturnValue(15),
        setMaxBounds: vi.fn().mockReturnThis(),
        setMinZoom: vi.fn().mockReturnThis(),
        setMaxZoom: vi.fn().mockReturnThis(),
        fitBounds: vi.fn().mockReturnThis(),
        flyToBounds: vi.fn().mockReturnThis(),
        invalidateSize: vi.fn().mockReturnThis(),
        remove: vi.fn(),
        on: vi.fn().mockReturnThis(),
        off: vi.fn().mockReturnThis(),
        addLayer: vi.fn((l) => addedLayers.add(l)),
        removeLayer: vi.fn((l) => addedLayers.delete(l)),
        hasLayer: vi.fn((l) => addedLayers.has(l)),
        addControl: vi.fn().mockReturnThis(),
        createPane: vi.fn((name) => {
            const pane = document.createElement("div");
            pane.style.zIndex = 0;
            panes[name] = pane;
            return pane;
        }),
        getPane: vi.fn((name) => {
            if (!panes[name]) {
                panes[name] = document.createElement("div");
                panes[name].style.zIndex = 0;
            }
            return panes[name];
        }),
    };
}

const L = {
    map: vi.fn(() => createMockMap()),
    tileLayer: vi.fn(() => createMockLayer()),
    geoJSON: vi.fn((data, opts) => {
        const layer = createMockLayer();
        // Simulate onEachFeature for features
        if (data?.features && opts?.onEachFeature) {
            data.features.forEach((f) => {
                if (opts.filter && !opts.filter(f)) return;
                const childLayer = createMockLayer({ feature: f });
                childLayer.feature = f;
                opts.onEachFeature(f, childLayer);
            });
        }
        layer.eachLayer = vi.fn((cb) => {
            if (data?.features) {
                data.features.forEach((f) => {
                    if (opts?.filter && !opts.filter(f)) return;
                    const childLayer = createMockLayer({ feature: f });
                    childLayer.feature = f;
                    cb(childLayer);
                });
            }
        });
        return layer;
    }),
    layerGroup: vi.fn(() => {
        const layers = [];
        return {
            addTo: vi.fn().mockReturnThis(),
            addLayer: vi.fn((l) => layers.push(l)),
            removeLayer: vi.fn(),
            getLayers: vi.fn(() => [...layers]),
            eachLayer: vi.fn((cb) => layers.forEach(cb)),
            remove: vi.fn(),
        };
    }),
    svg: vi.fn(() => ({
        _container: (() => {
            const svg = document.createElementNS(
                "http://www.w3.org/2000/svg",
                "svg",
            );
            return svg;
        })(),
    })),
    FeatureGroup: vi.fn(function (layers = [], opts = {}) {
        return createMockFeatureGroup(layers, opts);
    }),
    marker: vi.fn(() => createMockLayer()),
    divIcon: vi.fn((opts) => opts),
    control: {
        zoom: vi.fn(() => ({
            addTo: vi.fn().mockReturnThis(),
        })),
        layers: vi.fn(() => ({
            addTo: vi.fn().mockReturnThis(),
        })),
    },
    Control: {
        Draw: vi.fn(function () {
            return { addTo: vi.fn() };
        }),
    },
    Draw: {
        Event: {
            CREATED: "draw:created",
            EDITED: "draw:edited",
            DELETED: "draw:deleted",
        },
    },
    LatLngBounds: vi.fn(function () {
        this.extend = vi.fn();
    }),
};

// Store reference for test access
globalThis.L = L;
globalThis.__mockMap = createMockMap;
globalThis.__mockLayer = createMockLayer;
globalThis.__mockFeatureGroup = createMockFeatureGroup;

// ── DOM setup ───────────────────────────────────────────────────

// CSRF meta tag
const meta = document.createElement("meta");
meta.setAttribute("name", "csrf-token");
meta.setAttribute("content", "test-csrf-token-12345");
document.head.appendChild(meta);

// Map container
const mapDiv = document.createElement("div");
mapDiv.id = "map";
mapDiv.style.width = "800px";
mapDiv.style.height = "600px";
document.body.appendChild(mapDiv);

// Mock fetch globally
globalThis.fetch = vi.fn();

// Mock ResizeObserver — must be a real class so `new ResizeObserver()` works
globalThis.ResizeObserver = class ResizeObserver {
    constructor(cb) {
        this._cb = cb;
    }
    observe() {}
    unobserve() {}
    disconnect() {}
};
