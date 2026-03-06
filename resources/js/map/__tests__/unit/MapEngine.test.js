/**
 * Unit tests for MapEngine.js
 */
import { describe, it, expect, beforeEach, vi } from "vitest";
import MapEngine from "../../MapEngine";

describe("MapEngine", () => {
    beforeEach(() => {
        vi.clearAllMocks();
        // Reset map container — remove _leaflet_id
        const container = document.getElementById("map");
        if (container) delete container._leaflet_id;
    });

    // ── Constructor ─────────────────────────────────────────
    describe("constructor", () => {
        it("stores containerId", () => {
            const engine = new MapEngine("map");
            expect(engine.containerId).toBe("map");
        });

        it("uses default options when none provided", () => {
            const engine = new MapEngine("map");
            expect(engine.options.center).toEqual([-5.1532008, 119.4682932]);
            expect(engine.options.zoom).toBe(15);
            expect(engine.options.zoomControl).toBe(false);
            expect(engine.options.useSvgRenderer).toBe(true);
            expect(engine.options.baseLayers).toBe(true);
            expect(engine.options.autoResize).toBe(true);
        });

        it("merges custom options with defaults", () => {
            const engine = new MapEngine("map", {
                zoom: 12,
                center: [-5.2, 119.5],
            });
            expect(engine.options.zoom).toBe(12);
            expect(engine.options.center).toEqual([-5.2, 119.5]);
            expect(engine.options.useSvgRenderer).toBe(true); // default preserved
        });

        it("starts with null map and renderer", () => {
            const engine = new MapEngine("map");
            expect(engine.map).toBeNull();
            expect(engine.svgRenderer).toBeNull();
            expect(engine.patterns).toBeNull();
        });
    });

    // ── init() ──────────────────────────────────────────────
    describe("init", () => {
        it("creates a Leaflet map", () => {
            const engine = new MapEngine("map").init();
            expect(L.map).toHaveBeenCalledWith(
                "map",
                expect.objectContaining({
                    center: [-5.1532008, 119.4682932],
                    zoom: 15,
                }),
            );
            expect(engine.map).not.toBeNull();
        });

        it("returns this for chaining", () => {
            const engine = new MapEngine("map");
            const result = engine.init();
            expect(result).toBe(engine);
        });

        it("creates SVG renderer by default", () => {
            const engine = new MapEngine("map").init();
            expect(L.svg).toHaveBeenCalledWith({ padding: 0.5 });
            expect(engine.svgRenderer).not.toBeNull();
        });

        it("skips SVG renderer when useSvgRenderer is false", () => {
            vi.clearAllMocks();
            const engine = new MapEngine("map", {
                useSvgRenderer: false,
            }).init();
            expect(engine.svgRenderer).toBeNull();
            expect(engine.patterns).toBeNull();
        });

        it("creates custom panes (kelurahanPane, rwPane, customLayerPane)", () => {
            const engine = new MapEngine("map").init();
            expect(engine.map.createPane).toHaveBeenCalledWith("kelurahanPane");
            expect(engine.map.createPane).toHaveBeenCalledWith("rwPane");
            expect(engine.map.createPane).toHaveBeenCalledWith(
                "customLayerPane",
            );
        });

        it("creates PatternRenderer when SVG renderer is available", () => {
            const engine = new MapEngine("map").init();
            expect(engine.patterns).not.toBeNull();
        });

        it("adds resize observer by default", () => {
            const engine = new MapEngine("map").init();
            expect(engine._resizeObserver).not.toBeNull();
        });

        it("warns and returns this if container is missing", () => {
            const warnSpy = vi.spyOn(console, "warn").mockImplementation();
            const engine = new MapEngine("nonexistent").init();
            expect(warnSpy).toHaveBeenCalled();
            expect(engine.map).toBeNull();
        });

        it("prevents double initialization", () => {
            const engine = new MapEngine("map").init();
            const container = document.getElementById("map");
            container._leaflet_id = 123; // simulate Leaflet marking the container

            const warnSpy = vi.spyOn(console, "warn").mockImplementation();
            engine.init();
            expect(warnSpy).toHaveBeenCalled();
        });
    });

    // ── destroy() ───────────────────────────────────────────
    describe("destroy", () => {
        it("removes the map", () => {
            const engine = new MapEngine("map").init();
            const map = engine.map;
            engine.destroy();
            expect(map.remove).toHaveBeenCalled();
            expect(engine.map).toBeNull();
        });

        it("disconnects resize observer", () => {
            const engine = new MapEngine("map").init();
            const observer = engine._resizeObserver;
            const disconnectSpy = vi.spyOn(observer, "disconnect");
            engine.destroy();
            expect(disconnectSpy).toHaveBeenCalled();
            expect(engine._resizeObserver).toBeNull();
        });

        it("is safe to call multiple times", () => {
            const engine = new MapEngine("map").init();
            engine.destroy();
            expect(() => engine.destroy()).not.toThrow();
        });
    });

    // ── invalidateSize() ────────────────────────────────────
    describe("invalidateSize", () => {
        it("calls map.invalidateSize", () => {
            const engine = new MapEngine("map").init();
            engine.invalidateSize();
            expect(engine.map.invalidateSize).toHaveBeenCalled();
        });

        it("does nothing if map is null", () => {
            const engine = new MapEngine("map");
            expect(() => engine.invalidateSize()).not.toThrow();
        });
    });

    // ── fitBounds / flyToBounds ─────────────────────────────
    describe("bounds helpers", () => {
        it("fitBounds delegates to map.fitBounds", () => {
            const engine = new MapEngine("map").init();
            const bounds = { isValid: () => true };
            engine.fitBounds(bounds);
            expect(engine.map.fitBounds).toHaveBeenCalledWith(bounds, {
                padding: [20, 20],
            });
        });

        it("fitBounds does nothing with null bounds", () => {
            const engine = new MapEngine("map").init();
            engine.fitBounds(null);
            expect(engine.map.fitBounds).not.toHaveBeenCalled();
        });

        it("flyToBounds delegates to map.flyToBounds", () => {
            const engine = new MapEngine("map").init();
            const bounds = { isValid: () => true };
            engine.flyToBounds(bounds);
            expect(engine.map.flyToBounds).toHaveBeenCalledWith(
                bounds,
                expect.objectContaining({
                    padding: [20, 20],
                    duration: 0.5,
                }),
            );
        });

        it("constrainToBounds sets max bounds and zoom limits", () => {
            const engine = new MapEngine("map").init();
            const bounds = {
                pad: vi.fn().mockReturnValue("padded"),
            };
            engine.constrainToBounds(bounds, 0.15, 3);
            expect(bounds.pad).toHaveBeenCalledWith(0.15);
            expect(engine.map.setMaxBounds).toHaveBeenCalledWith("padded");
            expect(engine.map.setMinZoom).toHaveBeenCalledWith(14);
            expect(engine.map.setMaxZoom).toHaveBeenCalledWith(18);
        });
    });
});
