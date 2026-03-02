/**
 * Feature tests — Full map engine integration scenarios.
 *
 * These tests verify that multiple components work together
 * (MapEngine + layers + patterns + editor) as a cohesive system.
 */
import { describe, it, expect, beforeEach, vi } from "vitest";
import MapEngine from "../../MapEngine";
import KelurahanLayer from "../../layers/KelurahanLayer";
import RwLayer from "../../layers/RwLayer";
import CustomLayerManager from "../../layers/CustomLayerManager";

// ── Test data ───────────────────────────────────────────────
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

const customLayersData = [
    {
        id: 1,
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
    },
];

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

    it("loads kelurahan boundary and RW polygons in sequence", () => {
        engine = new MapEngine("map").init();

        const kelurahan = new KelurahanLayer(engine);
        kelurahan.render(kelurahanGeojson);
        expect(kelurahan.layer).not.toBeNull();
        expect(kelurahan.bounds).not.toBeNull();

        const rw = new RwLayer(engine);
        rw.render(rwGeojson);
        expect(rw.layer).not.toBeNull();
        expect(rw.dataList).toHaveLength(3);
        expect(rw.colors).toHaveProperty("RW 01");
        expect(rw.colors).toHaveProperty("RW 02");
        expect(rw.colors).toHaveProperty("RW 03");
    });

    it("passes engine.patterns to RW layer for hatch rendering", () => {
        engine = new MapEngine("map").init();

        // Spy on patterns method
        const spy = vi.spyOn(engine.patterns, "applyRwPatterns");

        const rw = new RwLayer(engine);
        rw.render(rwGeojson);

        expect(spy).toHaveBeenCalledWith(rw.colors, rw.layerMap);
    });
});

// ── Feature: RW Selection Workflow ──────────────────────────
describe("Feature: RW Selection & Interaction", () => {
    let engine;
    let rw;
    let selectHistory;
    let deselectCount;

    beforeEach(() => {
        vi.clearAllMocks();
        const container = document.getElementById("map");
        if (container) delete container._leaflet_id;

        selectHistory = [];
        deselectCount = 0;

        engine = new MapEngine("map").init();
        engine.flyToBounds = vi.fn(); // mock since it delegates to map

        rw = new RwLayer(engine, {
            onSelect: (name, data) => selectHistory.push({ name, data }),
            onDeselect: () => deselectCount++,
        });
        rw.render(rwGeojson);

        // Setup mock layers in layerMap
        ["RW 01", "RW 02", "RW 03"].forEach((name) => {
            const layer = globalThis.__mockLayer();
            layer.feature = { properties: { RW: name } };
            rw.layerMap[name] = layer;
            rw.colors[name] =
                rwGeojson.features.find(
                    (f) => f.properties.RW === name,
                )?.properties.warna || "#6b7280";
        });
    });

    it("selects RW and fires callback with correct data", () => {
        rw.select("RW 01");

        expect(rw.selectedRw).toBe("RW 01");
        expect(selectHistory).toHaveLength(1);
        expect(selectHistory[0].name).toBe("RW 01");
    });

    it("switching selection from one RW to another", () => {
        rw.select("RW 01");
        expect(rw.selectedRw).toBe("RW 01");

        rw.select("RW 02");
        expect(rw.selectedRw).toBe("RW 02");
        expect(selectHistory).toHaveLength(2);
    });

    it("toggling same RW deselects", () => {
        rw.select("RW 01");
        rw.select("RW 01");

        expect(rw.selectedRw).toBeNull();
        expect(deselectCount).toBe(1);
    });

    it("deselect clears everything", () => {
        rw.select("RW 02");
        rw.deselect();

        expect(rw.selectedRw).toBeNull();
        expect(rw._highlightedLayer).toBeNull();
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
        const kelurahan = new KelurahanLayer(engine);
        kelurahan.render(kelurahanGeojson);

        expect(kelurahan.visible).toBe(true);

        kelurahan.toggle(false);
        expect(kelurahan.visible).toBe(false);
        expect(engine.map.removeLayer).toHaveBeenCalled();

        kelurahan.toggle(true);
        expect(kelurahan.visible).toBe(true);
        expect(engine.map.addLayer).toHaveBeenCalled();
    });

    it("toggling RW layer hides both polygons and labels", () => {
        const rw = new RwLayer(engine);
        rw.render(rwGeojson);

        rw.toggle(false);
        expect(rw.visible).toBe(false);
        // removeLayer should be called for both the polygon layer and label layer
        expect(engine.map.removeLayer).toHaveBeenCalled();
    });

    it("toggling RW labels independently", () => {
        const rw = new RwLayer(engine);
        rw.render(rwGeojson);

        // Labels visible by default
        expect(rw.labelsVisible).toBe(true);

        rw.toggleLabels(false);
        expect(rw.labelsVisible).toBe(false);
        expect(engine.map.removeLayer).toHaveBeenCalled();

        rw.toggleLabels(true);
        expect(rw.labelsVisible).toBe(true);
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

    it("loads custom layers from API and renders on map", async () => {
        globalThis.fetch.mockResolvedValue({
            ok: true,
            json: () => Promise.resolve(customLayersData),
        });

        const mgr = new CustomLayerManager(engine);
        const layers = await mgr.load("/peta/custom-layers");

        expect(layers).toHaveLength(1);
        expect(layers[0].nama).toBe("Tempat Ibadah");
        expect(layers[0].visible).toBe(true);
        expect(L.geoJSON).toHaveBeenCalled();
    });

    it("applies non-solid pattern to custom layer", async () => {
        globalThis.fetch.mockResolvedValue({
            ok: true,
            json: () => Promise.resolve(customLayersData),
        });

        const mgr = new CustomLayerManager(engine);
        await mgr.load("/peta/custom-layers");

        expect(engine.patterns.applyCustomLayerPattern).toHaveBeenCalledWith(
            "tempat-ibadah",
            "dots",
            "#10b981",
            0.3,
            expect.any(Object),
        );
    });

    it("toggles custom layer on and off", async () => {
        globalThis.fetch.mockResolvedValue({
            ok: true,
            json: () => Promise.resolve(customLayersData),
        });

        const mgr = new CustomLayerManager(engine);
        await mgr.load("/peta/custom-layers");

        mgr.toggle(1);
        expect(mgr.layers[0].visible).toBe(false);

        mgr.toggle(1);
        expect(mgr.layers[0].visible).toBe(true);
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

    it("loads kelurahan boundary from API", async () => {
        globalThis.fetch.mockResolvedValue({
            ok: true,
            json: () => Promise.resolve(kelurahanGeojson),
        });

        const kelurahan = new KelurahanLayer(engine);
        await kelurahan.load("/peta/geojson/kelurahan");

        expect(globalThis.fetch).toHaveBeenCalledWith(
            "/peta/geojson/kelurahan",
            expect.any(Object),
        );
        expect(kelurahan.layer).not.toBeNull();
    });

    it("loads RW GeoJSON from API", async () => {
        globalThis.fetch.mockResolvedValue({
            ok: true,
            json: () => Promise.resolve(rwGeojson),
        });

        const rw = new RwLayer(engine);
        await rw.load("/peta/geojson/rw");

        expect(globalThis.fetch).toHaveBeenCalledWith(
            "/peta/geojson/rw",
            expect.any(Object),
        );
        expect(rw.dataList).toHaveLength(3);
    });

    it("provides correct headers including CSRF token", async () => {
        globalThis.fetch.mockResolvedValue({
            ok: true,
            json: () => Promise.resolve(kelurahanGeojson),
        });

        const kelurahan = new KelurahanLayer(engine);
        await kelurahan.load("/peta/geojson/kelurahan");

        const [, opts] = globalThis.fetch.mock.calls[0];
        expect(opts.headers["X-CSRF-TOKEN"]).toBe("test-csrf-token-12345");
        expect(opts.headers["Accept"]).toBe("application/json");
    });

    it("handles API errors gracefully without crashing map", async () => {
        globalThis.fetch.mockRejectedValue(new Error("Network error"));

        const kelurahan = new KelurahanLayer(engine);
        await expect(
            kelurahan.load("/peta/geojson/kelurahan"),
        ).rejects.toThrow();

        // Map should still be operational
        expect(engine.map).not.toBeNull();
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
        const kelurahan = new KelurahanLayer(engine);
        kelurahan.render(kelurahanGeojson);
        expect(kelurahan.layer).not.toBeNull();

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

        const rw = new RwLayer(engine);
        rw.render(rwGeojson);

        const sorted = sortRwList(rw.dataList);
        expect(sorted[0].name).toBe("RW 01");
        expect(sorted[1].name).toBe("RW 02");
        expect(sorted[2].name).toBe("RW 03");
    });

    it("includes population statistics per RW", () => {
        const rw = new RwLayer(engine);
        rw.render(rwGeojson);

        const rw01 = rw.dataList.find((d) => d.name === "RW 01");
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

    it("onDataLoad callback receives complete data", () => {
        const onDataLoad = vi.fn();
        const rw = new RwLayer(engine, { onDataLoad });
        rw.render(rwGeojson);

        expect(onDataLoad).toHaveBeenCalledOnce();
        const data = onDataLoad.mock.calls[0][0];
        expect(data).toHaveLength(3);
        data.forEach((item) => {
            expect(item).toHaveProperty("name");
            expect(item).toHaveProperty("warna");
            expect(item).toHaveProperty("total_penduduk");
        });
    });
});
