{{-- Styles for Peta Kelurahan --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
/* ── Map ── */
#map {
    height: 100%;
    min-height: 600px;
    border-radius: 0.75rem 0 0 0.75rem;
    z-index: 1;
}

@media (max-width: 1023px) {
    #map {
        min-height: 400px;
        border-radius: 0.75rem 0.75rem 0 0;
    }
}

/* ── Labels ── */
.rw-label {
    background: none !important;
    border: none !important;
    box-shadow: none !important;
    font-weight: 700;
    font-size: 11px;
    color: #1e293b;
    text-shadow: 1px 1px 2px white, -1px -1px 2px white, 1px -1px 2px white, -1px 1px 2px white;
    white-space: nowrap;
    pointer-events: none !important;
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

/* ── Layer switcher control ── */
.leaflet-control-layers {
    border: none !important;
    border-radius: 0.5rem !important;
    box-shadow: 0 1px 5px rgba(0, 0, 0, 0.15) !important;
    overflow: hidden;
}

.leaflet-control-layers-toggle {
    background-color: white !important;
    background-image: url('https://unpkg.com/leaflet@1.9.4/dist/images/layers.png') !important;
    width: 30px !important;
    height: 30px !important;
    background-size: 16px !important;
    background-position: center !important;
    background-repeat: no-repeat !important;
}

.leaflet-control-layers-toggle:hover {
    background-color: #f3f4f6 !important;
}

.leaflet-control-layers-expanded {
    background-color: white !important;
    color: #1e293b !important;
    padding: 8px 12px !important;
    font-size: 13px !important;
}

.leaflet-control-layers-list label {
    color: #1e293b !important;
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 2px 0;
}

.leaflet-control-layers-list label input {
    margin-right: 6px;
}

/* ── Sidebar ── */
.peta-sidebar {
    height: 100%;
    min-height: 600px;
    overflow-y: auto;
    border-radius: 0 0.75rem 0.75rem 0;
}

@media (max-width: 1023px) {
    .peta-sidebar {
        min-height: auto;
        max-height: 500px;
        border-radius: 0 0 0.75rem 0.75rem;
    }
}

.peta-sidebar::-webkit-scrollbar {
    width: 4px;
}

.peta-sidebar::-webkit-scrollbar-track {
    background: transparent;
}

.peta-sidebar::-webkit-scrollbar-thumb {
    background: oklch(var(--bc) / 0.15);
    border-radius: 4px;
}

/* ── RW List Items ── */
.rw-list-item {
    transition: all 0.2s ease;
    cursor: pointer;
    border-left: 4px solid transparent;
}

.rw-list-item:hover {
    background-color: oklch(var(--b2));
    transform: translateX(2px);
}

.rw-list-item.active {
    background-color: oklch(var(--b2));
    border-left-color: oklch(var(--p));
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
}



/* ── Color Swatch ── */
.rw-swatch {
    width: 14px;
    height: 14px;
    border-radius: 3px;
    flex-shrink: 0;
    border: 1px solid rgba(0, 0, 0, 0.15);
    position: relative;
    overflow: hidden;
}

.rw-swatch::after {
    content: '';
    position: absolute;
    inset: 0;
    background: repeating-linear-gradient(45deg, transparent, transparent 2px,
            rgba(255, 255, 255, 0.5) 2px, rgba(255, 255, 255, 0.5) 3.5px);
}
</style>