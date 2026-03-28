import L from "leaflet";
import "leaflet/dist/leaflet.css";
import MapEngine from "../map/MapEngine";
import "../../css/guest-map.css";

if (!globalThis.L) {
    globalThis.L = L;
}

const GUEST_MAP_FALLBACK_CENTER = [-5.1477, 119.4327];
const GUEST_MAP_FALLBACK_ZOOM = 30;
const GUEST_MAP_TILE_URL =
    "https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png";
const GUEST_MAP_TILE_ATTRIBUTION =
    "&copy; OpenStreetMap contributors &copy; CARTO";
const GUEST_MAP_SATELLITE_TILE_URL =
    "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}";
const GUEST_MAP_SATELLITE_TILE_ATTRIBUTION = "&copy; Esri";

function formatNumber(value) {
    return new Intl.NumberFormat("id-ID").format(Number(value) || 0);
}

function formatUpdatedAt(value) {
    if (!value) {
        return "Peta publik";
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return "Peta publik";
    }

    return date.toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "short",
        year: "numeric",
    });
}

function escapeHtml(value) {
    return String(value ?? "")
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}

function clampNumber(value, min, max) {
    const numeric = Number(value);

    if (!Number.isFinite(numeric)) {
        return min;
    }

    return Math.min(max, Math.max(min, numeric));
}

function getFeatureId(properties = {}) {
    return String(properties.rw_id ?? properties.id ?? properties.label ?? "");
}

function getRwName(properties = {}) {
    if (properties.label) {
        return properties.label;
    }

    if (properties.rw) {
        return properties.rw;
    }

    if (properties.nomor != null) {
        return `RW ${String(properties.nomor).padStart(2, "0")}`;
    }

    return "RW";
}

function getRwMeta(properties = {}) {
    return `${formatNumber(properties.total_penduduk)} jiwa • ${formatNumber(properties.total_rt)} RT • ${formatNumber(properties.total_kk)} KK`;
}

function getLayerKey(layer = {}) {
    return String(layer.slug ?? layer.id ?? layer.layer_type ?? "layer");
}

function getLayerColor(layer = {}) {
    return String(layer.warna || "#E4121B");
}

function getLayerLabel(layer = {}) {
    switch (layer.layer_type) {
        case "kelurahan":
            return "Batas Kelurahan";
        case "rw":
            return "Layer RW";
        default:
            return layer.nama || "Layer Peta";
    }
}

function getGeometryKind(target = {}) {
    const geometryType = String(
        target?.jenis ||
            target?.geometry?.type ||
            target?.features?.find((feature) => feature?.geometry?.type)
                ?.geometry?.type ||
            "",
    ).toLowerCase();

    if (geometryType.includes("point")) {
        return "point";
    }

    if (geometryType.includes("line")) {
        return "line";
    }

    return "polygon";
}

function createGuestMapId() {
    if (typeof globalThis.crypto?.randomUUID === "function") {
        return `guest-kelurahan-map-${globalThis.crypto.randomUUID()}`;
    }

    return `guest-kelurahan-map-${Date.now()}-${Math.random()
        .toString(36)
        .slice(2, 8)}`;
}

function getStorageAssetUrl(path, fallback = "/logo.png") {
    const value = String(path ?? "").trim();

    if (!value) {
        return fallback;
    }

    if (
        value.startsWith("/") ||
        value.startsWith("http://") ||
        value.startsWith("https://") ||
        value.startsWith("//")
    ) {
        return value;
    }

    return `/storage/${value.replace(/^\/+/, "")}`;
}

function toPercentage(value, total) {
    const safeTotal = Number(total) || 0;

    if (safeTotal <= 0) {
        return 0;
    }

    return Math.round(((Number(value) || 0) / safeTotal) * 100);
}

class GuestKelurahanMap {
    constructor(root) {
        this.root = root;
        this.endpoint = root.dataset.endpoint;
        this.canvas = root.querySelector("[data-map-canvas]");
        this.loadingOverlay = root.querySelector("[data-map-loading]");
        this.errorBox = root.querySelector("[data-map-error]");
        this.rwList = root.querySelector("[data-rw-list]");
        this.updatedAt = root.querySelector("[data-updated-at]");
        this.rwCount = root.querySelector("[data-rw-count]");
        this.layerToggleList = root.querySelector("[data-layer-toggle-list]");
        this.layerLegend = root.querySelector("[data-layer-legend]");
        this.summaryTargets = new Map(
            Array.from(root.querySelectorAll("[data-summary-value]")).map(
                (node) => [node.dataset.summaryValue, node],
            ),
        );
        this.resetViewButton = root.querySelector("[data-reset-view]");
        this.fixedLayerToggleButtons = new Map(
            Array.from(root.querySelectorAll("[data-toggle-layer]")).map(
                (node) => [node.dataset.toggleLayer, node],
            ),
        );

        this.engine = null;
        this.map = null;
        this.payload = null;
        this.kelurahanLayer = null;
        this.rwLayer = null;
        this.kelurahanLayerKey = null;
        this.rwLayerKey = null;
        this.activeFeatureId = null;
        this.rwFeatureLayers = new Map();
        this.rwFeatures = [];
        this.layerEntries = [];
        this.layerRegistry = new Map();
        this.layerToggleButtons = new Map();
        this.rwColorMap = {};
        this.rwNameLayerMap = {};
        this.rwLabelLayer = null;
        this.highlightedRwLayer = null;
        this.rwTooltipRegistry = new Map();
        this.baseLayerControl = null;
    }

    async init() {
        if (this.root.dataset.initialized === "true") {
            return;
        }

        this.root.dataset.initialized = "true";

        try {
            const payload = await this.fetchPayload();
            this.payload = payload;
            this.updateSummary(payload.summary ?? {}, payload.meta ?? {});
            this.initMap();
            this.renderAllLayers();
            this.renderRwList();
            this.renderLayerToggles();
            this.renderLayerLegend();
            this.bindControls();
            this.resetView();
            this.hideLoading();
        } catch (error) {
            console.error("[guest-map] init error:", error);
            this.showError(
                error instanceof Error
                    ? error.message
                    : "Peta kelurahan tidak dapat dimuat saat ini.",
            );
        }
    }

    async fetchPayload() {
        const response = await fetch(this.endpoint, {
            headers: {
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
        });

        let data = null;

        try {
            data = await response.json();
        } catch (_error) {
            data = null;
        }

        if (!response.ok) {
            throw new Error(
                data?.message || "Endpoint peta publik tidak dapat diakses.",
            );
        }

        return data;
    }

    initMap() {
        if (!this.canvas) {
            throw new Error("Container peta guest tidak ditemukan.");
        }

        if (!this.canvas.id) {
            this.canvas.id = createGuestMapId();
        }

        this.engine = new MapEngine(this.canvas.id, {
            center: GUEST_MAP_FALLBACK_CENTER,
            zoom: GUEST_MAP_FALLBACK_ZOOM,
            zoomPosition: "bottomright",
            useSvgRenderer: true,
            baseLayers: false,
        }).init();

        this.map = this.engine.map;

        if (!this.map) {
            throw new Error("MapEngine guest gagal diinisialisasi.");
        }

        this.map.scrollWheelZoom?.enable();
        this.map.touchZoom?.enable();

        [
            ["guest-map-overlay-pane", 410],
            ["guest-map-rw-pane", 420],
            ["guest-map-boundary-pane", 430],
            ["guest-map-point-pane", 440],
        ].forEach(([name, zIndex]) => this.ensurePane(name, zIndex));

        this.addBaseLayerControl();
    }

    ensurePane(name, zIndex) {
        if (!this.map) {
            return;
        }

        const pane = this.map.getPane(name) ?? this.map.createPane(name);
        pane.style.zIndex = String(zIndex);
    }

    addBaseLayerControl() {
        if (!this.map) {
            return;
        }

        const petaLayer = L.tileLayer(GUEST_MAP_TILE_URL, {
            maxZoom: 20,
            attribution: GUEST_MAP_TILE_ATTRIBUTION,
        });

        const satelitLayer = L.tileLayer(GUEST_MAP_SATELLITE_TILE_URL, {
            maxZoom: 19,
            attribution: GUEST_MAP_SATELLITE_TILE_ATTRIBUTION,
        });

        petaLayer.addTo(this.map);

        this.baseLayerControl = L.control
            .layers(
                {
                    Peta: petaLayer,
                    Satelit: satelitLayer,
                },
                null,
                {
                    position: "topright",
                    collapsed: false,
                },
            )
            .addTo(this.map);
    }

    getRendererOptions() {
        return this.engine?.svgRenderer
            ? { renderer: this.engine.svgRenderer }
            : {};
    }

    renderAllLayers() {
        const layers = Array.isArray(this.payload?.layers)
            ? this.payload.layers
            : [];

        this.layerEntries = [];
        this.layerRegistry.clear();
        this.layerToggleButtons.clear();
        this.rwFeatureLayers.clear();
        this.rwFeatures = [];
        this.rwColorMap = {};
        this.rwNameLayerMap = {};
        this.highlightedRwLayer = null;
        this.rwTooltipRegistry.clear();
        this.kelurahanLayer = null;
        this.rwLayer = null;
        this.kelurahanLayerKey = null;
        this.rwLayerKey = null;

        if (this.map && this.rwLabelLayer) {
            this.map.removeLayer(this.rwLabelLayer);
        }
        this.rwLabelLayer = null;

        layers.forEach((layerMeta) => {
            const entry = this.buildLayerEntry(layerMeta);

            if (!entry) {
                return;
            }

            if (entry.leafletLayer) {
                entry.leafletLayer.addTo(this.map);
            }

            if (layerMeta.layer_type === "kelurahan" && entry.leafletLayer) {
                this.kelurahanLayer = entry.leafletLayer;
                this.kelurahanLayerKey = entry.key;
            }

            if (layerMeta.layer_type === "rw" && entry.leafletLayer) {
                this.rwLayer = entry.leafletLayer;
                this.rwLayerKey = entry.key;
            }

            this.layerEntries.push(entry);
            this.layerRegistry.set(entry.key, entry);

            if (layerMeta.layer_type === "custom") {
                this.applyCustomLayerPattern(entry);
            }
        });

        this.applyRwPatterns();
        this.bringCustomToFront();
        this.kelurahanLayer?.bringToFront?.();
    }

    buildLayerEntry(layerMeta = {}) {
        const key = getLayerKey(layerMeta);
        const geometryKind = getGeometryKind(layerMeta);
        const featureCount = Array.isArray(layerMeta?.geojson?.features)
            ? layerMeta.geojson.features.length
            : Number(layerMeta?.feature_count ?? 0);
        const hasFeatures = featureCount > 0;
        const leafletLayer = hasFeatures
            ? this.createLeafletLayer(layerMeta, geometryKind)
            : null;

        return {
            key,
            meta: layerMeta,
            geometryKind,
            hasFeatures,
            visible: hasFeatures,
            leafletLayer,
        };
    }

    createLeafletLayer(layerMeta, geometryKind) {
        if (layerMeta.layer_type === "kelurahan") {
            return this.createKelurahanLayer(layerMeta);
        }

        if (layerMeta.layer_type === "rw") {
            return this.createRwLayer(layerMeta);
        }

        return this.createCustomLayer(layerMeta, geometryKind);
    }

    createKelurahanLayer(layerMeta) {
        const kelurahanGeojson = layerMeta?.geojson;

        if (!kelurahanGeojson?.features?.length) {
            return null;
        }

        const color = getLayerColor(layerMeta);

        return L.geoJSON(kelurahanGeojson, {
            pane: "guest-map-boundary-pane",
            ...this.getRendererOptions(),
            interactive: false,
            style: {
                color,
                weight: Math.max(Number(layerMeta.stroke_width ?? 3), 2.4),
                opacity: 1,
                dashArray: "10 7",
                fillColor: color,
                fillOpacity: Math.min(
                    clampNumber(layerMeta.fill_opacity ?? 0.08, 0.04, 0.2),
                    0.12,
                ),
            },
        });
    }

    createRwLayer(layerMeta) {
        const rwGeojson = layerMeta?.geojson;

        if (!rwGeojson?.features?.length) {
            return null;
        }

        this.rwLabelLayer = L.layerGroup().addTo(this.map);

        this.rwFeatures = [...rwGeojson.features].sort((left, right) => {
            const leftNumber = Number(left?.properties?.nomor ?? 0);
            const rightNumber = Number(right?.properties?.nomor ?? 0);

            return leftNumber - rightNumber;
        });

        this.rwFeatures.forEach((feature) => {
            const rwName = getRwName(feature?.properties);
            const color = feature?.properties?.warna || getLayerColor(layerMeta);

            this.rwColorMap[rwName] = color;
        });

        return L.geoJSON(
            {
                ...rwGeojson,
                features: this.rwFeatures,
            },
            {
                pane: "guest-map-rw-pane",
                ...this.getRendererOptions(),
                style: (feature) => this.getRwStyle(feature, layerMeta),
                onEachFeature: (feature, layer) =>
                    this.attachRwFeatureHandlers(feature, layer),
            },
        );
    }

    createCustomLayer(layerMeta, geometryKind) {
        const geojson = layerMeta?.geojson;

        if (!geojson?.features?.length) {
            return null;
        }

        return L.geoJSON(geojson, {
            pane: this.getPaneName(layerMeta.layer_type, geometryKind),
            ...this.getRendererOptions(),
            style: (feature) =>
                this.getCustomLayerStyle(layerMeta, feature, false),
            pointToLayer: (feature, latlng) =>
                L.circleMarker(
                    latlng,
                    {
                        ...this.getRendererOptions(),
                        ...this.getCustomLayerStyle(layerMeta, feature, false),
                    },
                ),
            onEachFeature: (feature, layer) =>
                this.attachCustomFeatureHandlers(layerMeta, feature, layer),
        });
    }

    getPaneName(layerType, geometryKind) {
        if (layerType === "kelurahan") {
            return "guest-map-boundary-pane";
        }

        if (layerType === "rw") {
            return "guest-map-rw-pane";
        }

        if (geometryKind === "point") {
            return "guest-map-point-pane";
        }

        return "guest-map-overlay-pane";
    }

    getRwStyle(feature, layerMeta = {}) {
        const color = feature?.properties?.warna || getLayerColor(layerMeta);

        return {
            color: "#ffffff",
            weight: Math.max(Number(layerMeta.stroke_width ?? 1.4), 1.2),
            opacity: 0.95,
            fillColor: color,
            fillOpacity: clampNumber(
                layerMeta.fill_opacity ?? 0.28,
                0.18,
                0.55,
            ),
        };
    }

    getCustomLayerStyle(layerMeta = {}, feature = {}, highlighted = false) {
        const color = feature?.properties?.warna || getLayerColor(layerMeta);
        const geometryKind = getGeometryKind(feature);
        const dashArray = this.getPatternDashArray(layerMeta.pattern_type);

        if (geometryKind === "point") {
            return {
                radius: highlighted ? 9 : 7.5,
                fillColor: color,
                color: "#ffffff",
                weight: highlighted ? 2.8 : 2.2,
                opacity: 1,
                fillOpacity: highlighted
                    ? 0.98
                    : clampNumber(layerMeta.fill_opacity ?? 0.92, 0.6, 1),
            };
        }

        return {
            color,
            weight: highlighted
                ? Math.max(Number(layerMeta.stroke_width ?? 1.6), 1.2) + 0.8
                : Math.max(Number(layerMeta.stroke_width ?? 1.6), 1.2),
            opacity: 0.95,
            fillColor: color,
            fillOpacity: highlighted
                ? Math.min(
                      clampNumber(layerMeta.fill_opacity ?? 0.3, 0.16, 0.7) +
                          0.18,
                      0.82,
                  )
                : clampNumber(layerMeta.fill_opacity ?? 0.3, 0.16, 0.7),
            dashArray,
        };
    }

    getPatternDashArray(patternType) {
        switch (patternType) {
            case "hatch":
                return "8 6";
            case "crosshatch":
                return "5 4";
            case "dots":
                return "2 6";
            default:
                return null;
        }
    }

    attachRwFeatureHandlers(feature, layer) {
        const featureId = getFeatureId(feature.properties);
        const rwName = getRwName(feature.properties);
        this.rwFeatureLayers.set(featureId, layer);
        this.rwNameLayerMap[rwName] = layer;

        layer.bindPopup(this.buildRwPopup(feature.properties), {
            closeButton: true,
            className: "guest-map-popup-wrap",
            maxWidth: 340,
            autoPan: true,
        });

        this.registerRwTooltip(layer, feature.properties);

        this.addRwLabel(layer, rwName);

        layer.on("mouseover", () => {
            if (
                this.activeFeatureId === featureId ||
                this.highlightedRwLayer === layer
            ) {
                return;
            }

            this.highlightRwLayer(layer);
        });

        layer.on("mouseout", () => {
            if (
                this.activeFeatureId === featureId ||
                this.highlightedRwLayer === layer
            ) {
                return;
            }

            this.restoreRwLayer(layer);
        });

        layer.on("click", () => {
            this.focusFeature(featureId, true);
        });
    }

    attachCustomFeatureHandlers(layerMeta, feature, layer) {
        const title = feature?.properties?.nama || layerMeta?.nama || "Layer Peta";
        const description = feature?.properties?.deskripsi || "";

        layer.bindPopup(this.buildLayerPopup(layerMeta, feature.properties), {
            closeButton: false,
            className: "guest-map-popup-wrap",
        });

        layer.bindTooltip(
            `<strong>${escapeHtml(title)}</strong>${description ? `<br>${escapeHtml(description)}` : ""}`,
            {
                sticky: true,
                direction: "top",
                opacity: 0.95,
            },
        );

        layer.on("mouseover", () => {
            if (typeof layer.setStyle === "function") {
                layer.setStyle(
                    this.getCustomLayerStyle(layerMeta, feature, true),
                );
            }

            try {
                layer.bringToFront?.();
                this.kelurahanLayer?.bringToFront?.();
            } catch (_) {}
        });

        layer.on("mouseout", () => {
            if (typeof layer.setStyle === "function") {
                layer.setStyle(
                    this.getCustomLayerStyle(layerMeta, feature, false),
                );
            }

            this.reapplyCustomPattern(layerMeta, layer);
        });

        layer.on("click", () => {
            this.focusLayerObject(layer, true);
        });
    }

    buildRwTooltip(properties = {}) {
        const rwName = escapeHtml(getRwName(properties));
        const luasArea = escapeHtml(properties?.profil_rw?.luas_area || "-");
        const description = escapeHtml(
            properties?.profil_rw?.deskripsi || properties?.deskripsi || "",
        );
        const stats = [
            `Penduduk: <strong>${formatNumber(properties.total_penduduk)}</strong> jiwa`,
            `KK: <strong>${formatNumber(properties.total_kk)}</strong>`,
            `UMKM: <strong>${formatNumber(properties.total_umkm)}</strong>`,
        ];

        return (
            `<strong>${rwName}</strong>` +
            `<p>Area: ${luasArea}</p>` +
            (description ? `<br>${description}` : "") +
            `<hr style="margin:4px 0;border-color:rgba(0,0,0,.15)">` +
            `<div style="line-height:1.5">${stats.join("<br>")}</div>`
        );
    }

    registerRwTooltip(layer, properties = {}) {
        const tooltipConfig = {
            content: this.buildRwTooltip(properties),
            options: {
                sticky: true,
                direction: "top",
                opacity: 0.95,
            },
        };

        this.rwTooltipRegistry.set(layer, tooltipConfig);
        this.resumeRwTooltip(layer);
    }

    suspendRwTooltip(layer) {
        if (!layer) {
            return;
        }

        try {
            layer.closeTooltip?.();
            layer.unbindTooltip?.();
        } catch (_) {}
    }

    resumeRwTooltip(layer) {
        if (!layer || layer.getTooltip?.()) {
            return;
        }

        const tooltipConfig = this.rwTooltipRegistry.get(layer);

        if (!tooltipConfig) {
            return;
        }

        layer.bindTooltip(tooltipConfig.content, tooltipConfig.options);
    }

    addRwLabel(layer, rwName) {
        if (!this.rwLabelLayer) {
            return;
        }

        const center = layer.getBounds?.().getCenter?.();

        if (!center) {
            return;
        }

        const label = L.marker(center, {
            pane: "guest-map-rw-pane",
            icon: L.divIcon({
                className: "rw-label",
                html: `<span>${escapeHtml(rwName)}</span>`,
                iconSize: [50, 18],
                iconAnchor: [25, 9],
            }),
            interactive: false,
        });

        this.rwLabelLayer.addLayer(label);
    }

    applyRwPatterns() {
        if (!this.rwLayer || !this.engine?.patterns) {
            return;
        }

        this.engine.patterns.applyRwPatterns(
            this.rwColorMap,
            this.rwNameLayerMap,
        );
    }

    applyCustomLayerPattern(entry) {
        if (
            !entry?.leafletLayer ||
            entry.geometryKind === "point" ||
            entry.meta?.pattern_type === "solid" ||
            !this.engine?.patterns
        ) {
            return;
        }

        this.engine.patterns.applyCustomLayerPattern(
            String(entry.meta?.slug || entry.key),
            entry.meta.pattern_type,
            getLayerColor(entry.meta),
            clampNumber(entry.meta.fill_opacity ?? 0.3, 0.16, 0.7),
            entry.leafletLayer,
        );
    }

    reapplyCustomPattern(layerMeta, layer) {
        if (
            !layer ||
            typeof layer.getLatLng === "function" ||
            layerMeta?.pattern_type === "solid" ||
            !this.engine?.patterns
        ) {
            return;
        }

        this.engine.patterns.applyToLayer(
            layer,
            `custom-${String(layerMeta?.slug || layerMeta?.id || "layer")}`,
        );
    }

    getRwLayerBaseStyle(layer) {
        const layerMeta = this.getLayerEntry(this.rwLayerKey)?.meta ?? {};
        return this.getRwStyle(layer?.feature, layerMeta);
    }

    highlightRwLayer(layer) {
        const rwName = getRwName(layer?.feature?.properties);
        const color =
            layer?.feature?.properties?.warna ||
            this.rwColorMap[rwName] ||
            "#6b7280";

        layer.setStyle({
            color: "#0F172A",
            weight: 3.4,
            fillColor: color,
            fillOpacity: 0.5,
            opacity: 1,
        });

        try {
            layer.bringToFront?.();
            this.kelurahanLayer?.bringToFront?.();
        } catch (_) {}
    }

    restoreRwLayer(layer) {
        if (!layer) {
            return;
        }

        layer.setStyle(this.getRwLayerBaseStyle(layer));

        if (this.engine?.patterns) {
            const patternId = `hatch-${getRwName(layer?.feature?.properties).replace(/\s/g, "-")}`;
            this.engine.patterns.applyToLayer(layer, patternId);
        }

        try {
            layer.bringToBack?.();
        } catch (_) {}

        this.bringCustomToFront();
        this.kelurahanLayer?.bringToFront?.();
    }

    bringCustomToFront() {
        this.layerEntries
            .filter(
                (entry) =>
                    entry?.meta?.layer_type === "custom" &&
                    entry?.visible &&
                    entry?.leafletLayer,
            )
            .reverse()
            .forEach((entry) => {
                try {
                    entry.leafletLayer.bringToFront?.();
                    entry.leafletLayer.eachLayer?.((child) =>
                        child.bringToFront?.(),
                    );
                } catch (_) {}
            });
    }

    buildRwPopup(properties = {}) {
        const totalPenduduk = Number(properties.total_penduduk) || 0;
        const totalLakiLaki = Number(properties.laki_laki) || 0;
        const totalPerempuan = Number(properties.perempuan) || 0;
        const persenLakiLaki = toPercentage(totalLakiLaki, totalPenduduk);
        const persenPerempuan = toPercentage(totalPerempuan, totalPenduduk);
        const foto = getStorageAssetUrl(properties?.profil_rw?.foto);
        const ketua = properties?.profil_rw?.ketua || "-";
        const luasArea = properties?.profil_rw?.luas_area || "-";
        const subtitle =
            ketua && ketua !== "-"
                ? `Ketua RW: ${ketua}`
                : "Statistik wilayah RW pada peta publik";

        return `
            <div class="w-full max-w-[19rem] rounded-md bg-white p-4 text-slate-900">
                <div class="flex items-center gap-3 pr-8">
                    <div class="h-[3.75rem] w-[3.75rem] shrink-0 overflow-hidden rounded-md border border-slate-200/80 bg-[radial-gradient(circle_at_top,_rgba(228,18,27,0.16),_transparent_60%),linear-gradient(180deg,_#ffffff_0%,_#f8fafc_100%)] shadow-[inset_0_1px_0_rgba(255,255,255,0.8)]">
                        <img class="block h-full w-full object-cover" src="${escapeHtml(foto)}" alt="${escapeHtml(getRwName(properties))}">
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-base leading-tight font-extrabold text-slate-900">${escapeHtml(getRwName(properties))}</div>
                        <div class="mt-1 text-sm leading-snug text-slate-500">${escapeHtml(subtitle)}</div>
                        <div class="mt-1 text-xs text-slate-700">Luas: <strong class="font-extrabold text-slate-900">${escapeHtml(luasArea)}</strong></div>
                    </div>
                </div>

                <div class="my-4 h-px bg-slate-200"></div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-md bg-gradient-to-b from-slate-50 to-slate-100 px-3 py-3">
                        <div class="text-[8px] font-semibold uppercase tracking-[0.02em] text-slate-500">Penduduk</div>
                        <div class="mt-1 text-lg leading-tight font-extrabold text-slate-900">${formatNumber(totalPenduduk)} jiwa</div>
                    </div>
                    <div class="rounded-md bg-gradient-to-b from-slate-50 to-slate-100 px-3 py-3">
                        <div class="text-[8px] font-semibold uppercase tracking-[0.02em] text-slate-500">Kepala Keluarga</div>
                        <div class="mt-1 text-lg leading-tight font-extrabold text-slate-900">${formatNumber(properties.total_kk)} KK</div>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <span class="inline-flex min-h-6 items-center justify-center rounded-full bg-gradient-to-r from-indigo-600 to-indigo-700 px-3 py-1 text-xs font-bold leading-none text-white">RT ${formatNumber(properties.total_rt)}</span>
                    <span class="inline-flex min-h-6 items-center justify-center rounded-full bg-gradient-to-r from-pink-500 to-pink-600 px-3 py-1 text-xs font-bold leading-none text-white">UMKM ${formatNumber(properties.total_umkm)}</span>
                </div>

                <div class="mt-4">
                    <div class="flex items-center justify-between gap-3 text-[13px] text-slate-700">
                        <span>Laki-laki (${formatNumber(totalLakiLaki)})</span>
                        <span>${formatNumber(persenLakiLaki)}%</span>
                    </div>
                    <div class="mt-2 h-2 overflow-hidden rounded-full bg-gradient-to-r from-indigo-50 to-slate-50">
                        <span class="block h-full rounded-full bg-gradient-to-r from-indigo-600 to-indigo-700" style="width:${persenLakiLaki}%"></span>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="flex items-center justify-between gap-3 text-[13px] text-slate-700">
                        <span>Perempuan (${formatNumber(totalPerempuan)})</span>
                        <span>${formatNumber(persenPerempuan)}%</span>
                    </div>
                    <div class="mt-2 h-2 overflow-hidden rounded-full bg-gradient-to-r from-pink-50 to-slate-50">
                        <span class="block h-full rounded-full bg-gradient-to-r from-pink-500 to-pink-300" style="width:${persenPerempuan}%"></span>
                    </div>
                </div>
            </div>
        `;
    }

    buildLayerPopup(layerMeta = {}, properties = {}) {
        const title = properties.nama || getLayerLabel(layerMeta);
        const subtitle = layerMeta.nama || "Layer peta publik";
        const description =
            properties.deskripsi ||
            `Objek ini berasal dari layer ${getLayerLabel(layerMeta)}.`;

        return `
            <div class="w-full max-w-[18rem] rounded-md bg-white p-4 text-slate-900">
                <div class="text-base leading-tight font-extrabold text-slate-900">${escapeHtml(title)}</div>
                <div class="mt-1 text-sm leading-snug text-slate-500">${escapeHtml(md)}</div>
                <div class="mt-3 rounded-md bg-slate-50 px-3 py-3">
                    <div class="text-[11px] font-semibold uppercase tracking-[0.02em] text-slate-500">Keterangan</div>
                    <div class="mt-1 text-sm leading-relaxed font-medium text-slate-800">${escapeHtml(description)}</div>
                </div>
            </div>
        `;
    }

    updateSummary(summary = {}, meta = {}) {
        for (const [key, node] of this.summaryTargets.entries()) {
            node.textContent = formatNumber(summary[key]);
        }

        if (this.updatedAt) {
            this.updatedAt.textContent = formatUpdatedAt(meta.updated_at);
        }
    }

    renderRwList() {
        if (!this.rwList) {
            return;
        }

        if (!this.rwFeatures.length) {
            this.rwList.innerHTML = `
                <div class="rounded-2xl border border-dashed border-slate-200 p-4 text-sm text-slate-500">
                    Belum ada polygon RW yang tersedia di peta publik.
                </div>
            `;

            if (this.rwCount) {
                this.rwCount.textContent = "0 wilayah";
            }

            return;
        }

        this.rwList.innerHTML = "";

        if (this.rwCount) {
            this.rwCount.textContent = `${formatNumber(this.rwFeatures.length)} wilayah`;
        }

        this.rwFeatures.forEach((feature) => {
            const featureId = getFeatureId(feature.properties);
            const button = document.createElement("button");
            button.type = "button";
            button.className = "guest-map-rw-item";
            button.dataset.featureId = featureId;

            button.innerHTML = `
                <span class="guest-map-rw-swatch" style="background:${escapeHtml(feature.properties?.warna || "#E4121B")}"></span>
                <span class="min-w-0 flex-1">
                    <span class="guest-map-rw-name">${escapeHtml(getRwName(feature.properties))}</span>
                    <span class="guest-map-rw-meta">${escapeHtml(getRwMeta(feature.properties))}</span>
                </span>
            `;

            button.addEventListener("click", () => this.focusFeature(featureId, true));
            this.rwList.appendChild(button);
        });
    }

    renderLayerToggles() {
        if (!this.layerToggleList) {
            return;
        }

        if (!this.layerEntries.length) {
            this.layerToggleList.innerHTML = `
                <div class="rounded-full border border-dashed border-slate-200 bg-white/90 px-4 py-2 text-sm text-slate-500">
                    Belum ada layer aktif.
                </div>
            `;
            return;
        }

        this.layerToggleList.innerHTML = "";
        this.layerToggleButtons.clear();

        this.layerEntries.forEach((entry) => {
            const button = document.createElement("button");
            button.type = "button";
            button.className = "guest-map-toggle guest-map-toggle-layer";
            button.dataset.layerKey = entry.key;
            button.style.setProperty(
                "--guest-layer-color",
                getLayerColor(entry.meta),
            );
            button.disabled = !entry.hasFeatures;
            button.title = entry.hasFeatures
                ? `Tampilkan atau sembunyikan layer ${getLayerLabel(entry.meta)}`
                : `Layer ${getLayerLabel(entry.meta)} belum memiliki objek peta untuk ditampilkan.`;

            button.innerHTML = `
                <span class="guest-map-toggle-swatch" aria-hidden="true"></span>
                <span>${escapeHtml(getLayerLabel(entry.meta))}</span>
            `;

            button.addEventListener("click", () => {
                this.toggleLayer(entry.key);
            });

            this.layerToggleList.appendChild(button);
            this.layerToggleButtons.set(entry.key, button);
        });

        this.syncLayerToggleButtons();
    }

    renderLayerLegend() {
        if (!this.layerLegend) {
            return;
        }

        if (!this.layerEntries.length) {
            this.layerLegend.innerHTML = `
                <div class="rounded-2xl border border-dashed border-slate-200 p-4 text-sm text-slate-500">
                    Layer publik belum tersedia.
                </div>
            `;
            return;
        }

        this.layerLegend.innerHTML = "";

        this.layerEntries.forEach((entry) => {
            const item = document.createElement("div");
            item.className = "flex items-center gap-3";

            const swatch = document.createElement("span");
            swatch.className = "guest-map-legend-swatch";
            swatch.style.setProperty(
                "--guest-layer-color",
                getLayerColor(entry.meta),
            );

            if (entry.meta.layer_type === "kelurahan") {
                swatch.classList.add("is-boundary");
            } else if (entry.geometryKind === "point") {
                swatch.classList.add("is-point");
            }

            if (!entry.hasFeatures) {
                swatch.classList.add("is-muted");
            }

            const text = document.createElement("span");
            text.textContent = this.getLegendDescription(entry);

            item.appendChild(swatch);
            item.appendChild(text);
            this.layerLegend.appendChild(item);
        });
    }

    getLegendDescription(entry) {
        const label = getLayerLabel(entry.meta);

        if (!entry.hasFeatures) {
            return `${label} belum memiliki objek peta untuk ditampilkan`;
        }

        if (entry.meta.layer_type === "kelurahan") {
            return `${label} resmi kelurahan`;
        }

        if (entry.meta.layer_type === "rw") {
            return `${label} dengan statistik wilayah`;
        }

        if (entry.geometryKind === "point") {
            return `${label} berbasis titik lokasi`;
        }

        return `${label} dari endpoint peta publik`;
    }

    bindControls() {
        this.fixedLayerToggleButtons.forEach((button, key) => {
            button.addEventListener("click", () => this.toggleLayer(key));
        });

        this.resetViewButton?.addEventListener("click", () => this.resetView());
    }

    getLayerEntry(key) {
        if (this.layerRegistry.has(key)) {
            return this.layerRegistry.get(key);
        }

        if (key === "kelurahan" && this.kelurahanLayerKey) {
            return this.layerRegistry.get(this.kelurahanLayerKey);
        }

        if (key === "rw" && this.rwLayerKey) {
            return this.layerRegistry.get(this.rwLayerKey);
        }

        return null;
    }

    toggleLayer(key) {
        const entry = this.getLayerEntry(key);

        if (!entry || !entry.hasFeatures || !entry.leafletLayer) {
            return;
        }

        this.setLayerVisibility(key, !entry.visible);
        this.resetView();
    }

    setLayerVisibility(key, visible) {
        const entry = this.getLayerEntry(key);

        if (!entry || !entry.hasFeatures || !entry.leafletLayer) {
            return;
        }

        entry.visible = Boolean(visible);

        if (entry.visible) {
            entry.leafletLayer.addTo(this.map);
        } else {
            this.map.removeLayer(entry.leafletLayer);
        }

        if (entry.key === this.rwLayerKey && this.rwLabelLayer) {
            if (entry.visible) {
                this.rwLabelLayer.addTo(this.map);
            } else {
                this.map.removeLayer(this.rwLabelLayer);
            }
        }

        if (entry.key === this.rwLayerKey && !entry.visible) {
            this.clearActiveRwState();
        }

        this.syncLayerToggleButtons();
    }

    syncLayerToggleButtons() {
        const buttonGroups = [
            ...this.fixedLayerToggleButtons.entries(),
            ...this.layerToggleButtons.entries(),
        ];

        buttonGroups.forEach(([key, button]) => {
            const entry = this.getLayerEntry(key);
            const isActive = Boolean(
                entry?.hasFeatures && entry?.visible !== false,
            );

            button.classList.toggle("is-active", isActive);
            button.classList.toggle("is-disabled", !entry?.hasFeatures);
            button.disabled = !entry?.hasFeatures;
            button.setAttribute("aria-pressed", isActive ? "true" : "false");
        });
    }

    focusFeature(featureId, openPopup = false) {
        if (this.rwLayerKey) {
            this.setLayerVisibility(this.rwLayerKey, true);
        }

        const layer = this.rwFeatureLayers.get(String(featureId));

        if (!layer) {
            return;
        }

        this.activeFeatureId = String(featureId);
        this.highlightedRwLayer = layer;

        this.rwFeatureLayers.forEach((item, id) => {
            if (id === this.activeFeatureId) {
                this.suspendRwTooltip(item);
                this.highlightRwLayer(item);
                return;
            }

            this.resumeRwTooltip(item);
            this.restoreRwLayer(item);
        });

        this.rwList?.querySelectorAll("[data-feature-id]").forEach((node) => {
            node.classList.toggle(
                "is-active",
                node.dataset.featureId === this.activeFeatureId,
            );
        });

        this.bringCustomToFront();
        this.kelurahanLayer?.bringToFront?.();
        this.focusLayerObject(layer, openPopup, 16);
    }

    focusLayerObject(layer, openPopup = false, maxZoom = 17) {
        if (!this.map || !layer) {
            return;
        }

        const bounds = layer.getBounds?.();

        if (bounds?.isValid()) {
            if (this.engine) {
                this.engine.fitBounds(bounds.pad(0.2), {
                    padding: [24, 24],
                    maxZoom,
                });
            } else {
                this.map.fitBounds(bounds.pad(0.2), {
                    padding: [24, 24],
                    maxZoom,
                });
            }
        } else if (typeof layer.getLatLng === "function") {
            this.map.setView(layer.getLatLng(), maxZoom);
        }

        if (openPopup) {
            layer.openPopup();
        }
    }

    clearActiveRwState() {
        this.activeFeatureId = null;
        this.highlightedRwLayer = null;
        this.map?.closePopup();

        this.rwFeatureLayers.forEach((layer) => {
            this.resumeRwTooltip(layer);
            this.restoreRwLayer(layer);
        });
        this.rwList?.querySelectorAll("[data-feature-id]").forEach((node) => {
            node.classList.remove("is-active");
        });
    }

    resetView() {
        const visibleLayers = this.layerEntries
            .filter((entry) => entry.visible && entry.leafletLayer)
            .map((entry) => entry.leafletLayer);

        const fallbackLayers = visibleLayers.length
            ? visibleLayers
            : this.layerEntries
                  .filter((entry) => entry.leafletLayer)
                  .map((entry) => entry.leafletLayer);

        if (!fallbackLayers.length) {
            if (this.map) {
                this.map.setView(
                    GUEST_MAP_FALLBACK_CENTER,
                    GUEST_MAP_FALLBACK_ZOOM,
                );
            }
            return;
        }

        const bounds = L.featureGroup(fallbackLayers).getBounds();

        if (bounds.isValid()) {
            if (this.engine) {
                this.engine.fitBounds(bounds.pad(0.08), {
                    padding: [28, 28],
                });
            } else {
                this.map.fitBounds(bounds.pad(0.08), {
                    padding: [28, 28],
                });
            }
        } else {
            this.map.setView(
                GUEST_MAP_FALLBACK_CENTER,
                GUEST_MAP_FALLBACK_ZOOM,
            );
        }

        this.clearActiveRwState();
    }

    hideLoading() {
        this.loadingOverlay?.classList.add("hidden");
        this.errorBox?.classList.add("hidden");

        window.requestAnimationFrame(() => {
            this.engine?.invalidateSize();
        });
    }

    showError(message) {
        this.loadingOverlay?.classList.add("hidden");

        if (this.errorBox) {
            this.errorBox.textContent = message;
            this.errorBox.classList.remove("hidden");
        }

        if (this.rwList) {
            this.rwList.innerHTML = `
                <div class="rounded-2xl border border-dashed border-red-200 bg-red-50 p-4 text-sm text-red-600">
                    ${escapeHtml(message)}
                </div>
            `;
        }

        if (this.layerLegend) {
            this.layerLegend.innerHTML = `
                <div class="rounded-2xl border border-dashed border-red-200 bg-red-50 p-4 text-sm text-red-600">
                    ${escapeHtml(message)}
                </div>
            `;
        }
    }
}

function initGuestKelurahanMaps() {
    document.querySelectorAll("[data-guest-kelurahan-map]").forEach((element) => {
        const map = new GuestKelurahanMap(element);
        map.init();
    });
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initGuestKelurahanMaps, {
        once: true,
    });
} else {
    initGuestKelurahanMaps();
}

window.initGuestKelurahanMaps = initGuestKelurahanMaps;
