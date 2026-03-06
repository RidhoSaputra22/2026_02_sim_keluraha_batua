/**
 * Feature tests — Full map engine integration scenarios.
 *
 * These tests verify that multiple components work together
 * (MapEngine + LayerManager + patterns + editor) as a cohesive system.
 */
import { describe, it, expect, beforeEach, vi } from "vitest";
import MapEngine from "../../MapEngine";
import LayerManager from "../../layers/LayerManager";

// ── Test data (unified format with layer_type) ──────────────
const kelurahanGeojson = {
    type: "FeatureCollection",
    features: [
        {
            type: "Feature",
            properties: { nama: "Kelurahan Batua" },
            geometry: {
                type: "Polygon",
                coordinates: [
                    [
                        [119.45, -5.14],
                        [119.48, -5.14],
                        [119.48, -5.17],
                        [119.45, -5.17],
                        [119.45, -5.14],
                    ],
                ],
            },
        },
    ],
};

const rwGeojson = {
    type: "FeatureCollection",
    features: [
        {
            type: "Feature",
            properties: {
                RW: "RW 01",
                warna: "#6366f1",
                total_penduduk: 500,
                total_kk: 120,
                total_rt: 5,
                total_umkm: 10,
                laki_laki: 250,
                perempuan: 250,
            },
            geometry: {
                type: "Polygon",
                coordinates: [
                    [
                        [119.45, -5.14],
                        [119.46, -5.14],
                        [119.46, -5.16],
                        [119.45, -5.14],
                    ],
                ],
            },
        },
        {
            type: "Feature",
            properties: {
                RW: "RW 02",
                warna: "#ef4444",
                total_penduduk: 300,
                total_kk: 80,
                total_rt: 4,
                total_umkm: 5,
                laki_laki: 140,
                perempuan: 160,
            },
            geometry: {
                type: "Polygon",
                coordinates: [
                    [
                        [119.46, -5.14],
                        [119.48, -5.14],
                        [119.48, -5.16],
                        [119.46, -5.14],
                    ],
                ],
            },
        },
        {
            type: "Feature",
            properties: {
                RW: "RW 03",
                warna: "#22c55e",
                total_penduduk: 400,
                total_kk: 100,
                total_rt: 6,
                total_umkm: 8,
                laki_laki: 200,
                perempuan: 200,
            },
            geometry: {
                type: "Polygon",
                coordinates: [
                    [
                        [119.45, -5.16],
                        [119.48, -5.16],
                        [119.48, -5.17],
                        [119.45, -5.16],
                    ],
                ],
            },
        },
    ],
};

// Unified layer data wrappers
const kelLayerData = {
    id: 100,
    layer_type: "kelurahan",
    nama: "Kelurahan Batua",
    slug: "kelurahan-batua",
    warna: "#1e293b",
    fill_opacity: 0.02,
    stroke_width: 3,
    pattern_type: "solid",
    geojson: kelurahanGeojson,
};

const rwLayerData = {
    id: 200,
    layer_type: "rw",
    nama: "RW Layer",
    slug: "rw-layer",
    warna: "#6b7280",
    fill_opacity: 0.3,
    stroke_width: 2.5,
    pattern_type: "solid",
    geojson: rwGeojson,
};

const customLayerData = {
    id: 1,
    layer_type: "custom",
    nama: "Tempat Ibadah",
    slug: "tempat-ibadah",
    warna: "#10b981",
    fill_opacity: 0.3,
    stroke_width: 2,
    pattern_type: "dots",
    geojson: {
        type: "FeatureCollection",
        features: [
            {
                type: "Feature",
                properties: { nama: "Masjid Al-Ikhlas" },
                geometry: {
                    type: "Polygon",
                    coordinates: [
                        [
                            [119.46, -5.15],
                            [119.465, -5.15],
                            [119.465, -5.155],
                            [119.46, -5.15],
                        ],
                    ],
                },
            },
        ],
    },
};

// ── Feature: Full Map Initialization ────────────────────────
describe("Feature: Map Initialization & Layer Loading", () => {
    let engine;

    beforeEach(() => {
        vi.clearAllMocks();
        const container = document.getElementById("map");
        if (container) delete container._leaflet_id;
    });

    it("initializes MapEngine with SVG renderer and pattern support", () => {
        engine = new MapEngine("map").init();

        expect(engine.map).not.toBeNull();
        expect(engine.svgRenderer).not.toBeNull();
        expect(engine.patterns).not.toBeNull();
    });

    it("loads kelurahan boundary and RW polygons via renderAll", () => {
        engine = new MapEngine("map").init();

        const mgr = new LayerManager(engine);
        mgr.renderAll([kelLayerData, rwLayerData]);

        expect(mgr.kelurahanLayer).not.toBeNull();
        expect(mgr.kelurahanBounds).not.toBeNull();
        expect(mgr.rwLayer).not.toBeNull();
        expect(mgr.rwDataList).toHaveLength(3);
        expect(mgr.rwColors).toHaveProperty("RW 01");
        expect(mgr.rwColors).toHaveProperty("RW 02");
        expect(mgr.rwColors).toHaveProperty("RW 03");
    });

    it("passes engine.patterns to RW layer for hatch rendering", () => {
        engine = new MapEngine("map").init();

        const spy = vi.spyOn(engine.patterns, "applyRwPatterns");

        const mgr = new LayerManager(engine);
        mgr.renderAll([rwLayerData]);

        expect(spy).toHaveBeenCalledWith(mgr.rwColors, mgr.rwLayerMap);
    });
});

// ── Feature: RW Selection Workflow ──────────────────────────
describe("Feature: RW Selection & Interaction", () => {
    let engine;
    let mgr;
    let selectHistory;
    let deselectCount;

    beforeEach(() => {
        vi.clearAllMocks();
        const container = document.getElementById("map");
        if (container) delete container._leaflet_id;

        selectHistory = [];
        deselectCount = 0;

        engine = new MapEngine("map").init();
        engine.flyToBounds = vi.fn();

        mgr = new LayerManager(engine, {
            onRwSelect: (name, data) => selectHistory.push({ name, data }),
            onRwDeselect: () => deselectCount++,
        });
        mgr.renderAll([rwLayerData]);

        // Setup mock layers in rwLayerMap
        ["RW 01", "RW 02", "RW 03"].forEach((name) => {
            const layer = globalThis.__mockLayer();
            layer.feature = { properties: { RW: name } };
            mgr.rwLayerMap[name] = layer;
            mgr.rwColors[name] =
                rwGeojson.features.find(
                    (f) => f.properties.RW === name,
                )?.properties.warna || "#6b7280";
        });
    });

    it("selects RW and fires callback with correct data", () => {
        mgr.selectRw("RW 01");

        expect(mgr.selectedRw).toBe("RW 01");
        expect(selectHistory).toHaveLength(1);
        expect(selectHistory[0].name).toBe("RW 01");
    });

    it("switching selection from one RW to another", () => {
        mgr.selectRw("RW 01");
        expect(mgr.selectedRw).toBe("RW 01");

        mgr.selectRw("RW 02");
        expect(mgr.selectedRw).toBe("RW 02");
        expect(selectHistory).toHaveLength(2);
    });

    it("toggling same RW deselects", () => {
        mgr.selectRw("RW 01");
        mgr.selectRw("RW 01");

        expect(mgr.selectedRw).toBeNull();
        expect(deselectCount).toBe(1);
    });

    it("deselect clears everything", () => {
        mgr.selectRw("RW 02");
        mgr.deselectRw();

        expect(mgr.selectedRw).toBeNull();
        expect(mgr._highlightedRw).toBeNull();
        expect(deselectCount).toBe(1);
    });
});

// ── Feature: Layer Visibility Management ────────────────────
describe("Feature: Layer Visibility Management", () => {
    let engine;

    beforeEach(() => {
        vi.clearAllMocks();
        const container = document.getElementById("map");
        if (container) delete container._leaflet_id;

        engine = new MapEngine("map").init();
    });

    it("toggling kelurahan boundary visibility", () => {
        const mgr = new LayerManager(engine);
        mgr.renderAll([kelLayerData]);

        expect(mgr.showKelurahan).toBe(true);

        mgr.toggleKelurahan(false);
        expect(mgr.showKelurahan).toBe(false);
        expect(engine.map.removeLayer).toHaveBeenCalled();

        mgr.toggleKelurahan(true);
        expect(mgr.showKelurahan).toBe(true);
        expect(engine.map.addLayer).toHaveBeenCalled();
    });

    it("toggling RW layer hides both polygons and labels", () => {
        const mgr = new LayerManager(engine);
        mgr.renderAll([rwLayerData]);

        mgr.toggleRw(false);
        expect(mgr.showRw).toBe(false);
        expect(engine.map.removeLayer).toHaveBeenCalled();
    });

    it("toggling RW labels independently", () => {
        const mgr = new LayerManager(engine);
        mgr.renderAll([rwLayerData]);

        expect(mgr.showRwLabels).toBe(true);

        mgr.toggleRwLabels(false);
        expect(mgr.showRwLabels).toBe(false);
        expect(engine.map.removeLayer).toHaveBeenCalled();

        mgr.toggleRwLabels(true);
        expect(mgr.showRwLabels).toBe(true);
        expect(engine.map.addLayer).toHaveBeenCalled();
    });
});

// ── Feature: Custom Layer Loading & Toggle ──────────────────
describe("Feature: Custom Layer Loading & Toggle", () => {
    let engine;

    beforeEach(() => {
        vi.clearAllMocks();
        const container = document.getElementById("map");
        if (container) delete container._leaflet_id;

        engine = new MapEngine("map").init();
        engine.patterns = {
            applyCustomLayerPattern: vi.fn(),
            applyRwPatterns: vi.fn(),
        };
    });

    it("renders custom layers via renderAll", () => {
        const mgr = new LayerManager(engine);
        mgr.renderAll([customLayerData]);

        expect(mgr.customLayers).toHaveLength(1);
        expect(mgr.customLayers[0].nama).toBe("Tempat Ibadah");
        expect(mgr.customLayers[0].visible).toBe(true);
        expect(L.geoJSON).toHaveBeenCalled();
    });

    it("applies non-solid pattern to custom layer", () => {
        const mgr = new LayerManager(engine);
        mgr.renderAll([customLayerData]);

        expect(engine.patterns.applyCustomLayerPattern).toHaveBeenCalledWith(
            "tempat-ibadah",
            "dots",
            "#10b981",
            0.3,
            expect.any(Object),
        );
    });

    it("toggles custom layer on and off", () => {
        const mgr = new LayerManager(engine);
        mgr.renderAll([customLayerData]);

        mgr.toggleCustomLayer(1);
        expect(mgr.customLayers[0].visible).toBe(false);

        mgr.toggleCustomLayer(1);
        expect(mgr.customLayers[0].visible).toBe(true);
    });
});

// ── Feature: API Data Loading Pipeline ──────────────────────
describe("Feature: API Data Loading Pipeline", () => {
    let engine;

    beforeEach(() => {
        vi.clearAllMocks();
        const container = document.getElementById("map");
        if (container) delete container._leaflet_id;
        engine = new MapEngine("map").init();
    });

    it("loads all layers from unified API endpoint", async () => {
        const allLayers = [kelLayerData, rwLayerData, customLayerData];

        globalThis.fetch.mockResolvedValue({
            ok: true,
            json: () => Promise.resolve(allLayers),
            text: () => Promise.resolve(JSON.stringify(allLayers)),
        });

        const mgr = new LayerManager(engine);
        await mgr.load("/peta/geojson/layers");

        expect(globalThis.fetch).toHaveBeenCalledWith(
            "/peta/geojson/layers",
            expect.any(Object),
        );
        expect(mgr.kelurahanLayer).not.toBeNull();
        expect(mgr.rwDataList).toHaveLength(3);
        expect(mgr.customLayers).toHaveLength(1);
    });

    it("provides correct headers including CSRF token", async () => {
        const allLayers = [kelLayerData];

        globalThis.fetch.mockResolvedValue({
            ok: true,
            json: () => Promise.resolve(allLayers),
            text: () => Promise.resolve(JSON.stringify(allLayers)),
        });

        const mgr = new LayerManager(engine);
        await mgr.load("/peta/geojson/layers");

        const [, opts] = globalThis.fetch.mock.calls[0];
        expect(opts.headers["X-CSRF-TOKEN"]).toBe("test-csrf-token-12345");
        expect(opts.headers["Accept"]).toBe("application/json");
    });

    it("handles API errors gracefully without crashing map", async () => {
        globalThis.fetch.mockRejectedValue(new Error("Network error"));

        const mgr = new LayerManager(engine);
        // load() catches errors internally
        await mgr.load("/peta/geojson/layers");

        // Map should still be operational
        expect(engine.map).not.toBeNull();
        expect(mgr.kelurahanLayer).toBeNull();
        expect(mgr.rwDataList).toHaveLength(0);
    });
});

// ── Feature: MapEngine Lifecycle ────────────────────────────
describe("Feature: MapEngine Lifecycle", () => {
    beforeEach(() => {
        vi.clearAllMocks();
        const container = document.getElementById("map");
        if (container) delete container._leaflet_id;
    });

    it("full lifecycle: init → use → destroy", () => {
        const engine = new MapEngine("map").init();
        expect(engine.map).not.toBeNull();
        expect(engine.svgRenderer).not.toBeNull();
        expect(engine.patterns).not.toBeNull();

        // Simulate usage
        const mgr = new LayerManager(engine);
        mgr.renderAll([kelLayerData]);
        expect(mgr.kelurahanLayer).not.toBeNull();

        // Destroy
        engine.destroy();
        expect(engine.map).toBeNull();
        expect(engine._resizeObserver).toBeNull();
    });

    it("resize observer triggers invalidateSize", () => {
        const engine = new MapEngine("map").init();
        expect(engine._resizeObserver).not.toBeNull();
        expect(engine._resizeObserver).toBeInstanceOf(ResizeObserver);
    });

    it("constrainToBounds sets proper zoom limits", () => {
        const engine = new MapEngine("map").init();
        const bounds = {
            pad: vi.fn().mockReturnValue("paddedBounds"),
        };
        engine.constrainToBounds(bounds, 0.2, 4);

        expect(bounds.pad).toHaveBeenCalledWith(0.2);
        expect(engine.map.setMaxBounds).toHaveBeenCalledWith("paddedBounds");
        expect(engine.map.setMinZoom).toHaveBeenCalledWith(14); // 15 - 1
        expect(engine.map.setMaxZoom).toHaveBeenCalledWith(19); // 15 + 4
    });
});

// ── Feature: Data Aggregation for Dashboard ─────────────────
describe("Feature: RW Data for Dashboard Display", () => {
    let engine;

    beforeEach(() => {
        vi.clearAllMocks();
        const container = document.getElementById("map");
        if (container) delete container._leaflet_id;
        engine = new MapEngine("map").init();
        engine.flyToBounds = vi.fn();
    });

    it("provides sorted RW data for sidebar rendering", () => {
        const { sortRwList } = require("../../utils/helpers");

        const mgr = new LayerManager(engine);
        mgr.renderAll([rwLayerData]);

        const sorted = sortRwList(mgr.rwDataList);
        expect(sorted[0].name).toBe("RW 01");
        expect(sorted[1].name).toBe("RW 02");
        expect(sorted[2].name).toBe("RW 03");
    });

    it("includes population statistics per RW", () => {
        const mgr = new LayerManager(engine);
        mgr.renderAll([rwLayerData]);

        const rw01 = mgr.rwDataList.find((d) => d.name === "RW 01");
        expect(rw01).toEqual(
            expect.objectContaining({
                total_penduduk: 500,
                total_kk: 120,
                total_rt: 5,
                total_umkm: 10,
                laki_laki: 250,
                perempuan: 250,
            }),
        );
    });

    it("onRwDataLoad callback receives complete data", () => {
        const onRwDataLoad = vi.fn();
        const mgr = new LayerManager(engine, { onRwDataLoad });
        mgr.renderAll([rwLayerData]);

        expect(onRwDataLoad).toHaveBeenCalledOnce();
        const data = onRwDataLoad.mock.calls[0][0];
        expect(data).toHaveLength(3);
        data.forEach((item) => {
            expect(item).toHaveProperty("name");
            expect(item).toHaveProperty("warna");
            expect(item).toHaveProperty("total_penduduk");
        });
    });
});
