/**
 * Unit tests for utils/helpers.js
 */
import { describe, it, expect, beforeEach } from "vitest";
import { formatNumber, getCsrfToken, sortRwList } from "../../utils/helpers";

describe("helpers", () => {
    // ── formatNumber ────────────────────────────────────────
    describe("formatNumber", () => {
        it("formats integers with Indonesian locale (dot separator)", () => {
            expect(formatNumber(1000)).toBe("1.000");
        });

        it("formats large numbers correctly", () => {
            expect(formatNumber(1234567)).toBe("1.234.567");
        });

        it("formats zero as '0'", () => {
            expect(formatNumber(0)).toBe("0");
        });

        it("returns '-' for null", () => {
            expect(formatNumber(null)).toBe("-");
        });

        it("returns '-' for undefined", () => {
            expect(formatNumber(undefined)).toBe("-");
        });

        it("returns '-' for empty string (falsy)", () => {
            expect(formatNumber("")).toBe("-");
        });

        it("formats small numbers without separator", () => {
            expect(formatNumber(42)).toBe("42");
        });

        it("formats negative numbers", () => {
            const result = formatNumber(-500);
            // Indonesian locale uses minus sign
            expect(result).toContain("500");
        });
    });

    // ── getCsrfToken ────────────────────────────────────────
    describe("getCsrfToken", () => {
        it("returns CSRF token from meta tag", () => {
            expect(getCsrfToken()).toBe("test-csrf-token-12345");
        });

        it("returns empty string when meta tag is missing", () => {
            const meta = document.querySelector('meta[name="csrf-token"]');
            meta.remove();

            expect(getCsrfToken()).toBe("");

            // Restore for other tests
            document.head.appendChild(meta);
        });
    });

    // ── sortRwList ──────────────────────────────────────────
    describe("sortRwList", () => {
        it("sorts RW items numerically by name", () => {
            const input = [
                { name: "RW 03", total: 100 },
                { name: "RW 01", total: 200 },
                { name: "RW 10", total: 50 },
                { name: "RW 02", total: 150 },
            ];
            const sorted = sortRwList(input);
            expect(sorted.map((r) => r.name)).toEqual([
                "RW 01",
                "RW 02",
                "RW 03",
                "RW 10",
            ]);
        });

        it("does not mutate original array", () => {
            const input = [
                { name: "RW 03" },
                { name: "RW 01" },
                { name: "RW 02" },
            ];
            const sorted = sortRwList(input);
            expect(input[0].name).toBe("RW 03"); // original unchanged
            expect(sorted[0].name).toBe("RW 01"); // sorted copy
        });

        it("handles single item", () => {
            const input = [{ name: "RW 05" }];
            const sorted = sortRwList(input);
            expect(sorted).toHaveLength(1);
            expect(sorted[0].name).toBe("RW 05");
        });

        it("handles empty array", () => {
            expect(sortRwList([])).toEqual([]);
        });
    });
});
