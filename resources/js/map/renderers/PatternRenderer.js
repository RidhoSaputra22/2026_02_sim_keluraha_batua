/**
 * SVG pattern renderer for Leaflet polygons.
 *
 * Creates hatch / dots / crosshatch fills inside a shared SVG <defs>
 * element so that `url(#patternId)` references resolve correctly.
 *
 * @module map/renderers/PatternRenderer
 */

const SVG_NS = "http://www.w3.org/2000/svg";

export default class PatternRenderer {
    /**
     * @param {L.SVG} svgRenderer - The shared Leaflet SVG renderer instance.
     */
    constructor(svgRenderer) {
        /** @type {L.SVG} */
        this.svgRenderer = svgRenderer;

        /** @type {Set<string>} Track injected pattern IDs to avoid duplicates */
        this._injected = new Set();
    }

    // ── Public API ──────────────────────────────────────────

    /**
     * Get or create the <defs> element in the shared SVG container.
     *
     * @returns {SVGDefsElement|null}
     */
    getDefs() {
        const svg = this.svgRenderer?._container;
        if (!svg) return null;

        let defs = svg.querySelector("defs");
        if (!defs) {
            defs = document.createElementNS(SVG_NS, "defs");
            svg.insertBefore(defs, svg.firstChild);
        }
        return defs;
    }

    /**
     * Create a pattern in <defs>.
     *
     * @param {string} id         - Unique pattern ID (e.g. "hatch-RW-01")
     * @param {string} type       - 'hatch' | 'dots' | 'crosshatch' | 'solid'
     * @param {string} color      - CSS color
     * @param {number} [bgOpacity=0.18]  - Background rectangle opacity
     * @param {number} [fgOpacity=0.55]  - Foreground stroke/fill opacity
     * @returns {boolean} True if pattern was created, false if already exists.
     */
    createPattern(id, type, color, bgOpacity = 0.18, fgOpacity = 0.55) {
        if (this._injected.has(id) || type === "solid") return false;

        const defs = this.getDefs();
        if (!defs) return false;

        // Skip if already in DOM (e.g. from previous render)
        if (document.getElementById(id)) {
            this._injected.add(id);
            return false;
        }

        const pattern = document.createElementNS(SVG_NS, "pattern");
        pattern.setAttribute("id", id);
        pattern.setAttribute("patternUnits", "userSpaceOnUse");
        pattern.setAttribute("width", "10");
        pattern.setAttribute("height", "10");

        // Background tint
        const rect = document.createElementNS(SVG_NS, "rect");
        rect.setAttribute("width", "10");
        rect.setAttribute("height", "10");
        rect.setAttribute("fill", color);
        rect.setAttribute("fill-opacity", String(bgOpacity));
        pattern.appendChild(rect);

        // Foreground shape
        switch (type) {
            case "hatch":
                this._addHatch(pattern, color, fgOpacity);
                break;
            case "dots":
                this._addDots(pattern, color, fgOpacity);
                break;
            case "crosshatch":
                this._addCrosshatch(pattern, color, fgOpacity);
                break;
        }

        defs.appendChild(pattern);
        this._injected.add(id);
        return true;
    }

    /**
     * Apply a pattern fill to a Leaflet layer's SVG <path>.
     *
     * @param {L.Layer} layer     - Leaflet layer with `_path`
     * @param {string}  patternId - ID of the pattern to apply
     */
    applyToLayer(layer, patternId) {
        if (!layer._path) return;
        layer._path.style.fill = `url(#${patternId})`;
        layer._path.style.fillOpacity = "1";
    }

    /**
     * Inject hatch patterns for a map of RW names → colors
     * and apply them to the corresponding layers.
     *
     * @param {Object<string, string>} colorMap  - { "RW 01": "#6366f1", … }
     * @param {Object<string, L.Layer>} layerMap - { "RW 01": layer, … }
     */
    applyRwPatterns(colorMap, layerMap) {
        Object.entries(colorMap).forEach(([rw, color]) => {
            const patternId = "hatch-" + rw.replace(/\s/g, "-");
            this.createPattern(patternId, "hatch", color, 0.18, 0.55);
        });

        Object.entries(layerMap).forEach(([rw, layer]) => {
            const patternId = "hatch-" + rw.replace(/\s/g, "-");
            this.applyToLayer(layer, patternId);
        });
    }

    /**
     * Create and apply a pattern for a custom layer group.
     *
     * @param {string}   slug        - Layer slug for pattern ID
     * @param {string}   patternType - 'hatch' | 'dots' | 'crosshatch' | 'solid'
     * @param {string}   color
     * @param {number}   fillOpacity
     * @param {L.GeoJSON} mapLayer   - The GeoJSON layer group
     */
    applyCustomLayerPattern(slug, patternType, color, fillOpacity, mapLayer) {
        if (patternType === "solid") return;

        const patternId = "custom-" + slug;
        this.createPattern(patternId, patternType, color, fillOpacity, 0.7);

        mapLayer.eachLayer((layer) => this.applyToLayer(layer, patternId));
    }

    // ── Private helpers ─────────────────────────────────────

    /** @private */
    _addHatch(pattern, color, opacity) {
        pattern.setAttribute("patternTransform", "rotate(45)");
        const line = document.createElementNS(SVG_NS, "line");
        line.setAttribute("x1", "0");
        line.setAttribute("y1", "0");
        line.setAttribute("x2", "0");
        line.setAttribute("y2", "10");
        line.setAttribute("stroke", color);
        line.setAttribute("stroke-width", "3");
        line.setAttribute("stroke-opacity", String(opacity));
        pattern.appendChild(line);
    }

    /** @private */
    _addDots(pattern, color, opacity) {
        const circle = document.createElementNS(SVG_NS, "circle");
        circle.setAttribute("cx", "5");
        circle.setAttribute("cy", "5");
        circle.setAttribute("r", "1.5");
        circle.setAttribute("fill", color);
        circle.setAttribute("fill-opacity", String(opacity));
        pattern.appendChild(circle);
    }

    /** @private */
    _addCrosshatch(pattern, color, opacity) {
        const line1 = document.createElementNS(SVG_NS, "line");
        line1.setAttribute("x1", "0");
        line1.setAttribute("y1", "0");
        line1.setAttribute("x2", "10");
        line1.setAttribute("y2", "10");
        line1.setAttribute("stroke", color);
        line1.setAttribute("stroke-width", "1.5");
        line1.setAttribute("stroke-opacity", String(opacity));
        pattern.appendChild(line1);

        const line2 = document.createElementNS(SVG_NS, "line");
        line2.setAttribute("x1", "10");
        line2.setAttribute("y1", "0");
        line2.setAttribute("x2", "0");
        line2.setAttribute("y2", "10");
        line2.setAttribute("stroke", color);
        line2.setAttribute("stroke-width", "1.5");
        line2.setAttribute("stroke-opacity", String(opacity));
        pattern.appendChild(line2);
    }
}
