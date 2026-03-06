/**
 * System tests — Simulate fetching ALL data from database via API endpoints.
 *
 * These tests verify the full data-fetching pipeline from API call
 * through to layer rendering, covering all database endpoints:
 *   1. GET /peta/geojson/kelurahan  → Kelurahan boundary
 *   2. GET /peta/geojson/rw         → RW polygons + statistics
 *   3. GET /peta/stats              → Aggregate population stats
 *   4. GET /peta/geojson/layers     → Custom layers + polygons
 *   5. PUT /peta/rw/{id}/polygon    → Update RW polygon
 *   6. PUT /peta/rw/{id}/color      → Update RW color
 *   7. DELETE /peta/rw/{id}/polygon → Delete RW polygon
 *   8. Layer CRUD (JSON API)        → Create/Update/Delete layers
 *   9. Polygon CRUD (JSON API)      → Create/Update/Delete polygons
 */
import { describe, it, expect, beforeEach, vi } from "vitest";
import MapEngine from "../../MapEngine";
import LayerManager from "../../layers/LayerManager";
import {
    apiFetch,
    apiGet,
    apiPut,
    apiPost,
    apiDelete,
} from "../../utils/ApiClient";

// ── Mock Database Responses ─────────────────────────────────
// These mirror the exact JSON shapes returned by PetaController
// and PetaLayerController from the database.

const DB_KELURAHAN_GEOJSON = {
    type: "FeatureCollection",
    name: "lurah",
    crs: {
        type: "name",
        properties: { name: "urn:ogc:def:crs:OGC:1.3:CRS84" },
    },
    features: [
        {
            type: "Feature",
            properties: { id: 1 },
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

const DB_RW_GEOJSON = {
    type: "FeatureCollection",
    name: "batua1",
    crs: {
        type: "name",
        properties: { name: "urn:ogc:def:crs:OGC:1.3:CRS84" },
    },
    features: [
        {
            type: "Feature",
            properties: {
                id: 1,
                RW: "RW 01",
                warna: "#6366f1",
                total_penduduk: 523,
                total_kk: 134,
                total_rt: 5,
                total_umkm: 12,
                laki_laki: 267,
                perempuan: 256,
            },
            geometry: {
                type: "Polygon",
                coordinates: [
                    [
                        [119.45, -5.14],
                        [119.46, -5.14],
                        [119.46, -5.155],
                        [119.45, -5.155],
                        [119.45, -5.14],
                    ],
                ],
            },
        },
        {
            type: "Feature",
            properties: {
                id: 2,
                RW: "RW 02",
                warna: "#ef4444",
                total_penduduk: 389,
                total_kk: 95,
                total_rt: 4,
                total_umkm: 7,
                laki_laki: 192,
                perempuan: 197,
            },
            geometry: {
                type: "Polygon",
                coordinates: [
                    [
                        [119.46, -5.14],
                        [119.47, -5.14],
                        [119.47, -5.155],
                        [119.46, -5.155],
                        [119.46, -5.14],
                    ],
                ],
            },
        },
        {
            type: "Feature",
            properties: {
                id: 3,
                RW: "RW 03",
                warna: "#22c55e",
                total_penduduk: 412,
                total_kk: 103,
                total_rt: 6,
                total_umkm: 9,
                laki_laki: 210,
                perempuan: 202,
            },
            geometry: {
                type: "Polygon",
                coordinates: [
                    [
                        [119.47, -5.14],
                        [119.48, -5.14],
                        [119.48, -5.155],
                        [119.47, -5.155],
                        [119.47, -5.14],
                    ],
                ],
            },
        },
        {
            type: "Feature",
            properties: {
                id: 4,
                RW: "RW 04",
                warna: "#f59e0b",
                total_penduduk: 278,
                total_kk: 72,
                total_rt: 3,
                total_umkm: 4,
                laki_laki: 135,
                perempuan: 143,
            },
            geometry: {
                type: "Polygon",
                coordinates: [
                    [
                        [119.45, -5.155],
                        [119.46, -5.155],
                        [119.46, -5.17],
                        [119.45, -5.17],
                        [119.45, -5.155],
                    ],
                ],
            },
        },
        {
            type: "Feature",
            properties: {
                id: 5,
                RW: "RW 05",
                warna: "#8b5cf6",
                total_penduduk: 345,
                total_kk: 88,
                total_rt: 4,
                total_umkm: 6,
                laki_laki: 175,
                perempuan: 170,
            },
            geometry: {
                type: "Polygon",
                coordinates: [
                    [
                        [119.46, -5.155],
                        [119.47, -5.155],
                        [119.47, -5.17],
                        [119.46, -5.17],
                        [119.46, -5.155],
                    ],
                ],
            },
        },
        {
            type: "Feature",
            properties: {
                id: 6,
                RW: "RW 06",
                warna: "#ec4899",
                total_penduduk: 298,
                total_kk: 76,
                total_rt: 3,
                total_umkm: 5,
                laki_laki: 148,
                perempuan: 150,
            },
            geometry: {
                type: "Polygon",
                coordinates: [
                    [
                        [119.47, -5.155],
                        [119.48, -5.155],
                        [119.48, -5.17],
                        [119.47, -5.17],
                        [119.47, -5.155],
                    ],
                ],
            },
        },
    ],
};

const DB_STATS = {
    total_penduduk: 2245,
    total_kk: 568,
    total_rw: 6,
    total_rt: 25,
    total_umkm: 43,
    laki_laki: 1127,
    perempuan: 1118,
};

const DB_CUSTOM_LAYERS = [
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
                    properties: {
                        id: 1,
                        nama: "Masjid Al-Ikhlas",
                        deskripsi: "Masjid utama RW 01",
                    },
                    geometry: {
                        type: "Polygon",
                        coordinates: [
                            [
                                [119.455, -5.145],
                                [119.458, -5.145],
                                [119.458, -5.148],
                                [119.455, -5.148],
                                [119.455, -5.145],
                            ],
                        ],
                    },
                },
                {
                    type: "Feature",
                    properties: {
                        id: 2,
                        nama: "Gereja Bethany",
                        deskripsi: "Gereja di RW 03",
                    },
                    geometry: {
                        type: "Polygon",
                        coordinates: [
                            [
                                [119.472, -5.142],
                                [119.475, -5.142],
                                [119.475, -5.145],
                                [119.472, -5.145],
                                [119.472, -5.142],
                            ],
                        ],
                    },
                },
            ],
        },
    },
    {
        id: 2,
        nama: "Fasilitas Kesehatan",
        slug: "fasilitas-kesehatan",
        warna: "#ef4444",
        fill_opacity: 0.25,
        stroke_width: 1.5,
        pattern_type: "hatch",
        geojson: {
            type: "FeatureCollection",
            features: [
                {
                    type: "Feature",
                    properties: {
                        id: 3,
                        nama: "Puskesmas Batua",
                        deskripsi: "Puskesmas kelurahan",
                    },
                    geometry: {
                        type: "Polygon",
                        coordinates: [
                            [
                                [119.462, -5.152],
                                [119.466, -5.152],
                                [119.466, -5.156],
                                [119.462, -5.156],
                                [119.462, -5.152],
                            ],
                        ],
                    },
                },
            ],
        },
    },
    {
        id: 3,
        nama: "Sekolah",
        slug: "sekolah",
        warna: "#3b82f6",
        fill_opacity: 0.2,
        stroke_width: 2,
        pattern_type: "solid",
        geojson: {
            type: "FeatureCollection",
            features: [
                {
                    type: "Feature",
                    properties: {
                        id: 4,
                        nama: "SDN 01 Batua",
                        deskripsi: "Sekolah dasar negeri",
                    },
                    geometry: {
                        type: "Polygon",
                        coordinates: [
                            [
                                [119.468, -5.148],
                                [119.471, -5.148],
                                [119.471, -5.151],
                                [119.468, -5.151],
                                [119.468, -5.148],
                            ],
                        ],
                    },
                },
                {
                    type: "Feature",
                    properties: {
                        id: 5,
                        nama: "SMPN 02 Batua",
                        deskripsi: "Sekolah menengah pertama",
                    },
                    geometry: {
                        type: "Polygon",
                        coordinates: [
                            [
                                [119.475, -5.16],
                                [119.478, -5.16],
                                [119.478, -5.163],
                                [119.475, -5.163],
                                [119.475, -5.16],
                            ],
                        ],
                    },
                },
            ],
        },
    },
    {
        id: 4,
        nama: "UMKM Zona",
        slug: "umkm-zona",
        warna: "#f97316",
        fill_opacity: 0.15,
        stroke_width: 1,
        pattern_type: "crosshatch",
        geojson: {
            type: "FeatureCollection",
            features: [],
        },
    },
];

// ── Unified layer wrappers ──────────────────────────────────
const DB_KELURAHAN_LAYER = {
    id: 100,
    layer_type: "kelurahan",
    nama: "Kelurahan Batua",
    slug: "kelurahan-batua",
    warna: "#1e293b",
    fill_opacity: 0.02,
    stroke_width: 3,
    pattern_type: "solid",
    geojson: DB_KELURAHAN_GEOJSON,
};

const DB_RW_LAYER = {
    id: 200,
    layer_type: "rw",
    nama: "RW Layer",
    slug: "rw-layer",
    warna: "#6b7280",
    fill_opacity: 0.3,
    stroke_width: 2.5,
    pattern_type: "solid",
    geojson: DB_RW_GEOJSON,
};

const DB_CUSTOM_LAYER_DATA = DB_CUSTOM_LAYERS.map((l) => ({
    ...l,
    layer_type: "custom",
}));

const DB_ALL_LAYERS = [DB_KELURAHAN_LAYER, DB_RW_LAYER, ...DB_CUSTOM_LAYER_DATA];

// ── Helpers ─────────────────────────────────────────────────

function mockFetchOk(data) {
    globalThis.fetch.mockResolvedValue({
        ok: true,
        json: () => Promise.resolve(data),
        text: () => Promise.resolve(JSON.stringify(data)),
    });
}

function mockFetchSequence(...responses) {
    responses.forEach((data) => {
        globalThis.fetch.mockResolvedValueOnce({
            ok: true,
            json: () => Promise.resolve(data),
            text: () => Promise.resolve(JSON.stringify(data)),
        });
    });
}

function mockFetchError(status, body = "Server Error") {
    globalThis.fetch.mockResolvedValue({
        ok: false,
        status,
        json: () => Promise.reject(new Error("not json")),
        text: () => Promise.resolve(body),
    });
}

// ═══════════════════════════════════════════════════════════════
// SYSTEM TEST: Fetch Kelurahan Data from Database
// ═══════════════════════════════════════════════════════════════
describe("System: Fetch Kelurahan GeoJSON from Database", () => {
    let engine;

    beforeEach(() => {
        vi.clearAllMocks();
        const container = document.getElementById("map");
        if (container) delete container._leaflet_id;
        engine = new MapEngine("map").init();
    });

    it("renders kelurahan boundary as dashed polygon via renderAll", () => {
        const mgr = new LayerManager(engine);
        mgr.renderAll([DB_KELURAHAN_LAYER]);

        expect(mgr.kelurahanLayer).not.toBeNull();
        expect(mgr.kelurahanBounds).not.toBeNull();
        expect(mgr.showKelurahan).toBe(true);
    });

    it("loads all layers from unified endpoint including kelurahan", async () => {
        mockFetchOk(DB_ALL_LAYERS);

        const mgr = new LayerManager(engine);
        await mgr.load("/peta/geojson/layers");

        expect(globalThis.fetch).toHaveBeenCalledTimes(1);
        expect(globalThis.fetch).toHaveBeenCalledWith(
            "/peta/geojson/layers",
            expect.objectContaining({
                headers: expect.objectContaining({
                    "X-CSRF-TOKEN": "test-csrf-token-12345",
                    Accept: "application/json",
                    "Content-Type": "application/json",
                }),
            }),
        );

        expect(mgr.kelurahanLayer).not.toBeNull();
        expect(mgr.kelurahanBounds).not.toBeNull();
    });

    it("parses CRS header from database response", () => {
        const mgr = new LayerManager(engine);
        mgr.renderAll([DB_KELURAHAN_LAYER]);

        // L.geoJSON receives the full FeatureCollection including CRS
        expect(L.geoJSON).toHaveBeenCalledWith(
            expect.objectContaining({
                type: "FeatureCollection",
                crs: expect.objectContaining({
                    type: "name",
                    properties: expect.objectContaining({
                        name: "urn:ogc:def:crs:OGC:1.3:CRS84",
                    }),
                }),
            }),
            expect.any(Object),
        );
    });

    it("handles empty kelurahan features gracefully", () => {
        const emptyKelLayer = {
            ...DB_KELURAHAN_LAYER,
            geojson: { type: "FeatureCollection", features: [] },
        };

        const mgr = new LayerManager(engine);
        mgr.renderAll([emptyKelLayer]);

        expect(mgr.kelurahanLayer).toBeNull();
    });

    it("handles network failure on load", async () => {
        globalThis.fetch.mockRejectedValue(new Error("Network error"));

        const mgr = new LayerManager(engine);
        // load() catches errors internally
        await mgr.load("/peta/geojson/layers");

        expect(engine.map).not.toBeNull();
        expect(mgr.kelurahanLayer).toBeNull();
    });

    it("handles HTTP 500 on load", async () => {
        mockFetchError(500, "Internal Server Error");

        const mgr = new LayerManager(engine);
        // load() catches errors internally via apiGet which throws
        await mgr.load("/peta/geojson/layers");

        expect(engine.map).not.toBeNull();
        expect(mgr.kelurahanLayer).toBeNull();
    });
});

// ═══════════════════════════════════════════════════════════════
// SYSTEM TEST: Fetch RW Data from Database
// ═══════════════════════════════════════════════════════════════
describe("System: Fetch RW GeoJSON + Statistics from Database", () => {
    let engine;

    beforeEach(() => {
        vi.clearAllMocks();
        const container = document.getElementById("map");
        if (container) delete container._leaflet_id;
        engine = new MapEngine("map").init();
    });

    it("renders all 6 RW polygons with statistics via renderAll", () => {
        const mgr = new LayerManager(engine);
        mgr.renderAll([DB_RW_LAYER]);

        expect(mgr.rwLayer).not.toBeNull();
        expect(mgr.rwDataList).toHaveLength(6);
    });

    it("loads all layers from unified endpoint including RW", async () => {
        mockFetchOk(DB_ALL_LAYERS);

        const mgr = new LayerManager(engine);
        await mgr.load("/peta/geojson/layers");

        expect(globalThis.fetch).toHaveBeenCalledWith(
            "/peta/geojson/layers",
            expect.any(Object),
        );
        expect(mgr.rwLayer).not.toBeNull();
        expect(mgr.rwDataList).toHaveLength(6);
    });

    it("correctly maps database statistics to each RW", () => {
        const mgr = new LayerManager(engine);
        mgr.renderAll([DB_RW_LAYER]);

        const rw01 = mgr.rwDataList.find((d) => d.name === "RW 01");
        expect(rw01).toEqual(
            expect.objectContaining({
                name: "RW 01",
                warna: "#6366f1",
                total_penduduk: 523,
                total_kk: 134,
                total_rt: 5,
                total_umkm: 12,
                laki_laki: 267,
                perempuan: 256,
            }),
        );

        const rw04 = mgr.rwDataList.find((d) => d.name === "RW 04");
        expect(rw04).toEqual(
            expect.objectContaining({
                name: "RW 04",
                warna: "#f59e0b",
                total_penduduk: 278,
                total_kk: 72,
                total_rt: 3,
                total_umkm: 4,
                laki_laki: 135,
                perempuan: 143,
            }),
        );

        const rw06 = mgr.rwDataList.find((d) => d.name === "RW 06");
        expect(rw06).toEqual(
            expect.objectContaining({
                name: "RW 06",
                warna: "#ec4899",
                total_penduduk: 298,
                total_kk: 76,
                total_rt: 3,
                total_umkm: 5,
                laki_laki: 148,
                perempuan: 150,
            }),
        );
    });

    it("builds complete color map from database warna values", () => {
        const mgr = new LayerManager(engine);
        mgr.renderAll([DB_RW_LAYER]);

        expect(Object.keys(mgr.rwColors)).toHaveLength(6);
        expect(mgr.rwColors["RW 01"]).toBe("#6366f1");
        expect(mgr.rwColors["RW 02"]).toBe("#ef4444");
        expect(mgr.rwColors["RW 03"]).toBe("#22c55e");
        expect(mgr.rwColors["RW 04"]).toBe("#f59e0b");
        expect(mgr.rwColors["RW 05"]).toBe("#8b5cf6");
        expect(mgr.rwColors["RW 06"]).toBe("#ec4899");
    });

    it("fires onRwDataLoad callback with all RW data", () => {
        const onRwDataLoad = vi.fn();
        const mgr = new LayerManager(engine, { onRwDataLoad });
        mgr.renderAll([DB_RW_LAYER]);

        expect(onRwDataLoad).toHaveBeenCalledOnce();
        const loadedData = onRwDataLoad.mock.calls[0][0];
        expect(loadedData).toHaveLength(6);
        loadedData.forEach((item) => {
            expect(item).toHaveProperty("name");
            expect(item).toHaveProperty("warna");
            expect(item).toHaveProperty("total_penduduk");
            expect(item).toHaveProperty("total_kk");
            expect(item).toHaveProperty("total_rt");
            expect(item).toHaveProperty("total_umkm");
            expect(item).toHaveProperty("laki_laki");
            expect(item).toHaveProperty("perempuan");
        });
    });

    it("aggregates total population across all RW", () => {
        const mgr = new LayerManager(engine);
        mgr.renderAll([DB_RW_LAYER]);

        const totalPenduduk = mgr.rwDataList.reduce(
            (sum, d) => sum + d.total_penduduk,
            0,
        );
        const totalKK = mgr.rwDataList.reduce(
            (sum, d) => sum + d.total_kk,
            0,
        );
        const totalUmkm = mgr.rwDataList.reduce(
            (sum, d) => sum + d.total_umkm,
            0,
        );

        expect(totalPenduduk).toBe(2245);
        expect(totalKK).toBe(568);
        expect(totalUmkm).toBe(43);
    });

    it("verifies gender split consistency per RW", () => {
        const mgr = new LayerManager(engine);
        mgr.renderAll([DB_RW_LAYER]);

        mgr.rwDataList.forEach((item) => {
            expect(item.laki_laki + item.perempuan).toBe(item.total_penduduk);
        });
    });

    it("handles empty features array", () => {
        const emptyRwLayer = {
            ...DB_RW_LAYER,
            geojson: { type: "FeatureCollection", features: [] },
        };

        const mgr = new LayerManager(engine);
        mgr.renderAll([emptyRwLayer]);

        expect(mgr.rwDataList).toHaveLength(0);
        expect(Object.keys(mgr.rwColors)).toHaveLength(0);
    });
});

// ═══════════════════════════════════════════════════════════════
// SYSTEM TEST: Fetch Aggregate Stats from Database
// ═══════════════════════════════════════════════════════════════
describe("System: Fetch Aggregate Statistics from Database", () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it("fetches population statistics via /peta/stats", async () => {
        mockFetchOk(DB_STATS);

        const stats = await apiGet("/peta/stats");

        expect(globalThis.fetch).toHaveBeenCalledWith(
            "/peta/stats",
            expect.objectContaining({
                headers: expect.objectContaining({
                    Accept: "application/json",
                }),
            }),
        );

        expect(stats).toEqual({
            total_penduduk: 2245,
            total_kk: 568,
            total_rw: 6,
            total_rt: 25,
            total_umkm: 43,
            laki_laki: 1127,
            perempuan: 1118,
        });
    });

    it("stats gender adds up to total population", async () => {
        mockFetchOk(DB_STATS);

        const stats = await apiGet("/peta/stats");

        expect(stats.laki_laki + stats.perempuan).toBe(stats.total_penduduk);
    });

    it("all stat values are non-negative integers", async () => {
        mockFetchOk(DB_STATS);

        const stats = await apiGet("/peta/stats");

        Object.values(stats).forEach((val) => {
            expect(Number.isInteger(val)).toBe(true);
            expect(val).toBeGreaterThanOrEqual(0);
        });
    });

    it("stats contain all required keys", async () => {
        mockFetchOk(DB_STATS);

        const stats = await apiGet("/peta/stats");
        const requiredKeys = [
            "total_penduduk",
            "total_kk",
            "total_rw",
            "total_rt",
            "total_umkm",
            "laki_laki",
            "perempuan",
        ];

        requiredKeys.forEach((key) => {
            expect(stats).toHaveProperty(key);
        });
    });

    it("handles stats endpoint failure", async () => {
        mockFetchError(500, "Database connection failed");

        await expect(apiGet("/peta/stats")).rejects.toThrow("HTTP 500");
    });
});

// ═══════════════════════════════════════════════════════════════
// SYSTEM TEST: Fetch Custom Layers from Database
// ═══════════════════════════════════════════════════════════════
describe("System: Fetch Custom Layers + Polygons from Database", () => {
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

    it("renders all active custom layers via renderAll", () => {
        const mgr = new LayerManager(engine);
        mgr.renderAll(DB_CUSTOM_LAYER_DATA);

        expect(mgr.customLayers).toHaveLength(4);
    });

    it("loads all layers from unified endpoint including custom", async () => {
        mockFetchOk(DB_ALL_LAYERS);

        const mgr = new LayerManager(engine);
        await mgr.load("/peta/geojson/layers");

        expect(globalThis.fetch).toHaveBeenCalledWith(
            "/peta/geojson/layers",
            expect.any(Object),
        );

        expect(mgr.customLayers).toHaveLength(4);
    });

    it("maps all layer metadata from database correctly", () => {
        const mgr = new LayerManager(engine);
        mgr.renderAll(DB_CUSTOM_LAYER_DATA);

        // Tempat Ibadah
        const tempatIbadah = mgr.customLayers.find(
            (l) => l.slug === "tempat-ibadah",
        );
        expect(tempatIbadah).toEqual(
            expect.objectContaining({
                id: 1,
                nama: "Tempat Ibadah",
                slug: "tempat-ibadah",
                warna: "#10b981",
                fill_opacity: 0.3,
                stroke_width: 2,
                pattern_type: "dots",
                visible: true,
                polygonCount: 2,
            }),
        );

        // Fasilitas Kesehatan
        const faskes = mgr.customLayers.find(
            (l) => l.slug === "fasilitas-kesehatan",
        );
        expect(faskes).toEqual(
            expect.objectContaining({
                id: 2,
                nama: "Fasilitas Kesehatan",
                slug: "fasilitas-kesehatan",
                warna: "#ef4444",
                fill_opacity: 0.25,
                stroke_width: 1.5,
                pattern_type: "hatch",
                visible: true,
                polygonCount: 1,
            }),
        );

        // Sekolah
        const sekolah = mgr.customLayers.find((l) => l.slug === "sekolah");
        expect(sekolah).toEqual(
            expect.objectContaining({
                id: 3,
                nama: "Sekolah",
                slug: "sekolah",
                warna: "#3b82f6",
                fill_opacity: 0.2,
                stroke_width: 2,
                pattern_type: "solid",
                visible: true,
                polygonCount: 2,
            }),
        );

        // UMKM Zona (empty layer)
        const umkm = mgr.customLayers.find((l) => l.slug === "umkm-zona");
        expect(umkm).toEqual(
            expect.objectContaining({
                id: 4,
                nama: "UMKM Zona",
                slug: "umkm-zona",
                warna: "#f97316",
                pattern_type: "crosshatch",
                visible: true,
                polygonCount: 0,
            }),
        );
    });

    it("applies non-solid patterns to layers from database", () => {
        const mgr = new LayerManager(engine);
        mgr.renderAll(DB_CUSTOM_LAYER_DATA);

        // Dots pattern for Tempat Ibadah
        expect(engine.patterns.applyCustomLayerPattern).toHaveBeenCalledWith(
            "tempat-ibadah",
            "dots",
            "#10b981",
            0.3,
            expect.any(Object),
        );

        // Hatch pattern for Faskes
        expect(engine.patterns.applyCustomLayerPattern).toHaveBeenCalledWith(
            "fasilitas-kesehatan",
            "hatch",
            "#ef4444",
            0.25,
            expect.any(Object),
        );

        // Crosshatch pattern for UMKM — NOT called because 0 features
        // Solid pattern for Sekolah — NOT called (solid is default)
    });

    it("renders only non-empty layers on map (skips empty features)", () => {
        const mgr = new LayerManager(engine);
        mgr.renderAll(DB_CUSTOM_LAYER_DATA);

        // L.geoJSON should be called for each layer with features > 0
        // DB_CUSTOM_LAYERS has 3 non-empty and 1 empty
        const geoJsonCalls = L.geoJSON.mock.calls.length;
        expect(geoJsonCalls).toBe(3); // Tempat Ibadah, Faskes, Sekolah
    });

    it("counts polygons per layer from database", () => {
        const mgr = new LayerManager(engine);
        mgr.renderAll(DB_CUSTOM_LAYER_DATA);

        const totalPolygons = mgr.customLayers.reduce(
            (sum, l) => sum + l.polygonCount,
            0,
        );
        expect(totalPolygons).toBe(5); // 2 + 1 + 2 + 0
    });

    it("toggles individual layer visibility after loading", () => {
        const mgr = new LayerManager(engine);
        mgr.renderAll(DB_CUSTOM_LAYER_DATA);

        // All visible initially
        mgr.customLayers.forEach((l) => expect(l.visible).toBe(true));

        // Toggle Tempat Ibadah off
        mgr.toggleCustomLayer(1);
        expect(mgr.customLayers.find((l) => l.id === 1).visible).toBe(false);
        expect(engine.map.removeLayer).toHaveBeenCalled();

        // Toggle it back on
        mgr.toggleCustomLayer(1);
        expect(mgr.customLayers.find((l) => l.id === 1).visible).toBe(true);
        expect(engine.map.addLayer).toHaveBeenCalled();
    });

    it("handles empty layers array", () => {
        const mgr = new LayerManager(engine);
        mgr.renderAll([]);

        expect(mgr.customLayers).toHaveLength(0);
    });

    it("handles load failure gracefully", async () => {
        globalThis.fetch.mockRejectedValue(new Error("Connection refused"));

        const mgr = new LayerManager(engine);
        // load() catches errors internally
        await mgr.load("/peta/geojson/layers");

        expect(mgr.customLayers).toHaveLength(0);
    });
});

// ═══════════════════════════════════════════════════════════════
// SYSTEM TEST: RW Polygon CRUD Operations
// ═══════════════════════════════════════════════════════════════
describe("System: RW Polygon CRUD via API", () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it("updates RW polygon geometry via PUT", async () => {
        const successResponse = {
            success: true,
            message: "Polygon RW 01 berhasil disimpan.",
        };
        mockFetchOk(successResponse);

        const geojson = {
            type: "Polygon",
            coordinates: [
                [
                    [119.451, -5.141],
                    [119.461, -5.141],
                    [119.461, -5.156],
                    [119.451, -5.156],
                    [119.451, -5.141],
                ],
            ],
        };

        const result = await apiPut("/peta/rw/1/polygon", {
            geojson,
            warna: "#6366f1",
        });

        expect(result.success).toBe(true);
        expect(result.message).toContain("berhasil disimpan");

        const [url, opts] = globalThis.fetch.mock.calls[0];
        expect(url).toBe("/peta/rw/1/polygon");
        expect(opts.method).toBe("PUT");

        const body = JSON.parse(opts.body);
        expect(body.geojson.type).toBe("Polygon");
        expect(body.geojson.coordinates[0]).toHaveLength(5);
        expect(body.warna).toBe("#6366f1");
    });

    it("sends CSRF token with polygon update", async () => {
        mockFetchOk({ success: true, message: "OK" });

        await apiPut("/peta/rw/2/polygon", {
            geojson: {
                type: "Polygon",
                coordinates: [
                    [
                        [119.46, -5.14],
                        [119.47, -5.14],
                        [119.47, -5.155],
                        [119.46, -5.14],
                    ],
                ],
            },
        });

        const [, opts] = globalThis.fetch.mock.calls[0];
        expect(opts.headers["X-CSRF-TOKEN"]).toBe("test-csrf-token-12345");
    });

    it("updates RW color via PUT", async () => {
        mockFetchOk({
            success: true,
            message: "Warna RW 03 berhasil diperbarui.",
        });

        const result = await apiPut("/peta/rw/3/color", {
            warna: "#14b8a6",
        });

        expect(result.success).toBe(true);
        expect(result.message).toContain("Warna RW 03");

        const body = JSON.parse(globalThis.fetch.mock.calls[0][1].body);
        expect(body.warna).toBe("#14b8a6");
    });

    it("deletes RW polygon via DELETE", async () => {
        mockFetchOk({
            success: true,
            message: "Polygon RW 04 berhasil dihapus.",
        });

        const result = await apiDelete("/peta/rw/4/polygon");

        expect(result.success).toBe(true);
        expect(result.message).toContain("berhasil dihapus");

        const [url, opts] = globalThis.fetch.mock.calls[0];
        expect(url).toBe("/peta/rw/4/polygon");
        expect(opts.method).toBe("DELETE");
    });

    it("handles validation error on polygon update (422)", async () => {
        mockFetchError(422, "The geojson field is required.");

        await expect(
            apiPut("/peta/rw/1/polygon", { geojson: null }),
        ).rejects.toThrow("HTTP 422");
    });

    it("handles invalid color format", async () => {
        mockFetchError(422, "The warna format is invalid.");

        await expect(
            apiPut("/peta/rw/1/color", { warna: "not-a-color" }),
        ).rejects.toThrow("HTTP 422");
    });
});

// ═══════════════════════════════════════════════════════════════
// SYSTEM TEST: Layer CRUD JSON API
// ═══════════════════════════════════════════════════════════════
describe("System: Peta Layer CRUD via JSON API", () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it("creates a new layer via POST", async () => {
        const newLayer = {
            id: 5,
            nama: "Taman Kota",
            slug: "taman-kota",
            warna: "#16a34a",
            fill_opacity: 0.2,
            stroke_width: 1.5,
            pattern_type: "solid",
            is_active: true,
            sort_order: 4,
        };

        mockFetchOk({
            success: true,
            layer: newLayer,
            message: "Layer berhasil dibuat.",
        });

        const result = await apiPost("/admin/peta-layer/store-json", {
            nama: "Taman Kota",
            deskripsi: "Area taman kota publik",
            warna: "#16a34a",
            fill_opacity: 0.2,
            stroke_width: 1.5,
            pattern_type: "solid",
            is_active: true,
        });

        expect(result.success).toBe(true);
        expect(result.layer.nama).toBe("Taman Kota");
        expect(result.layer.slug).toBe("taman-kota");

        const [url, opts] = globalThis.fetch.mock.calls[0];
        expect(url).toBe("/admin/peta-layer/store-json");
        expect(opts.method).toBe("POST");
    });

    it("updates a layer via PUT", async () => {
        const updatedLayer = {
            id: 1,
            nama: "Tempat Ibadah Updated",
            slug: "tempat-ibadah-updated",
            warna: "#059669",
            fill_opacity: 0.4,
            stroke_width: 2.5,
            pattern_type: "hatch",
            is_active: true,
        };

        mockFetchOk({
            success: true,
            layer: updatedLayer,
            message: "Layer berhasil diperbarui.",
        });

        const result = await apiPut("/admin/peta-layer/1/update-json", {
            nama: "Tempat Ibadah Updated",
            warna: "#059669",
            fill_opacity: 0.4,
            stroke_width: 2.5,
            pattern_type: "hatch",
        });

        expect(result.success).toBe(true);
        expect(result.layer.nama).toBe("Tempat Ibadah Updated");
        expect(result.layer.pattern_type).toBe("hatch");
    });

    it("deletes a layer via DELETE", async () => {
        mockFetchOk({
            success: true,
            message: "Layer 'UMKM Zona' berhasil dihapus.",
        });

        const result = await apiDelete("/admin/peta-layer/4/destroy-json");

        expect(result.success).toBe(true);
        expect(result.message).toContain("UMKM Zona");
    });

    it("toggles layer active status via PATCH", async () => {
        mockFetchOk({ success: true, is_active: false });

        const result = await apiFetch("/admin/peta-layer/2/toggle-active", {
            method: "PATCH",
        });

        expect(result.success).toBe(true);
        expect(result.is_active).toBe(false);
    });

    it("reorders layers via POST", async () => {
        mockFetchOk({
            success: true,
            message: "Urutan layer berhasil diperbarui.",
        });

        const result = await apiPost("/admin/peta-layer/reorder", {
            order: [3, 1, 2, 4],
        });

        expect(result.success).toBe(true);

        const body = JSON.parse(globalThis.fetch.mock.calls[0][1].body);
        expect(body.order).toEqual([3, 1, 2, 4]);
    });
});

// ═══════════════════════════════════════════════════════════════
// SYSTEM TEST: Polygon CRUD within Layers
// ═══════════════════════════════════════════════════════════════
describe("System: Peta Layer Polygon CRUD via API", () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it("creates a new polygon in a layer via POST", async () => {
        mockFetchOk({
            success: true,
            id: 10,
            message: "Polygon berhasil disimpan.",
        });

        const result = await apiPost("/admin/peta-layer/1/polygon", {
            nama: "Masjid Baru",
            deskripsi: "Masjid di RT 03",
            geojson: {
                type: "Polygon",
                coordinates: [
                    [
                        [119.46, -5.15],
                        [119.462, -5.15],
                        [119.462, -5.152],
                        [119.46, -5.152],
                        [119.46, -5.15],
                    ],
                ],
            },
        });

        expect(result.success).toBe(true);
        expect(result.id).toBe(10);

        const body = JSON.parse(globalThis.fetch.mock.calls[0][1].body);
        expect(body.nama).toBe("Masjid Baru");
        expect(body.geojson.type).toBe("Polygon");
    });

    it("updates an existing polygon via PUT", async () => {
        mockFetchOk({
            success: true,
            message: "Polygon berhasil diperbarui.",
        });

        const result = await apiPut("/admin/peta-layer/1/polygon/1", {
            nama: "Masjid Al-Ikhlas (Renovasi)",
            deskripsi: "Masjid utama RW 01 - renovasi 2026",
            geojson: {
                type: "Polygon",
                coordinates: [
                    [
                        [119.454, -5.144],
                        [119.459, -5.144],
                        [119.459, -5.149],
                        [119.454, -5.149],
                        [119.454, -5.144],
                    ],
                ],
            },
        });

        expect(result.success).toBe(true);
        expect(result.message).toContain("diperbarui");
    });

    it("deletes a polygon via DELETE", async () => {
        mockFetchOk({
            success: true,
            message: "Polygon berhasil dihapus.",
        });

        const result = await apiDelete("/admin/peta-layer/2/polygon/3");

        expect(result.success).toBe(true);

        const [url, opts] = globalThis.fetch.mock.calls[0];
        expect(url).toBe("/admin/peta-layer/2/polygon/3");
        expect(opts.method).toBe("DELETE");
    });

    it("handles validation error on polygon create", async () => {
        mockFetchError(422, "The geojson field is required.");

        await expect(
            apiPost("/admin/peta-layer/1/polygon", { nama: "No Geometry" }),
        ).rejects.toThrow("HTTP 422");
    });
});

// ═══════════════════════════════════════════════════════════════
// SYSTEM TEST: Full Data Loading Pipeline (All Endpoints)
// ═══════════════════════════════════════════════════════════════
describe("System: Full Map Data Loading Pipeline", () => {
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

    it("loads all data sources from unified endpoint", async () => {
        mockFetchOk(DB_ALL_LAYERS);

        const mgr = new LayerManager(engine);
        await mgr.load("/peta/geojson/layers");

        // 1. Kelurahan boundary
        expect(mgr.kelurahanLayer).not.toBeNull();

        // 2. RW polygons
        expect(mgr.rwDataList).toHaveLength(6);

        // 3. Custom layers
        expect(mgr.customLayers).toHaveLength(4);

        // Single API call
        expect(globalThis.fetch).toHaveBeenCalledTimes(1);
    });

    it("renders all layer types via renderAll", () => {
        const mgr = new LayerManager(engine);
        mgr.renderAll(DB_ALL_LAYERS);

        expect(mgr.kelurahanLayer).not.toBeNull();
        expect(mgr.rwDataList).toHaveLength(6);
        expect(mgr.customLayers).toHaveLength(4);
    });

    it("verifies API call uses correct CSRF headers", async () => {
        mockFetchOk(DB_ALL_LAYERS);

        const mgr = new LayerManager(engine);
        await mgr.load("/peta/geojson/layers");

        const [, opts] = globalThis.fetch.mock.calls[0];
        expect(opts.headers["X-CSRF-TOKEN"]).toBe("test-csrf-token-12345");
    });

    it("verifies API call sets Accept: application/json", async () => {
        mockFetchOk(DB_ALL_LAYERS);

        const mgr = new LayerManager(engine);
        await mgr.load("/peta/geojson/layers");

        const [, opts] = globalThis.fetch.mock.calls[0];
        expect(opts.headers["Accept"]).toBe("application/json");
    });

    it("RW statistics from layers match aggregate stats totals", async () => {
        mockFetchSequence(DB_ALL_LAYERS, DB_STATS);

        const mgr = new LayerManager(engine);
        await mgr.load("/peta/geojson/layers");

        const stats = await apiGet("/peta/stats");

        // Sum from individual RW data should match aggregate stats
        const sumPenduduk = mgr.rwDataList.reduce(
            (s, d) => s + d.total_penduduk,
            0,
        );
        const sumKK = mgr.rwDataList.reduce((s, d) => s + d.total_kk, 0);
        const sumUmkm = mgr.rwDataList.reduce((s, d) => s + d.total_umkm, 0);
        const sumLaki = mgr.rwDataList.reduce((s, d) => s + d.laki_laki, 0);
        const sumPerempuan = mgr.rwDataList.reduce(
            (s, d) => s + d.perempuan,
            0,
        );

        expect(sumPenduduk).toBe(stats.total_penduduk);
        expect(sumKK).toBe(stats.total_kk);
        expect(sumUmkm).toBe(stats.total_umkm);
        expect(sumLaki).toBe(stats.laki_laki);
        expect(sumPerempuan).toBe(stats.perempuan);
        expect(mgr.rwDataList.length).toBe(stats.total_rw);
    });

    it("map remains operational after API failure", async () => {
        globalThis.fetch.mockRejectedValue(new Error("Network offline"));

        const mgr = new LayerManager(engine);
        // load() catches errors internally
        await mgr.load("/peta/geojson/layers");

        expect(mgr.kelurahanLayer).toBeNull();
        expect(mgr.rwDataList).toHaveLength(0);
        expect(mgr.customLayers).toHaveLength(0);

        // Map engine still functional
        expect(engine.map).not.toBeNull();

        // Can still render with local data
        mgr.renderAll([DB_KELURAHAN_LAYER]);
        expect(mgr.kelurahanLayer).not.toBeNull();
    });

    it("full pipeline: load → interact → modify → reload", async () => {
        // Step 1: Initial load
        mockFetchOk(DB_ALL_LAYERS);

        const onRwSelect = vi.fn();
        const mgr = new LayerManager(engine, { onRwSelect });
        await mgr.load("/peta/geojson/layers");
        expect(mgr.rwDataList).toHaveLength(6);

        // Step 2: User selects RW from loaded data
        mgr.rwDataList.forEach((d) => {
            const layer = globalThis.__mockLayer();
            layer.feature = { properties: { RW: d.name } };
            mgr.rwLayerMap[d.name] = layer;
        });

        mgr.selectRw("RW 03");
        expect(mgr.selectedRw).toBe("RW 03");
        expect(onRwSelect).toHaveBeenCalledWith("RW 03", expect.any(Object));

        // Step 3: Admin updates polygon via API
        mockFetchOk({
            success: true,
            message: "Polygon RW 03 berhasil disimpan.",
        });

        const updateResult = await apiPut("/peta/rw/3/polygon", {
            geojson: {
                type: "Polygon",
                coordinates: [
                    [
                        [119.47, -5.14],
                        [119.48, -5.14],
                        [119.48, -5.155],
                        [119.47, -5.155],
                        [119.47, -5.14],
                    ],
                ],
            },
        });
        expect(updateResult.success).toBe(true);

        // Step 4: Reload fresh data with updated stats
        const updatedRwGeojson = { ...DB_RW_GEOJSON };
        updatedRwGeojson.features = updatedRwGeojson.features.map((f) => {
            if (f.properties.RW === "RW 03") {
                return {
                    ...f,
                    properties: {
                        ...f.properties,
                        total_penduduk: 420,
                    },
                };
            }
            return f;
        });

        const updatedAllLayers = [
            DB_KELURAHAN_LAYER,
            { ...DB_RW_LAYER, geojson: updatedRwGeojson },
            ...DB_CUSTOM_LAYER_DATA,
        ];

        mockFetchOk(updatedAllLayers);

        const mgr2 = new LayerManager(engine);
        await mgr2.load("/peta/geojson/layers");

        const updatedRw03 = mgr2.rwDataList.find((d) => d.name === "RW 03");
        expect(updatedRw03.total_penduduk).toBe(420);
    });
});

// ═══════════════════════════════════════════════════════════════
// SYSTEM TEST: Data Integrity & Validation
// ═══════════════════════════════════════════════════════════════
describe("System: Data Integrity & Validation from Database", () => {
    let engine;

    beforeEach(() => {
        vi.clearAllMocks();
        const container = document.getElementById("map");
        if (container) delete container._leaflet_id;
        engine = new MapEngine("map").init();
    });

    it("all RW features have required properties from database", () => {
        const mgr = new LayerManager(engine);
        mgr.renderAll([DB_RW_LAYER]);

        mgr.rwDataList.forEach((item) => {
            // Required fields from getRwStats()
            expect(item.name).toMatch(/^RW \d{2}$/);
            expect(item.warna).toMatch(/^#[0-9a-fA-F]{6}$/);
            expect(typeof item.total_penduduk).toBe("number");
            expect(typeof item.total_kk).toBe("number");
            expect(typeof item.total_rt).toBe("number");
            expect(typeof item.total_umkm).toBe("number");
            expect(typeof item.laki_laki).toBe("number");
            expect(typeof item.perempuan).toBe("number");
        });
    });

    it("all RW features have valid GeoJSON geometry", () => {
        DB_RW_GEOJSON.features.forEach((feature) => {
            expect(feature.type).toBe("Feature");
            expect(feature.geometry.type).toBe("Polygon");
            expect(Array.isArray(feature.geometry.coordinates)).toBe(true);
            expect(feature.geometry.coordinates[0].length).toBeGreaterThan(3);

            // First and last coordinates should be the same (closed ring)
            const ring = feature.geometry.coordinates[0];
            expect(ring[0]).toEqual(ring[ring.length - 1]);
        });
    });

    it("kelurahan feature has valid GeoJSON geometry", () => {
        const feature = DB_KELURAHAN_GEOJSON.features[0];
        expect(feature.type).toBe("Feature");
        expect(feature.geometry.type).toBe("Polygon");

        const ring = feature.geometry.coordinates[0];
        expect(ring.length).toBeGreaterThan(3);
        expect(ring[0]).toEqual(ring[ring.length - 1]);
    });

    it("RW names are unique in database response", () => {
        const mgr = new LayerManager(engine);
        mgr.renderAll([DB_RW_LAYER]);

        const names = mgr.rwDataList.map((d) => d.name);
        const uniqueNames = [...new Set(names)];
        expect(names).toHaveLength(uniqueNames.length);
    });

    it("custom layer slugs are unique in database response", () => {
        engine.patterns = {
            applyCustomLayerPattern: vi.fn(),
            applyRwPatterns: vi.fn(),
        };

        const mgr = new LayerManager(engine);
        mgr.renderAll(DB_CUSTOM_LAYER_DATA);

        const slugs = mgr.customLayers.map((l) => l.slug);
        const uniqueSlugs = [...new Set(slugs)];
        expect(slugs).toHaveLength(uniqueSlugs.length);
    });

    it("all polygon coordinates are within Makassar bounds", () => {
        const MAKASSAR_BOUNDS = {
            minLng: 119.3,
            maxLng: 119.6,
            minLat: -5.25,
            maxLat: -5.05,
        };

        const allFeatures = [
            ...DB_KELURAHAN_GEOJSON.features,
            ...DB_RW_GEOJSON.features,
        ];

        allFeatures.forEach((feature) => {
            feature.geometry.coordinates[0].forEach(([lng, lat]) => {
                expect(lng).toBeGreaterThanOrEqual(MAKASSAR_BOUNDS.minLng);
                expect(lng).toBeLessThanOrEqual(MAKASSAR_BOUNDS.maxLng);
                expect(lat).toBeGreaterThanOrEqual(MAKASSAR_BOUNDS.minLat);
                expect(lat).toBeLessThanOrEqual(MAKASSAR_BOUNDS.maxLat);
            });
        });
    });

    it("RW population data has reasonable values", () => {
        const mgr = new LayerManager(engine);
        mgr.renderAll([DB_RW_LAYER]);

        mgr.rwDataList.forEach((item) => {
            // Population should be positive
            expect(item.total_penduduk).toBeGreaterThan(0);
            expect(item.total_kk).toBeGreaterThan(0);
            expect(item.total_rt).toBeGreaterThan(0);

            // KK should be less than total population
            expect(item.total_kk).toBeLessThan(item.total_penduduk);

            // Gender sum must equal total
            expect(item.laki_laki + item.perempuan).toBe(item.total_penduduk);

            // Both genders should have > 0
            expect(item.laki_laki).toBeGreaterThan(0);
            expect(item.perempuan).toBeGreaterThan(0);
        });
    });

    it("custom layer patterns are valid enum values", () => {
        const validPatterns = ["solid", "hatch", "dots", "crosshatch"];

        DB_CUSTOM_LAYERS.forEach((layer) => {
            expect(validPatterns).toContain(layer.pattern_type);
        });
    });

    it("custom layer colors are valid hex format", () => {
        DB_CUSTOM_LAYERS.forEach((layer) => {
            expect(layer.warna).toMatch(/^#[0-9a-fA-F]{6}$/);
        });
    });

    it("custom layer opacity values are within 0-1 range", () => {
        DB_CUSTOM_LAYERS.forEach((layer) => {
            expect(layer.fill_opacity).toBeGreaterThanOrEqual(0);
            expect(layer.fill_opacity).toBeLessThanOrEqual(1);
        });
    });

    it("custom layer stroke width is positive", () => {
        DB_CUSTOM_LAYERS.forEach((layer) => {
            expect(layer.stroke_width).toBeGreaterThan(0);
        });
    });
});

// ═══════════════════════════════════════════════════════════════
// SYSTEM TEST: Error Handling & Edge Cases
// ═══════════════════════════════════════════════════════════════
describe("System: Error Handling & Edge Cases", () => {
    let engine;

    beforeEach(() => {
        vi.clearAllMocks();
        const container = document.getElementById("map");
        if (container) delete container._leaflet_id;
        engine = new MapEngine("map").init();
    });

    it("handles HTTP 401 Unauthorized (session expired)", async () => {
        mockFetchError(401, "Unauthenticated.");

        await expect(apiGet("/peta/geojson/kelurahan")).rejects.toThrow(
            "HTTP 401",
        );
    });

    it("handles HTTP 403 Forbidden (role-based access denied)", async () => {
        mockFetchError(403, "Forbidden.");

        await expect(apiPut("/peta/rw/1/polygon", {})).rejects.toThrow(
            "HTTP 403",
        );
    });

    it("handles HTTP 404 when RW does not exist", async () => {
        mockFetchError(404, "No query results for model [Rw].");

        await expect(apiPut("/peta/rw/999/polygon", {})).rejects.toThrow(
            "HTTP 404",
        );
    });

    it("handles HTTP 419 CSRF token mismatch", async () => {
        mockFetchError(419, "CSRF token mismatch.");

        await expect(apiPut("/peta/rw/1/polygon", {})).rejects.toThrow(
            "HTTP 419",
        );
    });

    it("handles malformed JSON response", async () => {
        globalThis.fetch.mockResolvedValue({
            ok: true,
            json: () => Promise.reject(new SyntaxError("Unexpected token")),
            text: () => Promise.resolve("not valid json"),
        });

        await expect(apiGet("/peta/stats")).rejects.toThrow();
    });

    it("handles empty response body", async () => {
        globalThis.fetch.mockResolvedValue({
            ok: true,
            json: () => Promise.resolve(null),
            text: () => Promise.resolve(""),
        });

        const result = await apiGet("/peta/stats");
        expect(result).toBeNull();
    });

    it("handles slow response timeout (network disruption)", async () => {
        globalThis.fetch.mockImplementation(
            () =>
                new Promise((_, reject) =>
                    setTimeout(() => reject(new Error("AbortError")), 100),
                ),
        );

        await expect(apiGet("/peta/geojson/rw")).rejects.toThrow("AbortError");
    });

    it("map engine survives total API failure", async () => {
        globalThis.fetch.mockRejectedValue(new Error("Network offline"));

        const mgr = new LayerManager(engine);
        // load() catches errors internally
        await mgr.load("/peta/geojson/layers");

        // Map + engine remain intact
        expect(engine.map).not.toBeNull();
        expect(engine.svgRenderer).not.toBeNull();
        expect(engine.patterns).not.toBeNull();

        // Can still render with local data
        mgr.renderAll([DB_KELURAHAN_LAYER]);
        expect(mgr.kelurahanLayer).not.toBeNull();
    });

    it("handles RW GeoJSON with missing optional properties", () => {
        const sparseRwLayer = {
            ...DB_RW_LAYER,
            geojson: {
                type: "FeatureCollection",
                features: [
                    {
                        type: "Feature",
                        properties: {
                            RW: "RW 01",
                            // warna, stats all missing
                        },
                        geometry: {
                            type: "Polygon",
                            coordinates: [
                                [
                                    [119.45, -5.14],
                                    [119.46, -5.14],
                                    [119.46, -5.15],
                                    [119.45, -5.14],
                                ],
                            ],
                        },
                    },
                ],
            },
        };

        const mgr = new LayerManager(engine);
        mgr.renderAll([sparseRwLayer]);

        expect(mgr.rwDataList).toHaveLength(1);
        const item = mgr.rwDataList[0];
        expect(item.name).toBe("RW 01");
        expect(item.warna).toBe("#6b7280"); // fallback color
        expect(item.total_penduduk).toBe(0);
        expect(item.total_kk).toBe(0);
        expect(item.total_rt).toBe(0);
        expect(item.total_umkm).toBe(0);
        expect(item.laki_laki).toBe(0);
        expect(item.perempuan).toBe(0);
    });
});
