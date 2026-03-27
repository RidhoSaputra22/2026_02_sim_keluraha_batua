import L from "leaflet";
import "leaflet/dist/leaflet.css";
import MapEngine from "../map/MapEngine";
import "../../css/guest-map.css";

if (!globalThis.L) {
    globalThis.L = L;
}

const GUEST_MAP_FALLBACK_CENTER = [-5.1477, 119.4327];
const GUEST_MAP_FALLBACK_ZOOM = 13;
const GUEST_MAP_TILE_URL =
    "https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png";
const GUEST_MAP_TILE_ATTRIBUTION =
    "&copy; OpenStreetMap contributors &copy; CARTO";

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

        this.map.scrollWheelZoom?.disable();

        [
            ["guest-map-overlay-pane", 410],
            ["guest-map-rw-pane", 420],
            ["guest-map-boundary-pane", 430],
            ["guest-map-point-pane", 440],
        ].forEach(([name, zIndex]) => this.ensurePane(name, zIndex));

        L.tileLayer(GUEST_MAP_TILE_URL, {
            maxZoom: 20,
            attribution: GUEST_MAP_TILE_ATTRIBUTION,
        }).addTo(this.map);
    }

    ensurePane(name, zIndex) {
        if (!this.map) {
            return;
        }

        const pane = this.map.getPane(name) ?? this.map.createPane(name);
        pane.style.zIndex = String(zIndex);
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
        this.kelurahanLayer = null;
        this.rwLayer = null;
        this.kelurahanLayerKey = null;
        this.rwLayerKey = null;

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
        });
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

        this.rwFeatures = [...rwGeojson.features].sort((left, right) => {
            const leftNumber = Number(left?.properties?.nomor ?? 0);
            const rightNumber = Number(right?.properties?.nomor ?? 0);

            return leftNumber - rightNumber;
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
        this.rwFeatureLayers.set(featureId, layer);

        layer.bindPopup(this.buildRwPopup(feature.properties), {
            closeButton: false,
            className: "guest-map-popup-wrap",
        });

        layer.on("mouseover", () => {
            if (this.activeFeatureId === featureId) {
                return;
            }

            layer.setStyle({
                weight: 2.2,
                fillOpacity: 0.38,
                color: "#0F172A",
            });
        });

        layer.on("mouseout", () => {
            if (this.activeFeatureId === featureId) {
                return;
            }

            this.rwLayer?.resetStyle(layer);
        });

        layer.on("click", () => {
            this.focusFeature(featureId, true);
        });
    }

    attachCustomFeatureHandlers(layerMeta, feature, layer) {
        layer.bindPopup(this.buildLayerPopup(layerMeta, feature.properties), {
            closeButton: false,
            className: "guest-map-popup-wrap",
        });

        layer.on("mouseover", () => {
            if (typeof layer.setStyle === "function") {
                layer.setStyle(
                    this.getCustomLayerStyle(layerMeta, feature, true),
                );
            }
        });

        layer.on("mouseout", () => {
            if (typeof layer.setStyle === "function") {
                layer.setStyle(
                    this.getCustomLayerStyle(layerMeta, feature, false),
                );
            }
        });

        layer.on("click", () => {
            this.focusLayerObject(layer, true);
        });
    }

    buildRwPopup(properties = {}) {
        return `
            <div class="guest-map-popup">
                <div class="guest-map-popup-title">${escapeHtml(getRwName(properties))}</div>
                <div class="guest-map-popup-subtitle">Statistik wilayah RW pada peta publik</div>
                <div class="guest-map-popup-grid">
                    <div class="guest-map-popup-card">
                        <div class="guest-map-popup-card-label">Penduduk</div>
                        <div class="guest-map-popup-card-value">${formatNumber(properties.total_penduduk)} jiwa</div>
                    </div>
                    <div class="guest-map-popup-card">
                        <div class="guest-map-popup-card-label">Kepala Keluarga</div>
                        <div class="guest-map-popup-card-value">${formatNumber(properties.total_kk)} KK</div>
                    </div>
                    <div class="guest-map-popup-card">
                        <div class="guest-map-popup-card-label">RT Aktif</div>
                        <div class="guest-map-popup-card-value">${formatNumber(properties.total_rt)} RT</div>
                    </div>
                    <div class="guest-map-popup-card">
                        <div class="guest-map-popup-card-label">UMKM</div>
                        <div class="guest-map-popup-card-value">${formatNumber(properties.total_umkm)} unit</div>
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
            <div class="guest-map-popup">
                <div class="guest-map-popup-title">${escapeHtml(title)}</div>
                <div class="guest-map-popup-subtitle">${escapeHtml(subtitle)}</div>
                <div class="guest-map-popup-card" style="margin-top:0.75rem;">
                    <div class="guest-map-popup-card-label">Keterangan</div>
                    <div class="guest-map-popup-card-value">${escapeHtml(description)}</div>
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

        this.rwFeatureLayers.forEach((item, id) => {
            if (id === this.activeFeatureId) {
                item.setStyle({
                    color: "#0F172A",
                    weight: 2.4,
                    fillOpacity: 0.44,
                });
                return;
            }

            this.rwLayer?.resetStyle(item);
        });

        this.rwList?.querySelectorAll("[data-feature-id]").forEach((node) => {
            node.classList.toggle(
                "is-active",
                node.dataset.featureId === this.activeFeatureId,
            );
        });

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
        this.map?.closePopup();

        this.rwFeatureLayers.forEach((layer) => this.rwLayer?.resetStyle(layer));
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
