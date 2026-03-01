/**
 * Lightweight fetch wrapper with CSRF & JSON defaults.
 *
 * @module map/utils/ApiClient
 */

import { getCsrfToken } from "./helpers";

/**
 * @param {string} url
 * @param {object} [options]
 * @returns {Promise<object>}
 */
export async function apiFetch(url, options = {}) {
    const defaults = {
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": getCsrfToken(),
            Accept: "application/json",
        },
    };

    const merged = {
        ...defaults,
        ...options,
        headers: { ...defaults.headers, ...(options.headers || {}) },
    };

    const res = await fetch(url, merged);

    if (!res.ok) {
        const text = await res.text().catch(() => "");
        throw new Error(`HTTP ${res.status}: ${text}`);
    }

    return res.json();
}

/**
 * GET JSON from url.
 *
 * @param {string} url
 * @returns {Promise<object>}
 */
export async function apiGet(url) {
    return apiFetch(url);
}

/**
 * PUT JSON to url.
 *
 * @param {string} url
 * @param {object} body
 * @returns {Promise<object>}
 */
export async function apiPut(url, body) {
    return apiFetch(url, {
        method: "PUT",
        body: JSON.stringify(body),
    });
}

/**
 * POST JSON to url.
 *
 * @param {string} url
 * @param {object} body
 * @returns {Promise<object>}
 */
export async function apiPost(url, body) {
    return apiFetch(url, {
        method: "POST",
        body: JSON.stringify(body),
    });
}

/**
 * DELETE request.
 *
 * @param {string} url
 * @returns {Promise<object>}
 */
export async function apiDelete(url) {
    return apiFetch(url, { method: "DELETE" });
}
