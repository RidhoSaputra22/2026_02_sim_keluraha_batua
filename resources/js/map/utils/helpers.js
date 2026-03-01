/**
 * Utility helpers for the map engine.
 *
 * @module map/utils/helpers
 */

/**
 * Format a number with Indonesian locale (dot separator).
 *
 * @param {number|null} num
 * @returns {string}
 */
export function formatNumber(num) {
    if (!num && num !== 0) return "-";
    return new Intl.NumberFormat("id-ID").format(num);
}

/**
 * Get the CSRF token from the page <meta> tag.
 *
 * @returns {string}
 */
export function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.content : "";
}

/**
 * Sort an array of RW objects by their numeric RW number.
 * Expects objects with a `name` property like "RW 01".
 *
 * @param {Array} list
 * @returns {Array}
 */
export function sortRwList(list) {
    return [...list].sort((a, b) => {
        return (
            parseInt(a.name.replace("RW ", "")) -
            parseInt(b.name.replace("RW ", ""))
        );
    });
}
