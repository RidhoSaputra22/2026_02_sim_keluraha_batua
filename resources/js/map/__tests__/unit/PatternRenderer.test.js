/**
 * Unit tests for renderers/PatternRenderer.js
 */
import { describe, it, expect, beforeEach, vi } from "vitest";
import PatternRenderer from "../../renderers/PatternRenderer";

describe("PatternRenderer", () => {
    let renderer;
    let svgContainer;

    beforeEach(() => {
        // Create a fresh SVG container for each test
        svgContainer = document.createElementNS(
            "http://www.w3.org/2000/svg",
            "svg",
        );
        document.body.appendChild(svgContainer);

        const mockSvgRenderer = { _container: svgContainer };
        renderer = new PatternRenderer(mockSvgRenderer);
    });

    afterEach(() => {
        if (svgContainer.parentNode) {
            svgContainer.parentNode.removeChild(svgContainer);
        }
    });

    // ── getDefs ─────────────────────────────────────────────
    describe("getDefs", () => {
        it("creates <defs> element if not present", () => {
            const defs = renderer.getDefs();
            expect(defs).not.toBeNull();
            expect(defs.tagName).toBe("defs");
        });

        it("returns existing <defs> element", () => {
            const defs1 = renderer.getDefs();
            const defs2 = renderer.getDefs();
            expect(defs1).toBe(defs2);
        });

        it("returns null if svgRenderer has no container", () => {
            const emptyRenderer = new PatternRenderer({});
            expect(emptyRenderer.getDefs()).toBeNull();
        });
    });

    // ── createPattern ───────────────────────────────────────
    describe("createPattern", () => {
        it("creates a hatch pattern", () => {
            const created = renderer.createPattern(
                "hatch-test",
                "hatch",
                "#ff0000",
            );
            expect(created).toBe(true);
            const pattern = svgContainer.querySelector("#hatch-test");
            expect(pattern).not.toBeNull();
            expect(pattern.getAttribute("patternTransform")).toBe("rotate(45)");
        });

        it("creates a dots pattern", () => {
            const created = renderer.createPattern(
                "dots-test",
                "dots",
                "#00ff00",
            );
            expect(created).toBe(true);
            const pattern = svgContainer.querySelector("#dots-test");
            expect(pattern).not.toBeNull();
            const circle = pattern.querySelector("circle");
            expect(circle).not.toBeNull();
            expect(circle.getAttribute("fill")).toBe("#00ff00");
        });

        it("creates a crosshatch pattern", () => {
            const created = renderer.createPattern(
                "cross-test",
                "crosshatch",
                "#0000ff",
            );
            expect(created).toBe(true);
            const pattern = svgContainer.querySelector("#cross-test");
            expect(pattern).not.toBeNull();
            const lines = pattern.querySelectorAll("line");
            expect(lines.length).toBe(2); // two diagonal lines
        });

        it("skips solid type", () => {
            const created = renderer.createPattern(
                "solid-test",
                "solid",
                "#000",
            );
            expect(created).toBe(false);
        });

        it("does not duplicate patterns", () => {
            renderer.createPattern("dup-test", "hatch", "#f00");
            const created = renderer.createPattern("dup-test", "hatch", "#f00");
            expect(created).toBe(false);
        });

        it("includes background rect with correct opacity", () => {
            renderer.createPattern("bg-test", "hatch", "#123456", 0.25, 0.6);
            const pattern = svgContainer.querySelector("#bg-test");
            const rect = pattern.querySelector("rect");
            expect(rect.getAttribute("fill")).toBe("#123456");
            expect(rect.getAttribute("fill-opacity")).toBe("0.25");
        });

        it("uses correct default opacities", () => {
            renderer.createPattern("default-op", "hatch", "#abc");
            const pattern = svgContainer.querySelector("#default-op");
            const rect = pattern.querySelector("rect");
            expect(rect.getAttribute("fill-opacity")).toBe("0.18");
            const line = pattern.querySelector("line");
            expect(line.getAttribute("stroke-opacity")).toBe("0.55");
        });
    });

    // ── applyToLayer ────────────────────────────────────────
    describe("applyToLayer", () => {
        it("sets fill style to url(#patternId)", () => {
            const mockPath = document.createElementNS(
                "http://www.w3.org/2000/svg",
                "path",
            );
            const layer = { _path: mockPath };

            renderer.applyToLayer(layer, "my-pattern");

            // jsdom normalizes url() with quotes: url("#id")
            expect(mockPath.style.fill).toContain("my-pattern");
            expect(mockPath.style.fillOpacity).toBe("1");
        });

        it("does nothing if layer has no _path", () => {
            const layer = {};
            expect(() => renderer.applyToLayer(layer, "x")).not.toThrow();
        });
    });

    // ── applyRwPatterns ─────────────────────────────────────
    describe("applyRwPatterns", () => {
        it("creates patterns for each RW color and applies to layers", () => {
            const colorMap = {
                "RW 01": "#6366f1",
                "RW 02": "#ef4444",
            };
            const layerMap = {
                "RW 01": {
                    _path: document.createElementNS(
                        "http://www.w3.org/2000/svg",
                        "path",
                    ),
                },
                "RW 02": {
                    _path: document.createElementNS(
                        "http://www.w3.org/2000/svg",
                        "path",
                    ),
                },
            };

            renderer.applyRwPatterns(colorMap, layerMap);

            // Check patterns were created
            expect(svgContainer.querySelector("#hatch-RW-01")).not.toBeNull();
            expect(svgContainer.querySelector("#hatch-RW-02")).not.toBeNull();

            // Check fills were applied (jsdom normalizes url() with quotes)
            expect(layerMap["RW 01"]._path.style.fill).toContain(
                "hatch-RW-01",
            );
            expect(layerMap["RW 02"]._path.style.fill).toContain(
                "hatch-RW-02",
            );
        });
    });

    // ── applyCustomLayerPattern ─────────────────────────────
    describe("applyCustomLayerPattern", () => {
        it("creates pattern and applies to all sublayers", () => {
            const mockPath1 = document.createElementNS(
                "http://www.w3.org/2000/svg",
                "path",
            );
            const mockPath2 = document.createElementNS(
                "http://www.w3.org/2000/svg",
                "path",
            );
            const mockGeoJSON = {
                eachLayer: vi.fn((cb) => {
                    cb({ _path: mockPath1 });
                    cb({ _path: mockPath2 });
                }),
            };

            renderer.applyCustomLayerPattern(
                "test-layer",
                "dots",
                "#ff0000",
                0.3,
                mockGeoJSON,
            );

            expect(
                svgContainer.querySelector("#custom-test-layer"),
            ).not.toBeNull();
            expect(mockPath1.style.fill).toContain("custom-test-layer");
            expect(mockPath2.style.fill).toContain("custom-test-layer");
        });

        it("skips solid pattern type", () => {
            const mockGeoJSON = { eachLayer: vi.fn() };
            renderer.applyCustomLayerPattern(
                "solid-layer",
                "solid",
                "#000",
                0.5,
                mockGeoJSON,
            );
            expect(mockGeoJSON.eachLayer).not.toHaveBeenCalled();
        });
    });
});
