/**
 * Unit tests for layers/KelurahanLayer.js
 */
import { describe, it, expect, beforeEach, vi } from "vitest";
import KelurahanLayer from "../../layers/KelurahanLayer";

describe("KelurahanLayer", () => {
    let engine;

    const sampleGeojson = {
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
    };

    beforeEach(() => {
        vi.clearAllMocks();
        engine = {
            map: globalThis.__mockMap(),
            svgRenderer: { _container: document.createElement("div") },
        };
    });

    // ── Constructor ─────────────────────────────────────────
    describe("constructor", () => {
        it("stores engine reference", () => {
            const layer = new KelurahanLayer(engine);
            expect(layer.engine).toBe(engine);
        });

        it("starts with null layer and bounds", () => {
            const layer = new KelurahanLayer(engine);
            expect(layer.layer).toBeNull();
            expect(layer.bounds).toBeNull();
        });

        it("starts visible", () => {
            const layer = new KelurahanLayer(engine);
            expect(layer.visible).toBe(true);
        });
    });

    // ── render ──────────────────────────────────────────────
    describe("render", () => {
        it("creates L.geoJSON with boundary style", () => {
            const kelLayer = new KelurahanLayer(engine);
            kelLayer.render(sampleGeojson);

            expect(L.geoJSON).toHaveBeenCalledWith(
                sampleGeojson,
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
            expect(kelLayer.layer).not.toBeNull();
            expect(kelLayer.bounds).not.toBeNull();
        });

        it("uses shared svgRenderer when available", () => {
            const kelLayer = new KelurahanLayer(engine);
            kelLayer.render(sampleGeojson);

            expect(L.geoJSON).toHaveBeenCalledWith(
                sampleGeojson,
                expect.objectContaining({
                    renderer: engine.svgRenderer,
                }),
            );
        });
    });

    // ── load ────────────────────────────────────────────────
    describe("load", () => {
        it("fetches GeoJSON and calls render", async () => {
            globalThis.fetch.mockResolvedValue({
                ok: true,
                json: () => Promise.resolve(sampleGeojson),
            });

            const kelLayer = new KelurahanLayer(engine);
            const result = await kelLayer.load("/peta/geojson/kelurahan");

            expect(result).toBe(kelLayer);
            expect(globalThis.fetch).toHaveBeenCalledWith(
                "/peta/geojson/kelurahan",
                expect.any(Object),
            );
        });

        it("handles error response gracefully", async () => {
            globalThis.fetch.mockResolvedValue({
                ok: true,
                json: () => Promise.resolve({ error: "Data not ready" }),
            });

            const warnSpy = vi.spyOn(console, "warn").mockImplementation();
            const kelLayer = new KelurahanLayer(engine);
            const result = await kelLayer.load("/peta/geojson/kelurahan");

            expect(result).toBe(kelLayer);
            expect(kelLayer.layer).toBeNull();
        });
    });

    // ── toggle ──────────────────────────────────────────────
    describe("toggle", () => {
        it("hides the layer when visible", () => {
            const kelLayer = new KelurahanLayer(engine);
            kelLayer.render(sampleGeojson);
            kelLayer.toggle(false);

            expect(kelLayer.visible).toBe(false);
            expect(engine.map.removeLayer).toHaveBeenCalled();
        });

        it("shows the layer when hidden", () => {
            const kelLayer = new KelurahanLayer(engine);
            kelLayer.render(sampleGeojson);
            kelLayer.visible = false;
            kelLayer.toggle(true);

            expect(kelLayer.visible).toBe(true);
            expect(engine.map.addLayer).toHaveBeenCalled();
        });

        it("toggles when no arg is provided", () => {
            const kelLayer = new KelurahanLayer(engine);
            kelLayer.render(sampleGeojson);
            expect(kelLayer.visible).toBe(true);

            kelLayer.toggle();
            expect(kelLayer.visible).toBe(false);
        });

        it("is safe when layer is not yet rendered", () => {
            const kelLayer = new KelurahanLayer(engine);
            expect(() => kelLayer.toggle(false)).not.toThrow();
        });
    });

    // ── bringToFront ────────────────────────────────────────
    describe("bringToFront", () => {
        it("calls bringToFront on the layer", () => {
            const kelLayer = new KelurahanLayer(engine);
            kelLayer.render(sampleGeojson);
            kelLayer.bringToFront();
            expect(kelLayer.layer.bringToFront).toHaveBeenCalled();
        });

        it("is safe when layer is null", () => {
            const kelLayer = new KelurahanLayer(engine);
            expect(() => kelLayer.bringToFront()).not.toThrow();
        });
    });
});
