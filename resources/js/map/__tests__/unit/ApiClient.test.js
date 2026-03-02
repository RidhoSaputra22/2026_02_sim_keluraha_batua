/**
 * Unit tests for utils/ApiClient.js
 */
import { describe, it, expect, beforeEach, vi } from "vitest";
import {
    apiFetch,
    apiGet,
    apiPut,
    apiPost,
    apiDelete,
} from "../../utils/ApiClient";

describe("ApiClient", () => {
    beforeEach(() => {
        vi.restoreAllMocks();
        globalThis.fetch = vi.fn();
    });

    function mockFetchOk(data = {}) {
        globalThis.fetch.mockResolvedValue({
            ok: true,
            json: () => Promise.resolve(data),
            text: () => Promise.resolve(JSON.stringify(data)),
        });
    }

    function mockFetchError(status = 500, body = "Server Error") {
        globalThis.fetch.mockResolvedValue({
            ok: false,
            status,
            json: () => Promise.reject(new Error("not json")),
            text: () => Promise.resolve(body),
        });
    }

    // ── apiFetch ────────────────────────────────────────────
    describe("apiFetch", () => {
        it("sends GET request with CSRF and JSON headers", async () => {
            mockFetchOk({ success: true });

            await apiFetch("/api/test");

            expect(globalThis.fetch).toHaveBeenCalledWith(
                "/api/test",
                expect.objectContaining({
                    headers: expect.objectContaining({
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "test-csrf-token-12345",
                        Accept: "application/json",
                    }),
                }),
            );
        });

        it("returns parsed JSON on success", async () => {
            mockFetchOk({ data: [1, 2, 3] });

            const result = await apiFetch("/api/data");
            expect(result).toEqual({ data: [1, 2, 3] });
        });

        it("throws error on non-ok response", async () => {
            mockFetchError(404, "Not Found");

            await expect(apiFetch("/api/missing")).rejects.toThrow(
                "HTTP 404: Not Found",
            );
        });

        it("merges custom headers", async () => {
            mockFetchOk({});

            await apiFetch("/api/test", {
                headers: { "X-Custom": "value" },
            });

            const callArgs = globalThis.fetch.mock.calls[0][1];
            expect(callArgs.headers["X-Custom"]).toBe("value");
            expect(callArgs.headers["X-CSRF-TOKEN"]).toBe(
                "test-csrf-token-12345",
            );
        });
    });

    // ── apiGet ──────────────────────────────────────────────
    describe("apiGet", () => {
        it("performs GET request", async () => {
            mockFetchOk({ items: [] });

            const result = await apiGet("/api/items");
            expect(result).toEqual({ items: [] });
            expect(globalThis.fetch).toHaveBeenCalledWith(
                "/api/items",
                expect.any(Object),
            );
        });
    });

    // ── apiPut ──────────────────────────────────────────────
    describe("apiPut", () => {
        it("sends PUT request with JSON body", async () => {
            mockFetchOk({ updated: true });

            const result = await apiPut("/api/item/1", { name: "test" });

            expect(result).toEqual({ updated: true });
            const [url, opts] = globalThis.fetch.mock.calls[0];
            expect(url).toBe("/api/item/1");
            expect(opts.method).toBe("PUT");
            expect(opts.body).toBe(JSON.stringify({ name: "test" }));
        });
    });

    // ── apiPost ─────────────────────────────────────────────
    describe("apiPost", () => {
        it("sends POST request with JSON body", async () => {
            mockFetchOk({ id: 99 });

            const result = await apiPost("/api/items", { nama: "Baru" });

            expect(result).toEqual({ id: 99 });
            const [url, opts] = globalThis.fetch.mock.calls[0];
            expect(url).toBe("/api/items");
            expect(opts.method).toBe("POST");
            expect(opts.body).toBe(JSON.stringify({ nama: "Baru" }));
        });
    });

    // ── apiDelete ───────────────────────────────────────────
    describe("apiDelete", () => {
        it("sends DELETE request", async () => {
            mockFetchOk({ deleted: true });

            const result = await apiDelete("/api/item/5");

            expect(result).toEqual({ deleted: true });
            const [url, opts] = globalThis.fetch.mock.calls[0];
            expect(url).toBe("/api/item/5");
            expect(opts.method).toBe("DELETE");
        });
    });
});
