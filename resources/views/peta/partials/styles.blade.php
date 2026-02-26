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

    /* ── Hide zoom control ── */
    .leaflet-control-zoom {
        display: none !important;
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

    .peta-sidebar::-webkit-scrollbar { width: 4px; }
    .peta-sidebar::-webkit-scrollbar-track { background: transparent; }
    .peta-sidebar::-webkit-scrollbar-thumb { background: oklch(var(--bc) / 0.15); border-radius: 4px; }

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

    /* ── Stat Mini Cards ── */
    .stat-mini {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
    }

    .stat-mini .stat-icon { font-size: 14px; flex-shrink: 0; }
    .stat-mini .stat-num { font-weight: 700; font-size: 13px; }
    .stat-mini .stat-lbl { color: oklch(var(--bc) / 0.5); font-size: 11px; }

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
        background: repeating-linear-gradient(
            45deg, transparent, transparent 2px,
            rgba(255, 255, 255, 0.5) 2px, rgba(255, 255, 255, 0.5) 3.5px
        );
    }

    /* ── Gender Bar ── */
    .gender-bar {
        height: 6px;
        border-radius: 3px;
        overflow: hidden;
        background: #e2e8f0;
    }

    .gender-bar-fill {
        height: 100%;
        border-radius: 3px;
        transition: width 0.5s ease;
    }
</style>
