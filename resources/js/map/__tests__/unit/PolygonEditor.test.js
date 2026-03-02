/**
 * Unit tests for editors/PolygonEditor.js
 */
import { describe, it, expect, beforeEach, vi } from "vitest";
import PolygonEditor from "../../editors/PolygonEditor";

describe("PolygonEditor", () => {
    beforeEach(() => {
        vi.clearAllMocks();
        const container = document.getElementById("map");
        if (container) delete container._leaflet_id;
    });

    // ── Constructor ─────────────────────────────────────────
    describe("constructor", () => {
        it("stores containerId and options", () => {
            const editor = new PolygonEditor("map", { color: "#ff0000" });
            expect(editor.containerId).toBe("map");
            expect(editor.options.color).toBe("#ff0000");
        });

        it("uses default options", () => {
            const editor = new PolygonEditor("map");
            expect(editor.options.color).toBe("#6366f1");
            expect(editor.options.weight).toBe(3);
            expect(editor.options.fillOpacity).toBe(0.35);
            expect(editor.options.rectangle).toBe(false);
        });

        it("starts with null state", () => {
            const editor = new PolygonEditor("map");
            expect(editor.map).toBeNull();
            expect(editor.drawnItems).toBeNull();
            expect(editor.drawControl).toBeNull();
            expect(editor.hasChanges).toBe(false);
        });
    });

    // ── init ────────────────────────────────────────────────
    describe("init", () => {
        it("creates map with draw controls", () => {
            const editor = new PolygonEditor("map").init();
            expect(L.map).toHaveBeenCalledWith(
                "map",
                expect.objectContaining({ zoomControl: true }),
            );
            expect(editor.map).not.toBeNull();
            expect(editor.drawnItems).not.toBeNull();
        });

        it("returns this for chaining", () => {
            const editor = new PolygonEditor("map");
            expect(editor.init()).toBe(editor);
        });

        it("creates custom panes (basePane, customLayerPane, editPane)", () => {
            const editor = new PolygonEditor("map").init();
            expect(editor.map.createPane).toHaveBeenCalledWith("basePane");
            expect(editor.map.createPane).toHaveBeenCalledWith(
                "customLayerPane",
            );
            expect(editor.map.createPane).toHaveBeenCalledWith("editPane");
        });

        it("warns and returns this when Leaflet is not loaded", () => {
            const origL = globalThis.L;
            delete globalThis.L;

            const warnSpy = vi.spyOn(console, "warn").mockImplementation();
            const editor = new PolygonEditor("map").init();
            expect(warnSpy).toHaveBeenCalled();
            expect(editor.map).toBeNull();

            globalThis.L = origL;
        });

        it("warns and returns this for missing container", () => {
            const warnSpy = vi.spyOn(console, "warn").mockImplementation();
            const editor = new PolygonEditor("nonexistent").init();
            expect(warnSpy).toHaveBeenCalled();
            expect(editor.map).toBeNull();
        });
    });

    // ── destroy ─────────────────────────────────────────────
    describe("destroy", () => {
        it("removes the map", () => {
            const editor = new PolygonEditor("map").init();
            const map = editor.map;
            editor.destroy();
            expect(map.remove).toHaveBeenCalled();
            expect(editor.map).toBeNull();
        });
    });

    // ── _sanitiseGeojson ────────────────────────────────────
    describe("_sanitiseGeojson", () => {
        let editor;

        beforeEach(() => {
            editor = new PolygonEditor("map");
        });

        it("returns null for null input", () => {
            expect(editor._sanitiseGeojson(null)).toBeNull();
        });

        it("returns null for empty object", () => {
            expect(editor._sanitiseGeojson({})).toBeNull();
        });

        it("handles valid Polygon", () => {
            const geo = {
                type: "Polygon",
                coordinates: [
                    [
                        [119.46, -5.15],
                        [119.47, -5.15],
                        [119.47, -5.16],
                        [119.46, -5.15],
                    ],
                ],
            };
            const result = editor._sanitiseGeojson(geo);
            expect(result.type).toBe("Polygon");
            expect(result.coordinates[0]).toHaveLength(4);
        });

        it("flattens single-element MultiPolygon to Polygon", () => {
            const geo = {
                type: "MultiPolygon",
                coordinates: [
                    [
                        [
                            [119.46, -5.15],
                            [119.47, -5.15],
                            [119.47, -5.16],
                            [119.46, -5.15],
                        ],
                    ],
                ],
            };
            const result = editor._sanitiseGeojson(geo);
            expect(result.type).toBe("Polygon");
        });

        it("keeps multi-element MultiPolygon", () => {
            const geo = {
                type: "MultiPolygon",
                coordinates: [
                    [
                        [
                            [119.46, -5.15],
                            [119.47, -5.15],
                            [119.47, -5.16],
                            [119.46, -5.15],
                        ],
                    ],
                    [
                        [
                            [119.48, -5.15],
                            [119.49, -5.15],
                            [119.49, -5.16],
                            [119.48, -5.15],
                        ],
                    ],
                ],
            };
            const result = editor._sanitiseGeojson(geo);
            expect(result.type).toBe("MultiPolygon");
            expect(result.coordinates).toHaveLength(2);
        });

        it("removes null coordinates", () => {
            const geo = {
                type: "Polygon",
                coordinates: [
                    [
                        [119.46, -5.15],
                        null,
                        [119.47, -5.15],
                        [119.47, -5.16],
                        [119.46, -5.15],
                    ],
                ],
            };
            const result = editor._sanitiseGeojson(geo);
            expect(result.coordinates[0]).toHaveLength(4);
            expect(result.coordinates[0]).not.toContain(null);
        });

        it("removes coordinates with null values", () => {
            const geo = {
                type: "Polygon",
                coordinates: [
                    [
                        [119.46, -5.15],
                        [null, -5.15],
                        [119.47, null],
                        [119.47, -5.16],
                        [119.46, -5.15],
                    ],
                ],
            };
            const result = editor._sanitiseGeojson(geo);
            expect(result.coordinates[0]).toHaveLength(3);
        });

        it("returns null when all coordinates are invalid", () => {
            const geo = {
                type: "Polygon",
                coordinates: [[null, [null, null], [Infinity, -5]]],
            };
            const result = editor._sanitiseGeojson(geo);
            expect(result).toBeNull();
        });
    });

    // ── _sanitiseRings ──────────────────────────────────────
    describe("_sanitiseRings", () => {
        let editor;
        beforeEach(() => {
            editor = new PolygonEditor("map");
        });

        it("filters out short rings (< 3 points)", () => {
            const rings = [
                [
                    [119.46, -5.15],
                    [119.47, -5.15],
                ],
            ];
            const result = editor._sanitiseRings(rings);
            expect(result).toHaveLength(0);
        });

        it("keeps valid rings (>= 3 points)", () => {
            const rings = [
                [
                    [119.46, -5.15],
                    [119.47, -5.15],
                    [119.47, -5.16],
                ],
            ];
            const result = editor._sanitiseRings(rings);
            expect(result).toHaveLength(1);
        });

        it("returns empty array for non-array input", () => {
            expect(editor._sanitiseRings(null)).toEqual([]);
            expect(editor._sanitiseRings("string")).toEqual([]);
        });
    });

    // ── _hasValidLatLngs ────────────────────────────────────
    describe("_hasValidLatLngs", () => {
        let editor;
        beforeEach(() => {
            editor = new PolygonEditor("map");
        });

        it("returns true for valid LatLng array", () => {
            const lls = [
                { lat: -5.15, lng: 119.46 },
                { lat: -5.16, lng: 119.47 },
            ];
            expect(editor._hasValidLatLngs(lls)).toBe(true);
        });

        it("returns false for null", () => {
            expect(editor._hasValidLatLngs(null)).toBe(false);
        });

        it("returns false for empty array", () => {
            expect(editor._hasValidLatLngs([])).toBe(false);
        });

        it("returns false when a LatLng has null lat", () => {
            const lls = [
                { lat: -5.15, lng: 119.46 },
                { lat: null, lng: 119.47 },
            ];
            expect(editor._hasValidLatLngs(lls)).toBe(false);
        });

        it("handles nested ring arrays", () => {
            const lls = [
                [
                    { lat: -5.15, lng: 119.46 },
                    { lat: -5.16, lng: 119.47 },
                ],
            ];
            expect(editor._hasValidLatLngs(lls)).toBe(true);
        });
    });

    // ── Event binding ───────────────────────────────────────
    describe("onSinglePolygonChange", () => {
        it("binds draw events on the map", () => {
            const editor = new PolygonEditor("map").init();
            const onChange = vi.fn();
            editor.onSinglePolygonChange(onChange);
            expect(editor.map.on).toHaveBeenCalledWith(
                "draw:created",
                expect.any(Function),
            );
            expect(editor.map.on).toHaveBeenCalledWith(
                "draw:edited",
                expect.any(Function),
            );
            expect(editor.map.on).toHaveBeenCalledWith(
                "draw:deleted",
                expect.any(Function),
            );
        });

        it("warns when map is null", () => {
            const warnSpy = vi.spyOn(console, "warn").mockImplementation();
            const editor = new PolygonEditor("map");
            editor.onSinglePolygonChange(vi.fn());
            expect(warnSpy).toHaveBeenCalled();
        });
    });

    describe("onMultiPolygonChange", () => {
        it("binds draw events with handlers", () => {
            const editor = new PolygonEditor("map").init();
            editor.onMultiPolygonChange({
                onCreated: vi.fn(),
                onEdited: vi.fn(),
                onDeleted: vi.fn(),
            });
            expect(editor.map.on).toHaveBeenCalledTimes(3);
        });
    });

    // ── updateColor ─────────────────────────────────────────
    describe("updateColor", () => {
        it("updates options color", () => {
            const editor = new PolygonEditor("map").init();
            editor.updateColor("#ff0000");
            expect(editor.options.color).toBe("#ff0000");
        });
    });

    // ── getGeometry ─────────────────────────────────────────
    describe("getGeometry", () => {
        it("returns null when drawnItems is null", () => {
            const editor = new PolygonEditor("map");
            expect(editor.getGeometry()).toBeNull();
        });

        it("returns null when no layers drawn", () => {
            const editor = new PolygonEditor("map").init();
            editor.drawnItems.getLayers.mockReturnValue([]);
            expect(editor.getGeometry()).toBeNull();
        });

        it("returns geometry from first drawn layer", () => {
            const editor = new PolygonEditor("map").init();
            const mockLayer = globalThis.__mockLayer();
            editor.drawnItems.getLayers.mockReturnValue([mockLayer]);
            const geom = editor.getGeometry();
            expect(geom).toEqual({
                type: "Polygon",
                coordinates: expect.any(Array),
            });
        });
    });

    // ── clearDrawn ──────────────────────────────────────────
    describe("clearDrawn", () => {
        it("clears all drawn items", () => {
            const editor = new PolygonEditor("map").init();
            editor.clearDrawn();
            expect(editor.drawnItems.clearLayers).toHaveBeenCalled();
        });

        it("is safe when drawnItems is null", () => {
            const editor = new PolygonEditor("map");
            expect(() => editor.clearDrawn()).not.toThrow();
        });
    });
});
