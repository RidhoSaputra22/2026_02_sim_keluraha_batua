/**
 * Unit tests for layers/CustomLayerManager.js
 */
import { describe, it, expect, beforeEach, vi } from "vitest";
import CustomLayerManager from "../../layers/CustomLayerManager";

describe("CustomLayerManager", () => {
    let engine;

    const sampleLayersData = [
        {
            id: 1,
            nama: "Tempat Ibadah",
            slug: "tempat-ibadah",
            warna: "#10b981",
            fill_opacity: 0.3,
            stroke_width: 2,
            pattern_type: "solid",
            geojson: {
                type: "FeatureCollection",
                features: [
                    {
                        type: "Feature",
                        properties: { nama: "Masjid Al-Ikhlas", deskripsi: "Masjid utama" },
                        geometry: {
                            type: "Polygon",
                            coordinates: [[[119.46, -5.15], [119.47, -5.15], [119.47, -5.16], [119.46, -5.15]]],
                        },
                    },
                ],
            },
        },
        {
            id: 2,
            nama: "Area Hijau",
            slug: "area-hijau",
            warna: "#22c55e",
            fill_opacity: 0.2,
            stroke_width: 1,
            pattern_type: "hatch",
            geojson: {
                type: "FeatureCollection",
                features: [
                    {
                        type: "Feature",
                        properties: { nama: "Taman RW 01" },
                        geometry: {
                            type: "Polygon",
                            coordinates: [[[119.46, -5.15], [119.47, -5.15], [119.47, -5.16], [119.46, -5.15]]],
                        },
                    },
                ],
            },
        },
    ];

    beforeEach(() => {
        vi.clearAllMocks();
        engine = {
            map: globalThis.__mockMap(),
            svgRenderer: { _container: document.createElement("div") },
            patterns: {
                applyCustomLayerPattern: vi.fn(),
            },
        };
    });

    // ── Constructor ─────────────────────────────────────────
    describe("constructor", () => {
        it("stores engine reference", () => {
            const mgr = new CustomLayerManager(engine);
            expect(mgr.engine).toBe(engine);
        });

        it("initializes with empty layers and mapLayers", () => {
            const mgr = new CustomLayerManager(engine);
            expect(mgr.layers).toEqual([]);
            expect(mgr._mapLayers).toEqual({});
        });
    });

    // ── load ────────────────────────────────────────────────
    describe("load", () => {
        it("fetches data and populates layers metadata", async () => {
            globalThis.fetch.mockResolvedValue({
                ok: true,
                json: () => Promise.resolve(sampleLayersData),
            });

            const mgr = new CustomLayerManager(engine);
            const result = await mgr.load("/peta/custom-layers");

            expect(result).toHaveLength(2);
            expect(mgr.layers[0]).toEqual(
                expect.objectContaining({
                    id: 1,
                    nama: "Tempat Ibadah",
                    slug: "tempat-ibadah",
                    visible: true,
                    polygonCount: 1,
                }),
            );
        });

        it("renders all layers on the map", async () => {
            globalThis.fetch.mockResolvedValue({
                ok: true,
                json: () => Promise.resolve(sampleLayersData),
            });

            const mgr = new CustomLayerManager(engine);
            await mgr.load("/peta/custom-layers");

            // geoJSON should be called for each layer
            expect(L.geoJSON).toHaveBeenCalledTimes(2);
        });

        it("binds tooltip for each custom layer feature", async () => {
            globalThis.fetch.mockResolvedValue({
                ok: true,
                json: () => Promise.resolve(sampleLayersData),
            });

            const tooltipSpy = vi.fn();
            L.geoJSON.mockImplementation((data, opts) => {
                const layer = globalThis.__mockLayer();
                if (data?.features && opts?.onEachFeature) {
                    data.features.forEach((feature) => {
                        const childLayer = globalThis.__mockLayer({ feature });
                        childLayer.feature = feature;
                        childLayer.bindTooltip = tooltipSpy;
                        opts.onEachFeature(feature, childLayer);
                    });
                }
                layer.eachLayer = vi.fn((cb) => {
                    if (data?.features) {
                        data.features.forEach((feature) => {
                            const childLayer = globalThis.__mockLayer({ feature });
                            childLayer.feature = feature;
                            cb(childLayer);
                        });
                    }
                });
                return layer;
            });

            const mgr = new CustomLayerManager(engine);
            await mgr.load("/peta/custom-layers");

            expect(tooltipSpy).toHaveBeenCalled();
            expect(tooltipSpy).toHaveBeenCalledWith(
                expect.stringContaining("Masjid Al-Ikhlas"),
                expect.objectContaining({
                    sticky: true,
                    direction: "top",
                }),
            );
        });

        it("applies non-solid patterns", async () => {
            globalThis.fetch.mockResolvedValue({
                ok: true,
                json: () => Promise.resolve(sampleLayersData),
            });

            const mgr = new CustomLayerManager(engine);
            await mgr.load("/peta/custom-layers");

            // Hatch pattern should be applied for area-hijau
            expect(engine.patterns.applyCustomLayerPattern).toHaveBeenCalledWith(
                "area-hijau",
                "hatch",
                "#22c55e",
                0.2,
                expect.any(Object),
            );
        });

        it("handles fetch errors gracefully", async () => {
            globalThis.fetch.mockRejectedValue(new Error("Network error"));

            const errorSpy = vi.spyOn(console, "error").mockImplementation();
            const mgr = new CustomLayerManager(engine);
            const result = await mgr.load("/peta/custom-layers");

            expect(result).toEqual([]);
            expect(errorSpy).toHaveBeenCalled();
        });

        it("skips layers with no features", async () => {
            const emptyLayer = {
                ...sampleLayersData[0],
                id: 3,
                geojson: { type: "FeatureCollection", features: [] },
            };
            globalThis.fetch.mockResolvedValue({
                ok: true,
                json: () => Promise.resolve([emptyLayer]),
            });

            const mgr = new CustomLayerManager(engine);
            await mgr.load("/peta/custom-layers");

            expect(mgr.layers[0].polygonCount).toBe(0);
        });
    });

    // ── toggle ──────────────────────────────────────────────
    describe("toggle", () => {
        it("hides a visible layer", async () => {
            globalThis.fetch.mockResolvedValue({
                ok: true,
                json: () => Promise.resolve(sampleLayersData),
            });

            const mgr = new CustomLayerManager(engine);
            await mgr.load("/peta/custom-layers");

            mgr.toggle(1);
            expect(mgr.layers[0].visible).toBe(false);
            expect(engine.map.removeLayer).toHaveBeenCalled();
        });

        it("shows a hidden layer", async () => {
            globalThis.fetch.mockResolvedValue({
                ok: true,
                json: () => Promise.resolve(sampleLayersData),
            });

            const mgr = new CustomLayerManager(engine);
            await mgr.load("/peta/custom-layers");

            mgr.toggle(1); // hide
            mgr.toggle(1); // show
            expect(mgr.layers[0].visible).toBe(true);
            expect(engine.map.addLayer).toHaveBeenCalled();
        });

        it("does nothing for unknown layer id", () => {
            const mgr = new CustomLayerManager(engine);
            expect(() => mgr.toggle(999)).not.toThrow();
        });
    });

    // ── z-order ─────────────────────────────────────────────
    describe("bringToFront", () => {
        it("brings visible custom layers to front", async () => {
            globalThis.fetch.mockResolvedValue({
                ok: true,
                json: () => Promise.resolve(sampleLayersData),
            });

            const parentBringToFront = vi.fn();
            const childBringToFront = vi.fn();

            L.geoJSON.mockImplementationOnce((data, opts) => {
                const layer = globalThis.__mockLayer();
                layer.bringToFront = parentBringToFront;
                layer.eachLayer = vi.fn((cb) => {
                    const child = globalThis.__mockLayer();
                    child.bringToFront = childBringToFront;
                    cb(child);
                });

                if (data?.features && opts?.onEachFeature) {
                    data.features.forEach((feature) => {
                        const childLayer = globalThis.__mockLayer({ feature });
                        childLayer.feature = feature;
                        opts.onEachFeature(feature, childLayer);
                    });
                }

                return layer;
            });

            const mgr = new CustomLayerManager(engine);
            await mgr.load("/peta/custom-layers");
            mgr.bringToFront();

            expect(parentBringToFront).toHaveBeenCalled();
            expect(childBringToFront).toHaveBeenCalled();
        });
    });
});
