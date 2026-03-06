/**
 * Unit tests for layers/LayerManager.js
 */
import { describe, it, expect, beforeEach, vi } from "vitest";
import LayerManager from "../../layers/LayerManager";

describe("LayerManager", () => {
    let engine;

    // ── Sample data matching the unified API response format ──
    const kelurahanLayerData = {
        id: 100,
        nama: "Batas Kelurahan",
        slug: "batas-kelurahan",
        warna: "#1e293b",
        fill_opacity: 0.02,
        stroke_width: 3,
        pattern_type: "solid",
        sort_order: 0,
        layer_type: "kelurahan",
        geojson: {
            type: "FeatureCollection",
            features: [
                {
                    type: "Feature",
                    properties: { nama: "Kelurahan Batua" },
                    geometry: {
                        type: "Polygon",
                        coordinates: [
                            [
                                [119.46, -5.15],
                                [119.47, -5.15],
                                [119.47, -5.16],
                                [119.46, -5.16],
                                [119.46, -5.15],
                            ],
                        ],
                    },
                },
            ],
        },
    };

    const rwLayerData = {
        id: 101,
        nama: "Wilayah RW",
        slug: "wilayah-rw",
        warna: "#6b7280",
        fill_opacity: 0.3,
        stroke_width: 2.5,
        pattern_type: "hatch",
        sort_order: 1,
        layer_type: "rw",
        geojson: {
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
        },
    };

    const customLayersData = [
        {
            id: 1,
            nama: "Tempat Ibadah",
            slug: "tempat-ibadah",
            warna: "#10b981",
            fill_opacity: 0.3,
            stroke_width: 2,
            pattern_type: "solid",
            sort_order: 10,
            layer_type: "custom",
            geojson: {
                type: "FeatureCollection",
                features: [
                    {
                        type: "Feature",
                        properties: {
                            nama: "Masjid Al-Ikhlas",
                            deskripsi: "Masjid utama",
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
            sort_order: 20,
            layer_type: "custom",
            geojson: {
                type: "FeatureCollection",
                features: [
                    {
                        type: "Feature",
                        properties: { nama: "Taman RW 01" },
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
                ],
            },
        },
    ];

    /** All layers combined (unified API response) */
    const allLayersData = [
        kelurahanLayerData,
        rwLayerData,
        ...customLayersData,
    ];

    beforeEach(() => {
        vi.clearAllMocks();
        engine = {
            map: globalThis.__mockMap(),
            svgRenderer: { _container: document.createElement("div") },
            patterns: {
                applyRwPatterns: vi.fn(),
                applyCustomLayerPattern: vi.fn(),
                applyToLayer: vi.fn(),
            },
            fitBounds: vi.fn(),
            flyToBounds: vi.fn(),
        };
    });

    // ══════════════════════════════════════════════════════
    // Constructor
    // ══════════════════════════════════════════════════════
    describe("constructor", () => {
        it("stores engine and callbacks", () => {
            const cb = { onRwSelect: vi.fn() };
            const mgr = new LayerManager(engine, cb);
            expect(mgr.engine).toBe(engine);
            expect(mgr.callbacks).toBe(cb);
        });

        it("initializes with empty state", () => {
            const mgr = new LayerManager(engine);
            // Kelurahan
            expect(mgr.kelurahanLayer).toBeNull();
            expect(mgr.kelurahanBounds).toBeNull();
            expect(mgr.showKelurahan).toBe(true);
            // RW
            expect(mgr.rwLayer).toBeNull();
            expect(mgr.rwLabelLayer).toBeNull();
            expect(mgr.rwColors).toEqual({});
            expect(mgr.rwLayerMap).toEqual({});
            expect(mgr.rwDataList).toEqual([]);
            expect(mgr.selectedRw).toBeNull();
            expect(mgr.showRw).toBe(true);
            expect(mgr.showRwLabels).toBe(true);
            // Custom
            expect(mgr.customLayers).toEqual([]);
            expect(mgr._customMapLayers).toEqual({});
            // All
            expect(mgr.allLayers).toEqual([]);
        });
    });

    // ══════════════════════════════════════════════════════
    // renderAll — kelurahan
    // ══════════════════════════════════════════════════════
    describe("renderAll — kelurahan", () => {
        it("creates L.geoJSON with boundary style on kelurahanPane", () => {
            const mgr = new LayerManager(engine);
            mgr.renderAll([kelurahanLayerData]);

            expect(L.geoJSON).toHaveBeenCalledWith(
                kelurahanLayerData.geojson,
                expect.objectContaining({
                    pane: "kelurahanPane",
                    interactive: false,
                    style: expect.objectContaining({
                        color: "#1e293b",
                        weight: 3,
                        dashArray: "10, 6",
                    }),
                }),
            );
            expect(mgr.kelurahanLayer).not.toBeNull();
            expect(mgr.kelurahanBounds).not.toBeNull();
        });

        it("uses shared svgRenderer", () => {
            const mgr = new LayerManager(engine);
            mgr.renderAll([kelurahanLayerData]);

            expect(L.geoJSON).toHaveBeenCalledWith(
                kelurahanLayerData.geojson,
                expect.objectContaining({
                    renderer: engine.svgRenderer,
                }),
            );
        });
    });

    // ══════════════════════════════════════════════════════
    // renderAll — RW
    // ══════════════════════════════════════════════════════
    describe("renderAll — RW", () => {
        it("creates geoJSON layer and label layer on rwPane", () => {
            const mgr = new LayerManager(engine);
            mgr.renderAll([rwLayerData]);

            expect(L.geoJSON).toHaveBeenCalled();
            expect(L.layerGroup).toHaveBeenCalled();
            expect(mgr.rwLayer).not.toBeNull();
            expect(mgr.rwLabelLayer).not.toBeNull();
        });

        it("builds color map from features", () => {
            const mgr = new LayerManager(engine);
            mgr.renderAll([rwLayerData]);

            expect(mgr.rwColors["RW 01"]).toBe("#6366f1");
            expect(mgr.rwColors["RW 02"]).toBe("#ef4444");
        });

        it("builds rwDataList from features", () => {
            const mgr = new LayerManager(engine);
            mgr.renderAll([rwLayerData]);

            expect(mgr.rwDataList).toHaveLength(2);
            expect(mgr.rwDataList[0]).toEqual(
                expect.objectContaining({
                    name: "RW 01",
                    warna: "#6366f1",
                    total_penduduk: 500,
                    total_kk: 120,
                }),
            );
        });

        it("applies hatch patterns via engine.patterns", () => {
            const mgr = new LayerManager(engine);
            mgr.renderAll([rwLayerData]);

            expect(engine.patterns.applyRwPatterns).toHaveBeenCalledWith(
                mgr.rwColors,
                mgr.rwLayerMap,
            );
        });

        it("calls onRwDataLoad callback", () => {
            const onRwDataLoad = vi.fn();
            const mgr = new LayerManager(engine, { onRwDataLoad });
            mgr.renderAll([rwLayerData]);

            expect(onRwDataLoad).toHaveBeenCalledWith(mgr.rwDataList);
        });
    });

    // ══════════════════════════════════════════════════════
    // renderAll — custom layers
    // ══════════════════════════════════════════════════════
    describe("renderAll — custom layers", () => {
        it("renders custom layers on customLayerPane", () => {
            const mgr = new LayerManager(engine);
            mgr.renderAll(customLayersData);

            expect(L.geoJSON).toHaveBeenCalledTimes(2);
            expect(mgr.customLayers).toHaveLength(2);
            expect(mgr.customLayers[0]).toEqual(
                expect.objectContaining({
                    id: 1,
                    nama: "Tempat Ibadah",
                    visible: true,
                    polygonCount: 1,
                }),
            );
        });

        it("binds tooltip for custom layer features", () => {
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
                            const childLayer = globalThis.__mockLayer({
                                feature,
                            });
                            childLayer.feature = feature;
                            cb(childLayer);
                        });
                    }
                });
                return layer;
            });

            const mgr = new LayerManager(engine);
            mgr.renderAll(customLayersData);

            expect(tooltipSpy).toHaveBeenCalled();
            expect(tooltipSpy).toHaveBeenCalledWith(
                expect.stringContaining("Masjid Al-Ikhlas"),
                expect.objectContaining({
                    sticky: true,
                    direction: "top",
                }),
            );
        });

        it("applies non-solid patterns", () => {
            const mgr = new LayerManager(engine);
            mgr.renderAll(customLayersData);

            expect(
                engine.patterns.applyCustomLayerPattern,
            ).toHaveBeenCalledWith(
                "area-hijau",
                "hatch",
                "#22c55e",
                0.2,
                expect.any(Object),
            );
        });

        it("skips layers with no features", () => {
            const emptyLayer = {
                ...customLayersData[0],
                id: 3,
                geojson: { type: "FeatureCollection", features: [] },
            };
            const mgr = new LayerManager(engine);
            mgr.renderAll([emptyLayer]);

            expect(mgr.customLayers[0].polygonCount).toBe(0);
        });
    });

    // ══════════════════════════════════════════════════════
    // renderAll — all layers combined
    // ══════════════════════════════════════════════════════
    describe("renderAll — all layers", () => {
        it("renders kelurahan + RW + custom in correct order", () => {
            const mgr = new LayerManager(engine);
            mgr.renderAll(allLayersData);

            expect(mgr.kelurahanLayer).not.toBeNull();
            expect(mgr.rwLayer).not.toBeNull();
            expect(mgr.customLayers).toHaveLength(2);
            expect(mgr.allLayers).toHaveLength(4);
        });

        it("calls onLayersReady callback", () => {
            const onLayersReady = vi.fn();
            const mgr = new LayerManager(engine, { onLayersReady });
            mgr.renderAll(allLayersData);

            expect(onLayersReady).toHaveBeenCalledWith(mgr.allLayers);
        });
    });

    // ══════════════════════════════════════════════════════
    // load — from URL
    // ══════════════════════════════════════════════════════
    describe("load", () => {
        it("fetches data and renders all layers", async () => {
            globalThis.fetch.mockResolvedValue({
                ok: true,
                json: () => Promise.resolve(allLayersData),
            });

            const mgr = new LayerManager(engine);
            const result = await mgr.load("/peta/geojson/layers");

            expect(result).toBe(mgr);
            expect(mgr.kelurahanLayer).not.toBeNull();
            expect(mgr.rwDataList).toHaveLength(2);
            expect(mgr.customLayers).toHaveLength(2);
        });

        it("handles fetch errors gracefully", async () => {
            globalThis.fetch.mockRejectedValue(new Error("Network error"));

            const errorSpy = vi.spyOn(console, "error").mockImplementation();
            const mgr = new LayerManager(engine);
            await mgr.load("/peta/geojson/layers");

            expect(errorSpy).toHaveBeenCalled();
            expect(mgr.kelurahanLayer).toBeNull();
        });
    });

    // ══════════════════════════════════════════════════════
    // RW select / deselect
    // ══════════════════════════════════════════════════════
    describe("selectRw", () => {
        it("highlights selected RW and calls onRwSelect", () => {
            const onRwSelect = vi.fn();
            const mgr = new LayerManager(engine, { onRwSelect });
            mgr.renderAll([rwLayerData]);

            const mockLayer = globalThis.__mockLayer();
            mockLayer.feature = { properties: { RW: "RW 01" } };
            mgr.rwLayerMap["RW 01"] = mockLayer;

            mgr.selectRw("RW 01");

            expect(mgr.selectedRw).toBe("RW 01");
            expect(onRwSelect).toHaveBeenCalledWith(
                "RW 01",
                expect.any(Object),
            );
        });

        it("deselects when selecting the same RW again", () => {
            const onRwDeselect = vi.fn();
            const mgr = new LayerManager(engine, { onRwDeselect });
            mgr.renderAll([rwLayerData]);

            const mockLayer = globalThis.__mockLayer();
            mockLayer.feature = { properties: { RW: "RW 01" } };
            mgr.rwLayerMap["RW 01"] = mockLayer;

            mgr.selectRw("RW 01");
            mgr.selectRw("RW 01");

            expect(mgr.selectedRw).toBeNull();
            expect(onRwDeselect).toHaveBeenCalled();
        });

        it("does nothing for non-existent RW", () => {
            const mgr = new LayerManager(engine);
            mgr.renderAll([rwLayerData]);
            mgr.selectRw("RW 99");
            expect(mgr.selectedRw).toBeNull();
        });
    });

    describe("deselectRw", () => {
        it("clears selection and calls onRwDeselect", () => {
            const onRwDeselect = vi.fn();
            const mgr = new LayerManager(engine, { onRwDeselect });

            const mockLayer = globalThis.__mockLayer();
            mockLayer.feature = { properties: { RW: "RW 01" } };
            mgr.rwLayerMap["RW 01"] = mockLayer;
            mgr.selectedRw = "RW 01";
            mgr._highlightedRw = mockLayer;

            mgr.deselectRw();

            expect(mgr.selectedRw).toBeNull();
            expect(mgr._highlightedRw).toBeNull();
            expect(onRwDeselect).toHaveBeenCalled();
        });
    });

    // ══════════════════════════════════════════════════════
    // Toggle — kelurahan
    // ══════════════════════════════════════════════════════
    describe("toggleKelurahan", () => {
        it("hides the layer", () => {
            const mgr = new LayerManager(engine);
            mgr.renderAll([kelurahanLayerData]);
            mgr.toggleKelurahan(false);

            expect(mgr.showKelurahan).toBe(false);
            expect(engine.map.removeLayer).toHaveBeenCalled();
        });

        it("shows the layer", () => {
            const mgr = new LayerManager(engine);
            mgr.renderAll([kelurahanLayerData]);
            mgr.showKelurahan = false;
            mgr.toggleKelurahan(true);

            expect(mgr.showKelurahan).toBe(true);
            expect(engine.map.addLayer).toHaveBeenCalled();
        });

        it("auto-toggles when no arg", () => {
            const mgr = new LayerManager(engine);
            mgr.renderAll([kelurahanLayerData]);
            mgr.toggleKelurahan();
            expect(mgr.showKelurahan).toBe(false);
        });

        it("is safe when layer is not rendered", () => {
            const mgr = new LayerManager(engine);
            expect(() => mgr.toggleKelurahan(false)).not.toThrow();
        });
    });

    // ══════════════════════════════════════════════════════
    // Toggle — RW
    // ══════════════════════════════════════════════════════
    describe("toggleRw", () => {
        it("hides RW polygons and labels", () => {
            const mgr = new LayerManager(engine);
            mgr.renderAll([rwLayerData]);
            mgr.toggleRw(false);

            expect(mgr.showRw).toBe(false);
            expect(engine.map.removeLayer).toHaveBeenCalled();
        });

        it("shows RW polygons and labels", () => {
            const mgr = new LayerManager(engine);
            mgr.renderAll([rwLayerData]);
            mgr.showRw = false;
            mgr.toggleRw(true);

            expect(mgr.showRw).toBe(true);
            expect(engine.map.addLayer).toHaveBeenCalled();
        });

        it("auto-toggles when no arg", () => {
            const mgr = new LayerManager(engine);
            mgr.renderAll([rwLayerData]);
            mgr.toggleRw();
            expect(mgr.showRw).toBe(false);
        });
    });

    // ══════════════════════════════════════════════════════
    // Toggle — RW labels
    // ══════════════════════════════════════════════════════
    describe("toggleRwLabels", () => {
        it("hides labels", () => {
            const mgr = new LayerManager(engine);
            mgr.renderAll([rwLayerData]);
            mgr.toggleRwLabels(false);
            expect(mgr.showRwLabels).toBe(false);
        });

        it("shows labels", () => {
            const mgr = new LayerManager(engine);
            mgr.renderAll([rwLayerData]);
            mgr.showRwLabels = false;
            mgr.toggleRwLabels(true);
            expect(mgr.showRwLabels).toBe(true);
        });

        it("auto-toggles when no arg", () => {
            const mgr = new LayerManager(engine);
            mgr.renderAll([rwLayerData]);
            mgr.toggleRwLabels();
            expect(mgr.showRwLabels).toBe(false);
        });
    });

    // ══════════════════════════════════════════════════════
    // Toggle — custom layers
    // ══════════════════════════════════════════════════════
    describe("toggleCustomLayer", () => {
        it("hides a visible custom layer", () => {
            const mgr = new LayerManager(engine);
            mgr.renderAll(customLayersData);

            mgr.toggleCustomLayer(1);
            expect(mgr.customLayers[0].visible).toBe(false);
            expect(engine.map.removeLayer).toHaveBeenCalled();
        });

        it("shows a hidden custom layer", () => {
            const mgr = new LayerManager(engine);
            mgr.renderAll(customLayersData);

            mgr.toggleCustomLayer(1);
            mgr.toggleCustomLayer(1);
            expect(mgr.customLayers[0].visible).toBe(true);
            expect(engine.map.addLayer).toHaveBeenCalled();
        });

        it("does nothing for unknown layer id", () => {
            const mgr = new LayerManager(engine);
            expect(() => mgr.toggleCustomLayer(999)).not.toThrow();
        });
    });

    // ══════════════════════════════════════════════════════
    // Z-order
    // ══════════════════════════════════════════════════════
    describe("bringCustomToFront", () => {
        it("brings visible custom layers to front", () => {
            const parentBringToFront = vi.fn();
            const childBringToFront = vi.fn();

            L.geoJSON.mockImplementation((data, opts) => {
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

            const mgr = new LayerManager(engine);
            mgr.renderAll(customLayersData);
            mgr.bringCustomToFront();

            expect(parentBringToFront).toHaveBeenCalled();
            expect(childBringToFront).toHaveBeenCalled();
        });
    });

    describe("bringKelurahanToFront", () => {
        it("calls bringToFront on kelurahan layer", () => {
            const mgr = new LayerManager(engine);
            mgr.renderAll([kelurahanLayerData]);
            mgr.bringKelurahanToFront();
            expect(mgr.kelurahanLayer.bringToFront).toHaveBeenCalled();
        });

        it("is safe when layer is null", () => {
            const mgr = new LayerManager(engine);
            expect(() => mgr.bringKelurahanToFront()).not.toThrow();
        });
    });

    // ══════════════════════════════════════════════════════
    // Destroy
    // ══════════════════════════════════════════════════════
    describe("destroy", () => {
        it("cleans up all layers", () => {
            const mgr = new LayerManager(engine);
            mgr.renderAll(allLayersData);

            mgr.destroy();

            expect(mgr.kelurahanLayer).toBeNull();
            expect(mgr.kelurahanBounds).toBeNull();
            expect(mgr.rwLayer).toBeNull();
            expect(mgr.rwLabelLayer).toBeNull();
            expect(mgr.rwLayerMap).toEqual({});
            expect(mgr.rwDataList).toEqual([]);
            expect(mgr.customLayers).toEqual([]);
            expect(mgr._customMapLayers).toEqual({});
            expect(mgr.allLayers).toEqual([]);
        });
    });
});
