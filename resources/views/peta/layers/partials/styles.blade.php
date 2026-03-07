{{-- Styles for QGIS-style layer manager --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />

<style>
/* ── Map ── */
#layer-map {
    height: 100%;
    min-height: 700px;
    border-radius: 0.75rem 0 0 0.75rem;
    z-index: 1;
}

@media (max-width: 1023px) {
    #layer-map {
        min-height: 450px;
        border-radius: 0.75rem 0.75rem 0 0;
    }
}

/* ── Sidebar ── */
.layer-sidebar {
    height: 100%;
    min-height: 700px;
    border-radius: 0 0.75rem 0.75rem 0;
}

@media (max-width: 1023px) {
    .layer-sidebar {
        min-height: auto;
        max-height: 500px;
        border-radius: 0 0 0.75rem 0.75rem;
    }
}

.layer-sidebar::-webkit-scrollbar {
    width: 4px;
}

.layer-sidebar::-webkit-scrollbar-track {
    background: transparent;
}

.layer-sidebar::-webkit-scrollbar-thumb {
    background: oklch(var(--bc) / 0.15);
    border-radius: 4px;
}

/* ── Layer list item ── */
.layer-item {
    transition: all 0.15s ease;
    cursor: pointer;
    border-left: 3px solid transparent;
}

.layer-item:hover {
    background-color: oklch(var(--b2));
}

.layer-item.active {
    background-color: oklch(var(--p) / 0.08);
    border-left-color: oklch(var(--p));
}

.layer-item.dragging {
    opacity: 0.5;
    background-color: oklch(var(--b3));
}

.layer-item.drag-over {
    border-top: 2px solid oklch(var(--p));
}

/* ── Polygon list item (legacy) ── */
.polygon-item {
    transition: all 0.15s ease;
}

.polygon-item:hover {
    background-color: oklch(var(--b2));
}

/* ── Tree structure ── */
.tree-layer-group {
    position: relative;
}

.tree-toggle-btn {
    opacity: 0.5;
    transition: opacity 0.15s;
}

.tree-toggle-btn:hover {
    opacity: 1;
}

.tree-children {
    border-left: 1px solid oklch(var(--bc) / 0.1);
    margin-left: 1.15rem;
}

.polygon-tree-item {
    transition: all 0.15s ease;
    position: relative;
}

.polygon-tree-item:hover {
    background-color: oklch(var(--b2));
}

.polygon-tree-item.polygon-editing {
    background-color: oklch(var(--p) / 0.12);
    border-left: 2px solid oklch(var(--p));
}

.polygon-tree-item.polygon-highlighted {
    background-color: #6366f1;
    border-left: 2px solid #6366f1;
}

.polygon-tree-item.polygon-drag-over {
    border-top: 2px solid oklch(var(--p));
}

.polygon-tree-item.dragging {
    opacity: 0.5;
    background-color: oklch(var(--b3));
}

.tree-connector {
    margin-left: -0.4rem;
}

/* ── Zoom controls ── */
.leaflet-control-zoom {
    border: none !important;
    box-shadow: 0 1px 5px rgba(0, 0, 0, 0.15) !important;
    border-radius: 0.5rem !important;
    overflow: hidden;
}

.leaflet-control-zoom a {
    background-color: oklch(var(--b1)) !important;
    color: oklch(var(--bc)) !important;
    border-color: oklch(var(--bc) / 0.1) !important;
    width: 30px !important;
    height: 30px !important;
    line-height: 30px !important;
    font-size: 16px !important;
}

.leaflet-control-zoom a:hover {
    background-color: oklch(var(--b2)) !important;
}

#layer-map .leaflet-bottom.leaflet-right .leaflet-control-zoom {
    margin-bottom: 12px !important;
    margin-right: 12px !important;
}

/* Keep Leaflet.Draw tooltip away from top-left toolbar */
#layer-map {
    position: relative;
}

#layer-map .leaflet-draw-tooltip {
    margin-left: 12px;
    margin-top: 0;
}

#layer-map .leaflet-draw-tooltip:before {
    left: -7px;
}

.leaflet-draw .leaflet-control {
    margin-top: 12px;
}

/* ── RW reference labels ── */
.rw-label-ref {
    background: none !important;
    border: none !important;
    box-shadow: none !important;
    font-weight: 600;
    font-size: 10px;
    color: #64748b;
    text-shadow: 1px 1px 2px white, -1px -1px 2px white;
    white-space: nowrap;
    pointer-events: none !important;
}

/* ── Drag handle ── */
.drag-handle {
    cursor: grab;
    opacity: 0.4;
    transition: opacity 0.15s;
}

.drag-handle:hover {
    opacity: 0.8;
}

.drag-handle:active {
    cursor: grabbing;
}

/* ── Color dot ── */
.layer-color-dot {
    width: 14px;
    height: 14px;
    border-radius: 3px;
    flex-shrink: 0;
    border: 1px solid rgba(0, 0, 0, 0.15);
}
</style>