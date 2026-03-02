/**
 * Feature tests — PolygonEditor integration scenarios.
 */
import { describe, it, expect, beforeEach, vi } from "vitest";
import PolygonEditor from "../../editors/PolygonEditor";

describe("Feature: Polygon Editor Workflows", () => {
    beforeEach(() => {
        vi.clearAllMocks();
        const container = document.getElementById("map");
        if (container) delete container._leaflet_id;
    });

    // ── Feature: RW Polygon Editing ─────────────────────────
    describe("RW Polygon Editing", () => {
        it("initializes editor and loads existing polygon", () => {
            const editor = new PolygonEditor("map", {
                color: "#6366f1",
            }).init();

            expect(editor.map).not.toBeNull();
            expect(editor.drawnItems).not.toBeNull();

            const geojson = {
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
            editor.loadExisting(geojson, "#6366f1");

            // drawnItems should have layers now
            expect(L.geoJSON).toHaveBeenCalled();
        });

        it("tracks change state with onSinglePolygonChange", () => {
            const editor = new PolygonEditor("map").init();
            const changeHandler = vi.fn();

            editor.onSinglePolygonChange(changeHandler);

            // Verify events were bound
            expect(editor.map.on).toHaveBeenCalledWith(
                "draw:created",
                expect.any(Function),
            );
        });

        it("returns geometry from drawn layer", () => {
            const editor = new PolygonEditor("map").init();

            // Mock drawnItems with a layer
            const mockLayer = globalThis.__mockLayer();
            editor.drawnItems.getLayers.mockReturnValue([mockLayer]);

            const geom = editor.getGeometry();
            expect(geom).toEqual({
                type: "Polygon",
                coordinates: expect.any(Array),
            });
        });

        it("returns null geometry when no shapes drawn", () => {
            const editor = new PolygonEditor("map").init();
            editor.drawnItems.getLayers.mockReturnValue([]);

            const geom = editor.getGeometry();
            expect(geom).toBeNull();
        });

        it("returns null geometry when drawnItems is null", () => {
            const editor = new PolygonEditor("map");
            // drawnItems is null before init()
            expect(editor.drawnItems).toBeNull();
            expect(editor.getGeometry()).toBeNull();
        });
    });

    // ── Feature: Kelurahan Reference Boundary ───────────────
    describe("Kelurahan Reference in Editor", () => {
        it("adds kelurahan boundary as reference layer", () => {
            const editor = new PolygonEditor("map").init();
            const geojson = {
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
            };

            editor.addKelurahan(geojson);

            expect(L.geoJSON).toHaveBeenCalledWith(
                expect.any(Object),
                expect.objectContaining({
                    pane: "basePane",
                    style: expect.objectContaining({
                        dashArray: "10, 6",
                    }),
                }),
            );
            expect(editor.kelurahanLayer).not.toBeNull();
        });

        it("handles null geojson gracefully", () => {
            const editor = new PolygonEditor("map").init();
            expect(() => editor.addKelurahan(null)).not.toThrow();
            expect(editor.kelurahanLayer).toBeNull();
        });
    });

    // ── Feature: RW Reference Overlay ───────────────────────
    describe("RW Reference Overlay", () => {
        it("adds RW polygons as faded reference", () => {
            const editor = new PolygonEditor("map").init();
            const polygons = [
                {
                    label: "RW 01",
                    warna: "#6366f1",
                    geojson: {
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
                    label: "RW 02",
                    warna: "#ef4444",
                    geojson: {
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
            ];

            editor.addRwReference(polygons);

            expect(editor.referenceLayer).not.toBeNull();
        });

        it("toggles RW overlay visibility", async () => {
            const editor = new PolygonEditor("map").init();
            editor.rwOverlay = L.layerGroup();

            editor.toggleRwOverlay(false);
            expect(editor.map.removeLayer).toHaveBeenCalled();

            editor.toggleRwOverlay(true);
            expect(editor.map.addLayer).toHaveBeenCalled();
        });
    });

    // ── Feature: GeoJSON Sanitisation Pipeline ──────────────
    describe("GeoJSON Sanitisation Pipeline", () => {
        let editor;
        beforeEach(() => {
            editor = new PolygonEditor("map");
        });

        it("handles MySQL-generated degenerate GeoJSON", () => {
            // MySQL ST_AsGeoJSON sometimes emits null coords
            const degenerate = {
                type: "Polygon",
                coordinates: [
                    [
                        [119.46, -5.15],
                        null,
                        [119.47, -5.15],
                        [null, null],
                        [119.47, -5.16],
                        [119.46, -5.15],
                    ],
                ],
            };

            const result = editor._sanitiseGeojson(degenerate);
            expect(result).not.toBeNull();
            expect(result.coordinates[0]).not.toContainEqual(null);
            result.coordinates[0].forEach((coord) => {
                expect(coord[0]).not.toBeNull();
                expect(coord[1]).not.toBeNull();
            });
        });

        it("flattens single-polygon MultiPolygon for Leaflet.Draw compatibility", () => {
            const multi = {
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

            const result = editor._sanitiseGeojson(multi);
            expect(result.type).toBe("Polygon");
            expect(result.coordinates[0]).toHaveLength(4);
        });

        it("preserves valid MultiPolygon with multiple polygons", () => {
            const multi = {
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

            const result = editor._sanitiseGeojson(multi);
            expect(result.type).toBe("MultiPolygon");
            expect(result.coordinates).toHaveLength(2);
        });

        it("filters out rings with < 3 valid coordinates", () => {
            const geo = {
                type: "Polygon",
                coordinates: [
                    // Valid ring
                    [
                        [119.46, -5.15],
                        [119.47, -5.15],
                        [119.47, -5.16],
                    ],
                    // Too-short ring (will be removed)
                    [
                        [119.48, -5.15],
                        [119.49, -5.15],
                    ],
                ],
            };
            const result = editor._sanitiseGeojson(geo);
            expect(result.coordinates).toHaveLength(1);
        });

        it("rejects Infinity coordinates", () => {
            const geo = {
                type: "Polygon",
                coordinates: [
                    [
                        [Infinity, -5.15],
                        [119.47, Infinity],
                        [119.47, -5.16],
                        [119.46, -5.15],
                    ],
                ],
            };
            const result = editor._sanitiseGeojson(geo);
            // Only 2 valid coords after filtering, ring dropped (< 3)
            expect(result).toBeNull();
        });
    });

    // ── Feature: Color Management ───────────────────────────
    describe("Color Management", () => {
        it("updates color of all drawn items", () => {
            const editor = new PolygonEditor("map").init();
            const mockLayer = globalThis.__mockLayer();
            editor.drawnItems.getLayers.mockReturnValue([mockLayer]);
            editor.drawnItems.eachLayer.mockImplementation((cb) =>
                cb(mockLayer),
            );

            editor.updateColor("#ff6600");

            expect(editor.options.color).toBe("#ff6600");
            expect(mockLayer.setStyle).toHaveBeenCalledWith({
                color: "#ff6600",
                fillColor: "#ff6600",
            });
        });
    });

    // ── Feature: Multi-Polygon Editor (Custom Layers) ───────
    describe("Custom Layer Multi-Polygon Editor", () => {
        it("loads existing feature collection into editor", () => {
            const editor = new PolygonEditor("map").init();
            const collection = {
                type: "FeatureCollection",
                features: [
                    {
                        type: "Feature",
                        properties: { id: 1, nama: "Area A" },
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
                        properties: { id: 2, nama: "Area B" },
                        geometry: {
                            type: "Polygon",
                            coordinates: [
                                [
                                    [119.48, -5.15],
                                    [119.49, -5.15],
                                    [119.49, -5.16],
                                    [119.48, -5.15],
                                ],
                            ],
                        },
                    },
                ],
            };

            const list = editor.loadExistingCollection(collection);
            // L.geoJSON is called with the collection
            expect(L.geoJSON).toHaveBeenCalled();
        });

        it("binds multi-polygon change handlers", () => {
            const editor = new PolygonEditor("map").init();
            const handlers = {
                onCreated: vi.fn(),
                onEdited: vi.fn(),
                onDeleted: vi.fn(),
            };

            editor.onMultiPolygonChange(handlers);

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
    });
});
