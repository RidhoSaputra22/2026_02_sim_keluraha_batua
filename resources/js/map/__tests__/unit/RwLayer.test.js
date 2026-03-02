/**
 * Unit tests for layers/RwLayer.js
 */
import { describe, it, expect, beforeEach, vi } from "vitest";
import RwLayer from "../../layers/RwLayer";

describe("RwLayer", () => {
    let engine;

    const sampleGeojson = {
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
                            [119.46, -5.15],
                            [119.47, -5.15],
                            [119.47, -5.16],
                            [119.46, -5.15],
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
                    laki_laki: 150,
                    perempuan: 150,
                },
                geometry: {
                    type: "Polygon",
                    coordinates: [
                        [
                            [119.47, -5.15],
                            [119.48, -5.15],
                            [119.48, -5.16],
                            [119.47, -5.15],
                        ],
                    ],
                },
            },
        ],
    };

    beforeEach(() => {
        vi.clearAllMocks();
        engine = {
            map: globalThis.__mockMap(),
            svgRenderer: { _container: document.createElement("div") },
            patterns: {
                applyRwPatterns: vi.fn(),
                applyToLayer: vi.fn(),
            },
            flyToBounds: vi.fn(),
        };
    });

    // ── Constructor ─────────────────────────────────────────
    describe("constructor", () => {
        it("stores engine and callbacks", () => {
            const cb = { onSelect: vi.fn() };
            const rw = new RwLayer(engine, cb);
            expect(rw.engine).toBe(engine);
            expect(rw.callbacks).toBe(cb);
        });

        it("initializes with empty state", () => {
            const rw = new RwLayer(engine);
            expect(rw.layer).toBeNull();
            expect(rw.labelLayer).toBeNull();
            expect(rw.colors).toEqual({});
            expect(rw.layerMap).toEqual({});
            expect(rw.dataList).toEqual([]);
            expect(rw.selectedRw).toBeNull();
            expect(rw.visible).toBe(true);
            expect(rw.labelsVisible).toBe(true);
        });
    });

    // ── render ──────────────────────────────────────────────
    describe("render", () => {
        it("creates a geoJSON layer and label layer", () => {
            const rw = new RwLayer(engine);
            rw.render(sampleGeojson);

            expect(L.geoJSON).toHaveBeenCalled();
            expect(L.layerGroup).toHaveBeenCalled();
            expect(rw.layer).not.toBeNull();
            expect(rw.labelLayer).not.toBeNull();
        });

        it("builds color map from GeoJSON features", () => {
            const rw = new RwLayer(engine);
            rw.render(sampleGeojson);

            expect(rw.colors["RW 01"]).toBe("#6366f1");
            expect(rw.colors["RW 02"]).toBe("#ef4444");
        });

        it("builds dataList from GeoJSON features", () => {
            const rw = new RwLayer(engine);
            rw.render(sampleGeojson);

            expect(rw.dataList).toHaveLength(2);
            expect(rw.dataList[0]).toEqual(
                expect.objectContaining({
                    name: "RW 01",
                    warna: "#6366f1",
                    total_penduduk: 500,
                    total_kk: 120,
                }),
            );
        });

        it("applies hatch patterns via engine.patterns", () => {
            const rw = new RwLayer(engine);
            rw.render(sampleGeojson);

            expect(engine.patterns.applyRwPatterns).toHaveBeenCalledWith(
                rw.colors,
                rw.layerMap,
            );
        });

        it("calls onDataLoad callback with dataList", () => {
            const onDataLoad = vi.fn();
            const rw = new RwLayer(engine, { onDataLoad });
            rw.render(sampleGeojson);

            expect(onDataLoad).toHaveBeenCalledWith(rw.dataList);
        });
    });

    // ── load ────────────────────────────────────────────────
    describe("load", () => {
        it("fetches data and renders", async () => {
            globalThis.fetch.mockResolvedValue({
                ok: true,
                json: () => Promise.resolve(sampleGeojson),
            });

            const rw = new RwLayer(engine);
            const result = await rw.load("/peta/geojson/rw");

            expect(result).toBe(rw);
            expect(globalThis.fetch).toHaveBeenCalled();
        });

        it("handles error response", async () => {
            globalThis.fetch.mockResolvedValue({
                ok: true,
                json: () => Promise.resolve({ error: "No data" }),
            });

            const warnSpy = vi.spyOn(console, "warn").mockImplementation();
            const rw = new RwLayer(engine);
            await rw.load("/peta/geojson/rw");

            expect(rw.layer).toBeNull();
        });
    });

    // ── select / deselect ───────────────────────────────────
    describe("select", () => {
        it("highlights selected RW and calls onSelect callback", () => {
            const onSelect = vi.fn();
            const rw = new RwLayer(engine, { onSelect });
            rw.render(sampleGeojson);

            // Manually add a layer to layerMap for testing
            const mockLayer = globalThis.__mockLayer();
            mockLayer.feature = { properties: { RW: "RW 01" } };
            rw.layerMap["RW 01"] = mockLayer;

            rw.select("RW 01");

            expect(rw.selectedRw).toBe("RW 01");
            expect(onSelect).toHaveBeenCalledWith(
                "RW 01",
                expect.any(Object),
            );
        });

        it("deselects when selecting the same RW again", () => {
            const onDeselect = vi.fn();
            const rw = new RwLayer(engine, { onDeselect });
            rw.render(sampleGeojson);

            const mockLayer = globalThis.__mockLayer();
            mockLayer.feature = { properties: { RW: "RW 01" } };
            rw.layerMap["RW 01"] = mockLayer;

            rw.select("RW 01");
            rw.select("RW 01"); // toggle off

            expect(rw.selectedRw).toBeNull();
            expect(onDeselect).toHaveBeenCalled();
        });

        it("does nothing for non-existent RW", () => {
            const rw = new RwLayer(engine);
            rw.render(sampleGeojson);
            rw.select("RW 99");
            expect(rw.selectedRw).toBeNull();
        });
    });

    describe("deselect", () => {
        it("clears selection and calls onDeselect", () => {
            const onDeselect = vi.fn();
            const rw = new RwLayer(engine, { onDeselect });

            const mockLayer = globalThis.__mockLayer();
            mockLayer.feature = { properties: { RW: "RW 01" } };
            rw.layerMap["RW 01"] = mockLayer;
            rw.selectedRw = "RW 01";
            rw._highlightedLayer = mockLayer;

            rw.deselect();

            expect(rw.selectedRw).toBeNull();
            expect(rw._highlightedLayer).toBeNull();
            expect(onDeselect).toHaveBeenCalled();
        });
    });

    // ── toggle ──────────────────────────────────────────────
    describe("toggle", () => {
        it("hides RW polygons and labels", () => {
            const rw = new RwLayer(engine);
            rw.render(sampleGeojson);
            rw.toggle(false);

            expect(rw.visible).toBe(false);
            expect(engine.map.removeLayer).toHaveBeenCalled();
        });

        it("shows RW polygons and labels", () => {
            const rw = new RwLayer(engine);
            rw.render(sampleGeojson);
            rw.visible = false;
            rw.toggle(true);

            expect(rw.visible).toBe(true);
            expect(engine.map.addLayer).toHaveBeenCalled();
        });

        it("auto-toggles when no arg provided", () => {
            const rw = new RwLayer(engine);
            rw.render(sampleGeojson);
            expect(rw.visible).toBe(true);

            rw.toggle();
            expect(rw.visible).toBe(false);
        });
    });

    // ── toggleLabels ────────────────────────────────────────
    describe("toggleLabels", () => {
        it("hides labels", () => {
            const rw = new RwLayer(engine);
            rw.render(sampleGeojson);
            rw.toggleLabels(false);
            expect(rw.labelsVisible).toBe(false);
        });

        it("shows labels", () => {
            const rw = new RwLayer(engine);
            rw.render(sampleGeojson);
            rw.labelsVisible = false;
            rw.toggleLabels(true);
            expect(rw.labelsVisible).toBe(true);
        });

        it("auto-toggles when no arg", () => {
            const rw = new RwLayer(engine);
            rw.render(sampleGeojson);
            rw.toggleLabels();
            expect(rw.labelsVisible).toBe(false);
        });
    });
});
