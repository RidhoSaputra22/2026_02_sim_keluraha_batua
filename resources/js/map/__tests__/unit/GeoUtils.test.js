/**
 * Unit tests for utils/GeoUtils.js
 */
import { describe, it, expect } from "vitest";
import { diffPolygon, cutPolygon } from "../../utils/GeoUtils";

// ── Test data ──────────────────────────────────────────────
const bigSquare = {
    type: "Polygon",
    coordinates: [
        [
            [119.46, -5.15],
            [119.48, -5.15],
            [119.48, -5.17],
            [119.46, -5.17],
            [119.46, -5.15],
        ],
    ],
};

// Small square fully inside bigSquare
const innerSquare = {
    type: "Polygon",
    coordinates: [
        [
            [119.465, -5.155],
            [119.475, -5.155],
            [119.475, -5.165],
            [119.465, -5.165],
            [119.465, -5.155],
        ],
    ],
};

// Square that partially overlaps bigSquare (overlaps on the right edge)
const overlappingSquare = {
    type: "Polygon",
    coordinates: [
        [
            [119.47, -5.15],
            [119.50, -5.15],
            [119.50, -5.17],
            [119.47, -5.17],
            [119.47, -5.15],
        ],
    ],
};

// Square completely outside bigSquare (no overlap)
const outsideSquare = {
    type: "Polygon",
    coordinates: [
        [
            [119.50, -5.15],
            [119.52, -5.15],
            [119.52, -5.17],
            [119.50, -5.17],
            [119.50, -5.15],
        ],
    ],
};

// Very large square that fully contains bigSquare
const hugeSquare = {
    type: "Polygon",
    coordinates: [
        [
            [119.44, -5.13],
            [119.50, -5.13],
            [119.50, -5.19],
            [119.44, -5.19],
            [119.44, -5.13],
        ],
    ],
};

// ── Tests ──────────────────────────────────────────────────

describe("GeoUtils", () => {
    describe("diffPolygon", () => {
        it("creates a hole when cutter is fully inside target", () => {
            const result = diffPolygon(bigSquare, innerSquare);
            expect(result).not.toBeNull();
            // Should produce a Polygon with a hole (outer ring + inner ring)
            expect(result.type).toBe("Polygon");
            expect(result.coordinates.length).toBeGreaterThanOrEqual(2);
        });

        it("removes the overlapping portion when cutter partially overlaps", () => {
            const result = diffPolygon(bigSquare, overlappingSquare);
            expect(result).not.toBeNull();
            // Result should be smaller than original
            expect(result.type).toMatch(/Polygon|MultiPolygon/);
        });

        it("returns the original when cutter does not overlap", () => {
            const result = diffPolygon(bigSquare, outsideSquare);
            expect(result).not.toBeNull();
            expect(result.type).toBe("Polygon");
            // Coordinates should be the same as original (no change)
            expect(result.coordinates[0].length).toBe(
                bigSquare.coordinates[0].length,
            );
        });

        it("returns null when target is fully contained in cutter", () => {
            const result = diffPolygon(bigSquare, hugeSquare);
            expect(result).toBeNull();
        });

        it("returns null for null target", () => {
            expect(diffPolygon(null, innerSquare)).toBeNull();
        });

        it("returns null for null cutter", () => {
            expect(diffPolygon(bigSquare, null)).toBeNull();
        });

        it("returns null for both null", () => {
            expect(diffPolygon(null, null)).toBeNull();
        });
    });

    describe("cutPolygon", () => {
        it("behaves identically to diffPolygon", () => {
            const diffResult = diffPolygon(bigSquare, innerSquare);
            const cutResult = cutPolygon(bigSquare, innerSquare);
            expect(cutResult).toEqual(diffResult);
        });

        it("removes the overlapping part", () => {
            const result = cutPolygon(bigSquare, overlappingSquare);
            expect(result).not.toBeNull();
            expect(result.type).toMatch(/Polygon|MultiPolygon/);
        });

        it("returns null for invalid inputs", () => {
            expect(cutPolygon(null, null)).toBeNull();
        });
    });
});
