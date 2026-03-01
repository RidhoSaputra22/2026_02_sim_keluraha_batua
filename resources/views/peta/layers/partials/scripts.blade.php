{{-- Scripts for QGIS-style Layer Manager --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
@vite('resources/js/map/index.js')

@php
$layerRoutes = [
'geojsonRw' => route('peta.geojson.rw'),
'reorder' => route('admin.peta-layer.reorder'),
'storeJson' => route('admin.peta-layer.store-json'),
'toggleActive' => url('admin/peta-layer'),
'updateJson' => url('admin/peta-layer'),
'destroyJson' => url('admin/peta-layer'),
'polygonBase' => url('admin/peta-layer'),
];

$initialLayer = $layers->map(fn($l) => [
'id' => $l->id,
'nama' => $l->nama,
'slug' => $l->slug,
'deskripsi' => $l->deskripsi,
'warna' => $l->warna,
'fill_opacity' => $l->fill_opacity,
'stroke_width' => $l->stroke_width,
'pattern_type' => $l->pattern_type,
'is_active' => $l->is_active,
'sort_order' => $l->sort_order,
'polygons_count' => $l->polygons_count,
])->values();
@endphp

<script>
const LAYER_ROUTES = @json($layerRoutes);
const INITIAL_LAYERS = @json($initialLayer);
const LAYERS_GEOJSON = @json($layersGeojson);

function layerManager() {
    return {
        // ─── State ─────────────────────────────────────
        loading: true,
        layers: [],
        activeLayer: null,
        activePolygonCount: 0,
        polygonList: [],
        showRwOverlay: true,

        // ─── Modal state ───────────────────────────────
        editingLayer: null,
        modalForm: {
            nama: '',
            deskripsi: '',
            warna: '#3b82f6',
            fill_opacity: 0.30,
            stroke_width: 2.0,
            pattern_type: 'solid',
            is_active: true,
        },

        // ─── Toast ─────────────────────────────────────
        toast: {
            show: false,
            message: '',
            type: 'success'
        },

        // ─── Drag ──────────────────────────────────────
        _dragIndex: null,

        // ─── Engine ────────────────────────────────────
        _editor: null,
        _displayLayers: {}, // layerId -> L.GeoJSON (read-only display)
        _rwOverlayGroup: null,

        // ─── Init ──────────────────────────────────────
        init() {
            this.layers = JSON.parse(JSON.stringify(INITIAL_LAYERS));
            this.layers.forEach(l => l.visible = true);
            this.$nextTick(() => this._bootstrap());
        },

        async _bootstrap() {
            // Wait for SimPeta
            if (typeof SimPeta === 'undefined') {
                await new Promise(resolve => {
                    const check = setInterval(() => {
                        if (typeof SimPeta !== 'undefined') {
                            clearInterval(check);
                            resolve();
                        }
                    }, 20);
                });
            }



            // Init polygon editor
            this._editor = new SimPeta.PolygonEditor('layer-map', {
                color: '#6366f1',
                weight: 3,
                fillOpacity: 0.35,
                strokeWidth: 3,
                rectangle: true,

            }).init();

            // Load RW overlay
            this._editor.loadRwOverlay(LAYER_ROUTES.geojsonRw);
            this._rwOverlayGroup = this._editor.rwOverlay;

            // Remove draw control initially (no layer selected)
            if (this._editor.drawControl) {
                this._editor.map.removeControl(this._editor.drawControl);
            }

            // Render all layers as read-only display
            this._renderAllDisplayLayers();

            // Bind draw events
            this._bindDrawEvents();

            this.loading = false;
            this.$nextTick(() => {
                if (this._editor.map) this._editor.map.invalidateSize();
            });
        },

        // ─── Render display layers (read-only) ─────────
        _renderAllDisplayLayers() {
            this.layers.forEach(layer => {
                this._renderDisplayLayer(layer);
            });
        },

        _renderDisplayLayer(layer) {
            if (!this._editor || !this._editor.map) return;
            const geojson = LAYERS_GEOJSON[layer.id];
            if (!geojson || !geojson.features || geojson.features.length === 0) return;

            const mapLayer = L.geoJSON(geojson, {
                pane: 'customLayerPane',
                style: {
                    color: layer.warna,
                    weight: layer.stroke_width,
                    fillOpacity: layer.fill_opacity,
                    fillColor: layer.warna,
                },
                onEachFeature: (feature, lyr) => {
                    const nama = feature.properties.nama || layer.nama;
                    lyr.bindTooltip(nama, {
                        sticky: true
                    });
                },
            });

            mapLayer.addTo(this._editor.map);
            this._displayLayers[layer.id] = mapLayer;
        },

        _removeDisplayLayer(layerId) {
            const ml = this._displayLayers[layerId];
            if (ml && this._editor && this._editor.map) {
                this._editor.map.removeLayer(ml);
                delete this._displayLayers[layerId];
            }
        },

        // ─── Select layer for editing ──────────────────
        selectLayer(layer) {
            if (!this._editor || !this._editor.map) return;
            if (this.activeLayer?.id === layer.id) return;

            // Deselect current
            this._deactivateCurrentLayer();

            this.activeLayer = layer;

            // Hide the display layer for this one (we'll show editable version)
            this._removeDisplayLayer(layer.id);

            // Configure editor for this layer
            this._editor.options.color = layer.warna;
            this._editor.options.fillOpacity = layer.fill_opacity;
            this._editor.options.strokeWidth = layer.stroke_width;

            // Clear drawn items
            this._editor.clearDrawn();

            // Load existing polygons into editor
            const geojson = LAYERS_GEOJSON[layer.id] || {
                type: 'FeatureCollection',
                features: []
            };
            this.polygonList = this._editor.loadExistingCollection(geojson);
            this.activePolygonCount = this.polygonList.length;

            // Re-add draw control with layer's color
            this._addDrawControl(layer);

            // Zoom to layer bounds if has polygons
            if (this.polygonList.length > 0 && this._editor.drawnItems.getLayers().length > 0) {
                this._editor.map.fitBounds(this._editor.drawnItems.getBounds(), {
                    padding: [50, 50]
                });
            }
        },

        _deactivateCurrentLayer() {
            if (!this.activeLayer) return;
            if (!this._editor || !this._editor.map) return;

            // Remove draw control
            if (this._editor.drawControl) {
                this._editor.map.removeControl(this._editor.drawControl);
            }

            // Clear editor drawn items
            this._editor.clearDrawn();

            // Re-render as display layer
            const currentId = this.activeLayer.id;
            if (LAYERS_GEOJSON[currentId] && LAYERS_GEOJSON[currentId].features.length > 0) {
                this._renderDisplayLayer(this.activeLayer);
            }

            this.activeLayer = null;
            this.polygonList = [];
            this.activePolygonCount = 0;
        },

        _addDrawControl(layer) {
            if (!this._editor || !this._editor.map) return;
            // Remove existing
            if (this._editor.drawControl) {
                this._editor.map.removeControl(this._editor.drawControl);
            }

            this._editor.drawControl = new L.Control.Draw({
                position: 'topleft',
                draw: {
                    polygon: {
                        allowIntersection: false,
                        shapeOptions: {
                            color: layer.warna,
                            weight: layer.stroke_width,
                            fillOpacity: layer.fill_opacity,
                        },
                    },
                    polyline: false,
                    circle: false,
                    circlemarker: false,
                    marker: false,
                    rectangle: {
                        shapeOptions: {
                            color: layer.warna,
                            weight: layer.stroke_width,
                            fillOpacity: layer.fill_opacity,
                        },
                    },

                },
                edit: {
                    featureGroup: this._editor.drawnItems,
                    remove: true,
                },

            });
            this._editor.map.addControl(this._editor.drawControl);
        },

        // ─── Draw events ───────────────────────────────
        _bindDrawEvents() {
            if (!this._editor || !this._editor.map) return;
            this._editor.map.on(L.Draw.Event.CREATED, (e) => {
                if (!this.activeLayer) return;
                // Ensure newly drawn polygon renders in editPane (above base/custom layers)
                if (e.layer.options) e.layer.options.pane = 'editPane';
                this._editor.drawnItems.addLayer(e.layer);
                this._saveNewPolygon(e.layer);
            });

            this._editor.map.on(L.Draw.Event.EDITED, (e) => {
                e.layers.eachLayer((layer) => {
                    this._updatePolygonGeometry(layer);
                });
            });

            this._editor.map.on(L.Draw.Event.DELETED, (e) => {
                e.layers.eachLayer((layer) => {
                    const poly = this.polygonList.find(p => p.layer === layer);
                    if (poly && poly.id) this._deletePolygonFromServer(poly.id);
                    this.polygonList = this.polygonList.filter(p => p.layer !== layer);
                    this.activePolygonCount = this.polygonList.length;
                    this._updateLayerGeojsonCache();
                });
            });
        },

        // ─── Polygon CRUD ──────────────────────────────
        async _saveNewPolygon(layer) {
            const lid = this.activeLayer.id;
            const url = LAYER_ROUTES.polygonBase + '/' + lid + '/polygon';
            try {
                const geojson = layer.toGeoJSON().geometry;
                const data = await SimPeta.apiPost(url, {
                    geojson,
                    nama: 'Polygon ' + (this.polygonList.length + 1),
                });
                if (data.success) {
                    this.polygonList.push({
                        id: data.id,
                        nama: 'Polygon ' + (this.polygonList.length + 1),
                        layer,
                    });
                    this.activePolygonCount = this.polygonList.length;
                    this._updateLayerPolygonCount(lid, this.activePolygonCount);
                    this._updateLayerGeojsonCache();
                    this._flash('Polygon berhasil disimpan.', 'success');
                }
            } catch (e) {
                this._flash('Gagal menyimpan polygon: ' + e.message, 'error');
            }
        },

        async _updatePolygonGeometry(layer) {
            const poly = this.polygonList.find(p => p.layer === layer);
            if (!poly || !poly.id) return;
            const lid = this.activeLayer.id;
            const url = LAYER_ROUTES.polygonBase + '/' + lid + '/polygon/' + poly.id;
            try {
                await SimPeta.apiPut(url, {
                    geojson: layer.toGeoJSON().geometry
                });
                this._updateLayerGeojsonCache();
                this._flash('Polygon diperbarui.', 'success');
            } catch (e) {
                this._flash('Gagal memperbarui polygon.', 'error');
            }
        },

        async updatePolygonName(poly, newName) {
            if (!poly.id || !this.activeLayer) return;
            poly.nama = newName;
            const lid = this.activeLayer.id;
            const url = LAYER_ROUTES.polygonBase + '/' + lid + '/polygon/' + poly.id;
            try {
                await SimPeta.apiPut(url, {
                    nama: newName
                });
            } catch (e) {
                console.error('Failed to update polygon name:', e);
            }
        },

        async deletePolygon(poly, index) {
            if (!confirm('Hapus polygon ini?')) return;
            if (poly.id) await this._deletePolygonFromServer(poly.id);
            if (poly.layer && this._editor && this._editor.drawnItems) this._editor.drawnItems.removeLayer(poly
                .layer);
            this.polygonList.splice(index, 1);
            this.activePolygonCount = this.polygonList.length;
            if (this.activeLayer) {
                this._updateLayerPolygonCount(this.activeLayer.id, this.activePolygonCount);
            }
            this._updateLayerGeojsonCache();
            this._flash('Polygon dihapus.', 'success');
        },

        async _deletePolygonFromServer(id) {
            const lid = this.activeLayer.id;
            const url = LAYER_ROUTES.polygonBase + '/' + lid + '/polygon/' + id;
            try {
                await SimPeta.apiDelete(url);
            } catch (e) {
                console.error(e);
            }
        },

        _updateLayerPolygonCount(layerId, count) {
            const l = this.layers.find(x => x.id === layerId);
            if (l) l.polygons_count = count;
        },

        _updateLayerGeojsonCache() {
            if (!this.activeLayer) return;
            // Rebuild the geojson cache from current drawn items
            const features = [];
            this.polygonList.forEach(p => {
                if (p.layer) {
                    features.push({
                        type: 'Feature',
                        properties: {
                            id: p.id,
                            nama: p.nama
                        },
                        geometry: p.layer.toGeoJSON().geometry,
                    });
                }
            });
            LAYERS_GEOJSON[this.activeLayer.id] = {
                type: 'FeatureCollection',
                features,
            };
        },

        zoomToPolygon(poly) {
            if (poly.layer && this._editor) {
                this._editor.zoomToLayer(poly.layer);
            }
        },

        // ─── Layer visibility ──────────────────────────
        toggleLayerVisibility(layer) {
            if (!this._editor || !this._editor.map) return;
            layer.visible = !layer.visible;
            const ml = this._displayLayers[layer.id];
            if (ml) {
                if (layer.visible) {
                    this._editor.map.addLayer(ml);
                } else {
                    this._editor.map.removeLayer(ml);
                }
            }
            // If this is the active layer, toggle the editable drawn items
            if (this.activeLayer?.id === layer.id && this._editor.drawnItems) {
                if (layer.visible) {
                    this._editor.map.addLayer(this._editor.drawnItems);
                } else {
                    this._editor.map.removeLayer(this._editor.drawnItems);
                }
            }
        },

        // ─── Layer actions ─────────────────────────────
        async toggleActive(layer) {
            const url = LAYER_ROUTES.toggleActive + '/' + layer.id + '/toggle-active';
            try {
                const res = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (data.success) {
                    layer.is_active = data.is_active;
                    this._flash(layer.is_active ? 'Layer diaktifkan.' : 'Layer dinonaktifkan.', 'success');
                }
            } catch (e) {
                this._flash('Gagal mengubah status.', 'error');
            }
        },

        async deleteLayer(layer) {
            if (!confirm('Hapus layer "' + layer.nama + '" beserta semua polygonnya?')) return;
            const url = LAYER_ROUTES.destroyJson + '/' + layer.id + '/destroy-json';
            try {
                await SimPeta.apiDelete(url);

                // Remove from map
                this._removeDisplayLayer(layer.id);
                if (this.activeLayer?.id === layer.id) {
                    if (this._editor) this._editor.clearDrawn();
                    if (this._editor && this._editor.drawControl && this._editor.map) this._editor.map
                        .removeControl(this._editor.drawControl);
                    this.activeLayer = null;
                    this.polygonList = [];
                    this.activePolygonCount = 0;
                }

                // Remove from list
                this.layers = this.layers.filter(l => l.id !== layer.id);
                delete LAYERS_GEOJSON[layer.id];
                this._flash('Layer berhasil dihapus.', 'success');
            } catch (e) {
                this._flash('Gagal menghapus layer.', 'error');
            }
        },

        zoomToLayer(layer) {
            if (!this._editor || !this._editor.map) return;
            const ml = this._displayLayers[layer.id];
            if (ml && ml.getBounds && ml.getBounds().isValid()) {
                this._editor.map.fitBounds(ml.getBounds(), {
                    padding: [50, 50]
                });
                return;
            }
            // Check if it's the active editable layer
            if (this.activeLayer?.id === layer.id && this._editor.drawnItems && this._editor.drawnItems.getLayers()
                .length > 0) {
                this._editor.map.fitBounds(this._editor.drawnItems.getBounds(), {
                    padding: [50, 50]
                });
            }
        },

        // ─── Drag & Drop reorder ───────────────────────
        onDragStart(event, index) {
            this._dragIndex = index;
            event.target.classList.add('dragging');
            event.dataTransfer.effectAllowed = 'move';
        },

        onDragOver(event, index) {
            event.preventDefault();
            event.dataTransfer.dropEffect = 'move';

            // Visual feedback
            const items = document.querySelectorAll('.layer-item');
            items.forEach(i => i.classList.remove('drag-over'));
            event.currentTarget.classList.add('drag-over');
        },

        onDragLeave(event) {
            event.currentTarget.classList.remove('drag-over');
        },

        onDrop(event, targetIndex) {
            event.preventDefault();
            event.currentTarget.classList.remove('drag-over');

            if (this._dragIndex === null || this._dragIndex === targetIndex) return;

            // Reorder array
            const moved = this.layers.splice(this._dragIndex, 1)[0];
            this.layers.splice(targetIndex, 0, moved);

            // Save to server
            this._saveOrder();
            this._dragIndex = null;
        },

        onDragEnd(event) {
            event.target.classList.remove('dragging');
            document.querySelectorAll('.layer-item').forEach(i => i.classList.remove('drag-over'));
            this._dragIndex = null;
        },

        async _saveOrder() {
            const order = this.layers.map(l => l.id);
            try {
                await SimPeta.apiPost(LAYER_ROUTES.reorder, {
                    order
                });

                // Reorder map layers (z-index)
                this.layers.forEach((layer, idx) => {
                    layer.sort_order = idx;
                    const ml = this._displayLayers[layer.id];
                    if (ml) ml.bringToFront();
                });

                this._flash('Urutan layer diperbarui.', 'success');
            } catch (e) {
                this._flash('Gagal menyimpan urutan.', 'error');
            }
        },

        // ─── Modal: New Layer ──────────────────────────
        openNewLayerModal() {
            this.editingLayer = null;
            this.modalForm = {
                nama: '',
                deskripsi: '',
                warna: '#3b82f6',
                fill_opacity: 0.30,
                stroke_width: 2.0,
                pattern_type: 'solid',
                is_active: true,
            };
            document.getElementById('layer-settings-modal').showModal();
        },

        openEditLayerModal(layer) {
            this.editingLayer = layer;
            this.modalForm = {
                nama: layer.nama,
                deskripsi: layer.deskripsi || '',
                warna: layer.warna,
                fill_opacity: layer.fill_opacity,
                stroke_width: layer.stroke_width,
                pattern_type: layer.pattern_type,
                is_active: layer.is_active,
            };
            document.getElementById('layer-settings-modal').showModal();
        },

        async saveLayerSettings() {
            if (this.editingLayer) {
                // Update existing
                const url = LAYER_ROUTES.updateJson + '/' + this.editingLayer.id + '/update-json';
                try {
                    const data = await SimPeta.apiPut(url, this.modalForm);
                    if (data.success) {
                        // Update local state
                        Object.assign(this.editingLayer, data.layer);
                        this.editingLayer.visible = true;

                        // Re-render display
                        this._removeDisplayLayer(this.editingLayer.id);
                        if (this.activeLayer?.id !== this.editingLayer.id) {
                            this._renderDisplayLayer(this.editingLayer);
                        } else {
                            // Re-configure editor colors
                            this._editor.updateColor(data.layer.warna);
                            this._addDrawControl(this.editingLayer);
                        }

                        this._flash('Layer berhasil diperbarui.', 'success');
                    }
                } catch (e) {
                    this._flash('Gagal menyimpan: ' + e.message, 'error');
                }
            } else {
                // Create new
                try {
                    const data = await SimPeta.apiPost(LAYER_ROUTES.storeJson, this.modalForm);
                    if (data.success) {
                        const newLayer = {
                            ...data.layer,
                            polygons_count: 0,
                            visible: true,
                        };
                        this.layers.push(newLayer);
                        LAYERS_GEOJSON[newLayer.id] = {
                            type: 'FeatureCollection',
                            features: []
                        };
                        this._flash('Layer berhasil dibuat.', 'success');

                        // Auto-select the new layer
                        this.$nextTick(() => this.selectLayer(newLayer));
                    }
                } catch (e) {
                    this._flash('Gagal membuat layer: ' + e.message, 'error');
                }
            }
            document.getElementById('layer-settings-modal').close();
        },

        // ─── Map controls ─────────────────────────────
        toggleRwOverlay() {
            this.showRwOverlay = !this.showRwOverlay;
            if (this._editor) this._editor.toggleRwOverlay(this.showRwOverlay);
        },

        resetZoom() {
            if (!this._editor || !this._editor.map) return;
            if (this._editor.rwOverlay) {
                // Fit to RW bounds
                let hasLayers = false;
                this._editor.rwOverlay.eachLayer(l => {
                    if (l.getBounds) {
                        this._editor.map.fitBounds(l.getBounds(), {
                            padding: [30, 30]
                        });
                        hasLayers = true;
                    }
                });
                if (!hasLayers) {
                    this._editor.map.setView([-5.155, 119.466], 15);
                }
            } else {
                this._editor.map.setView([-5.155, 119.466], 15);
            }
        },

        // ─── Toast ─────────────────────────────────────
        _flash(msg, type = 'success') {
            this.toast = {
                show: true,
                message: msg,
                type
            };
            setTimeout(() => {
                this.toast.show = false;
            }, 3500);
        },
    };
}
</script>