{{-- Scripts for QGIS-style Layer Manager --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
@vite('resources/js/map/index.js')

@php
$layerRoutes = [
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

        // ─── Per-polygon edit state ────────────────────
        editingPolygon: null, // { id, nama, layerId, layer (L.Layer) }
        _layerPolygonLists: {}, // layerId -> [{ id, nama, layer }]

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

        // ─── Polygon modal state ───────────────────────
        _editingPolygonMeta: null, // { id, layerId }
        polygonModalForm: {
            nama: '',
            warna: '#6366f1',
        },

        // ─── Toast ─────────────────────────────────────
        toast: {
            show: false,
            message: '',
            type: 'success'
        },

        // ─── Highlighted polygon (via map click) ──────
        highlightedPolygonId: null,

        // ─── Drag ──────────────────────────────────────
        _dragIndex: null,
        _polyDragIndex: null,
        _polyDragLayerId: null,
        _polyDragOver: null, // { layerId, index }

        // ─── Engine ────────────────────────────────────
        _editor: null,
        _rwOverlayGroup: null,

        // ─── Init ──────────────────────────────────────
        init() {
            this.layers = JSON.parse(JSON.stringify(INITIAL_LAYERS));
            this.layers.forEach(l => {
                l.visible = true;
                l._expanded = false;
            });
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

            // Reposition zoom control to avoid overlap with custom top-left toolbar
            if (this._editor?.map?.zoomControl) {
                this._editor.map.zoomControl.setPosition('bottomright');
            }

            // Remove draw control initially (no layer selected)
            if (this._editor.drawControl) {
                this._editor.removeDrawControl();
            }

            // Render all layers as read-only display
            this._renderAllDisplayLayers();

            // Build polygon lists from GeoJSON cache for tree display
            this._buildAllPolygonLists();

            // Bind draw events
            this._bindDrawEvents();

            this.loading = false;
            this.$nextTick(() => {
                if (this._editor.map) this._editor.map.invalidateSize();
            });
        },

        // ─── Render display layers (read-only) ─────────
        _renderAllDisplayLayers() {
            // Render in reverse so the first layer (top of sidebar) is added last → renders on top
            for (let i = this.layers.length - 1; i >= 0; i--) {
                this._renderDisplayLayer(this.layers[i]);
            }
        },

        _renderDisplayLayer(layer) {
            if (!this._editor || !this._editor.map) return;
            const geojson = LAYERS_GEOJSON[layer.id];
            this._editor.renderDisplayLayer(layer, geojson, (polyId, layerId) => {
                this._onMapPolygonClick(layerId, polyId);
            });
        },

        _onMapPolygonClick(layerId, polyId) {
            // Normalize to numbers to handle PostgreSQL PDO string IDs
            const lid = Number(layerId);
            const pid = Number(polyId);

            // Find the parent layer
            const layer = this.layers.find(l => Number(l.id) === lid);
            if (!layer) return;

            // Expand tree + highlight
            layer._expanded = true;
            this.highlightedPolygonId = pid;

            // Find the polygon entry in the tree list
            const treeList = this._layerPolygonLists[layer.id] || [];
            const poly = treeList.find(p => Number(p.id) === pid);
            if (!poly) return;

            // Enter edit mode (this also selects the layer, zooms, etc.)
            const idx = treeList.indexOf(poly);
            this.selectPolygonForEdit(layer, poly, idx);

            // Scroll the subtree item into view after Alpine re-renders
            this.$nextTick(() => {
                const el = document.querySelector('[data-poly-id="' + pid + '"]');
                if (el) el.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            });
        },

        _removeDisplayLayer(layerId) {
            this._editor.removeDisplayLayer(layerId);
        },

        // ─── Build polygon lists for tree ──────────────
        _buildAllPolygonLists() {
            this.layers.forEach(layer => {
                this._buildPolygonList(layer);
            });
        },

        _buildPolygonList(layer) {
            const geojson = LAYERS_GEOJSON[layer.id];
            this._layerPolygonLists[layer.id] = this._editor.extractPolygonList(geojson);
        },

        getLayerPolygons(layer) {
            return this._layerPolygonLists[layer.id] || [];
        },

        // ─── Tree expand/collapse ──────────────────────
        toggleTreeExpand(layer) {
            layer._expanded = !layer._expanded;
        },

        // ─── Per-polygon edit ──────────────────────────
        selectPolygonForEdit(layer, poly, idx) {
            // If already editing this polygon, do nothing
            if (Number(this.editingPolygon?.id) === Number(poly.id) && Number(this.editingPolygon?.layerId) === Number(
                    layer.id)) return;

            // Stop any current single-polygon edit
            this._stopSinglePolygonEdit();

            // Make sure this layer is active (loads all polygons into editor)
            if (Number(this.activeLayer?.id) !== Number(layer.id)) {
                this.selectLayer(layer);
            }

            // Find the matching polygon entry in polygonList (which has .layer ref)
            const polyEntry = this.polygonList.find(p => Number(p.id) === Number(poly.id));
            if (!polyEntry || !polyEntry.layer) return;

            this._editor.startSinglePolygonEdit(polyEntry.layer, layer, (clickedOriginalLayer) => {
                // Find the polygon entry matching the clicked stashed layer
                const clickedPoly = this.polygonList.find(p => p.layer === clickedOriginalLayer);
                if (!clickedPoly) return;
                const clickedTreePoly = (this._layerPolygonLists[layer.id] || []).find(p => Number(p.id) === Number(clickedPoly.id));
                if (!clickedTreePoly) return;
                // Switch to the clicked polygon (editingPolygon has a different ID, so the guard passes)
                this.selectPolygonForEdit(layer, clickedTreePoly, (this._layerPolygonLists[layer.id] || []).indexOf(clickedTreePoly));
            });

            // Set editing state
            this.editingPolygon = {
                id: poly.id,
                nama: poly.nama,
                layerId: layer.id,
                layer: polyEntry.layer,
            };
        },

        stopEditingPolygon() {
            this._stopSinglePolygonEdit();
        },

        _stopSinglePolygonEdit(restoreLayers = true) {
            if (!this.editingPolygon) return;

            this._editor.stopSinglePolygonEdit(this.activeLayer, restoreLayers);

            this.editingPolygon = null;
        },

        // ─── Select layer for editing ──────────────────
        selectLayer(layer) {
            if (!this._editor || !this._editor.map) return;
            if (this.activeLayer?.id === layer.id) return;

            // Stop any single-polygon edit
            this._stopSinglePolygonEdit(false);

            // Deselect current
            this._deactivateCurrentLayer();

            this.activeLayer = layer;
            this.highlightedPolygonId = null;

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

            // Update the tree polygon list to include layer refs
            this._layerPolygonLists[layer.id] = this._editor.extractPolygonList(LAYERS_GEOJSON[layer.id]);

            // Auto-expand tree
            layer._expanded = true;

            // Re-add draw control with layer's color
            this._addDrawControl(layer);

            // Zoom to layer bounds if has polygons
            if (this.polygonList.length > 0 && this._editor.drawnItems.getLayers().length > 0) {
                this._editor.fitBoundsNoAnim(this._editor.drawnItems.getBounds(), [50, 50]);
            }
        },

        _deactivateCurrentLayer() {
            if (!this.activeLayer) return;
            if (!this._editor || !this._editor.map) return;

            // Stop single-polygon edit if active
            this._stopSinglePolygonEdit(false);

            // Stop all draw/edit handlers before removing controls/layers.
            this._editor.disableDrawModes();

            // Remove draw control
            if (this._editor.drawControl) {
                this._editor.removeDrawControl();
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
            this._editor.replaceDrawControl(layer);
        },

        // ─── Draw events ───────────────────────────────
        _bindDrawEvents() {
            if (!this._editor) return;

            this._editor.onMultiPolygonChange({
                onCreated: (layer) => {
                    if (!this.activeLayer) return;
                    if (layer.options) layer.options.pane = 'editPane';
                    this._saveNewPolygon(layer);
                },
                onEdited: (layer) => {
                    this._updatePolygonGeometry(layer);
                },
                onDeleted: (layer) => {
                    const poly = this.polygonList.find(p => p.layer === layer);
                    if (poly && poly.id) this._deletePolygonFromServer(poly.id);
                    this.polygonList = this.polygonList.filter(p => p.layer !== layer);
                    this.activePolygonCount = this.polygonList.length;
                    this._updateLayerGeojsonCache();

                    // Sync tree polygon list
                    if (this.activeLayer) {
                        this._layerPolygonLists[this.activeLayer.id] = this._editor.extractPolygonList(
                            this._editor.toFeatureCollection(this.polygonList)
                        );
                        this._updateLayerPolygonCount(this.activeLayer.id, this.activePolygonCount);
                    }
                },
            });
        },

        // ─── Polygon CRUD ──────────────────────────────
        async _saveNewPolygon(layer) {
            const lid = this.activeLayer.id;
            try {
                const geojson = layer.toGeoJSON().geometry;
                const data = await this._editor.createLayerPolygon(
                    LAYER_ROUTES.polygonBase,
                    lid,
                    geojson,
                    'Polygon ' + (this.polygonList.length + 1),
                );
                if (data.success) {
                    this.polygonList.push({
                        id: data.id,
                        nama: 'Polygon ' + (this.polygonList.length + 1),
                        warna: '#6366f1',
                        layer,
                    });
                    this.activePolygonCount = this.polygonList.length;
                    this._updateLayerPolygonCount(lid, this.activePolygonCount);
                    this._updateLayerGeojsonCache();

                    // Update tree polygon list
                    this._layerPolygonLists[lid] = this._editor.extractPolygonList(
                        this._editor.toFeatureCollection(this.polygonList)
                    );

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
            try {
                await this._editor.updateLayerPolygon(LAYER_ROUTES.polygonBase, lid, poly.id, {
                    geojson: layer.toGeoJSON().geometry
                });
                this._updateLayerGeojsonCache();
                this._flash('Polygon diperbarui.', 'success');
            } catch (e) {
                this._flash('Gagal memperbarui polygon.', 'error');
            }
        },

        async updatePolygonName(poly, newName) {
            if (!poly.id) return;
            poly.nama = newName;

            // Also update in polygonList
            const polyEntry = this.polygonList.find(p => p.id === poly.id);
            if (polyEntry) polyEntry.nama = newName;

            // Find which layer this polygon belongs to
            const lid = this.activeLayer?.id;
            if (!lid) return;

            try {
                await this._editor.updateLayerPolygon(LAYER_ROUTES.polygonBase, lid, poly.id, {
                    nama: newName
                });
            } catch (e) {
                console.error('Failed to update polygon name:', e);
            }
        },

        // ─── Polygon settings modal ────────────────────
        openEditPolygonModal(layer, poly) {
            this._editingPolygonMeta = {
                id: poly.id,
                layerId: layer.id
            };
            this.polygonModalForm = {
                nama: poly.nama || '',
                warna: poly.warna || layer.warna || '#6366f1',
            };
            document.getElementById('polygon-settings-modal').showModal();
        },

        async savePolygonSettings() {
            const meta = this._editingPolygonMeta;
            if (!meta) return;

            const {
                nama,
                warna
            } = this.polygonModalForm;

            // Update in _layerPolygonLists
            const treeList = this._layerPolygonLists[meta.layerId];
            if (treeList) {
                const treePoly = treeList.find(p => p.id === meta.id);
                if (treePoly) {
                    treePoly.nama = nama;
                    treePoly.warna = warna;
                }
            }

            // Update in polygonList (if active layer)
            const polyEntry = this.polygonList.find(p => p.id === meta.id);
            if (polyEntry) {
                polyEntry.nama = nama;
                polyEntry.warna = warna;
            }

            // Save to server
            try {
                await this._editor.updateLayerPolygon(LAYER_ROUTES.polygonBase, meta.layerId, meta.id, {
                    nama,
                    warna,
                });

                // Always patch LAYERS_GEOJSON for this polygon so re-renders use the new warna
                const cachedGeojson = LAYERS_GEOJSON[meta.layerId];
                if (cachedGeojson?.features) {
                    const f = cachedGeojson.features.find(feat => feat?.properties?.id === meta.id);
                    if (f?.properties) {
                        f.properties.nama = nama;
                        f.properties.warna = warna;
                    }
                }

                this._updateLayerGeojsonCache();

                // Re-render display layer if not actively editing this layer
                if (this.activeLayer?.id !== meta.layerId) {
                    const layer = this.layers.find(l => l.id === meta.layerId);
                    if (layer) {
                        this._removeDisplayLayer(meta.layerId);
                        this._renderDisplayLayer(layer);
                    }
                }

                this._flash('Polygon berhasil diperbarui.', 'success');
            } catch (e) {
                this._flash('Gagal menyimpan: ' + e.message, 'error');
            }

            this._editingPolygonMeta = null;
            document.getElementById('polygon-settings-modal').close();
        },

        async deletePolygon(poly, index) {
            if (!await confirmAction('Hapus polygon ini?')) return;

            // If this polygon is being edited, stop editing first
            if (this.editingPolygon?.id === poly.id) {
                this._stopSinglePolygonEdit();
            }

            // Find the matching entry in polygonList (which has .layer ref)
            const polyEntry = this.polygonList.find(p => p.id === poly.id);

            if (poly.id) await this._deletePolygonFromServer(poly.id);
            if (polyEntry?.layer && this._editor && this._editor.drawnItems) {
                this._editor.drawnItems.removeLayer(polyEntry.layer);
            }
            this.polygonList = this.polygonList.filter(p => p.id !== poly.id);
            this.activePolygonCount = this.polygonList.length;
            if (this.activeLayer) {
                this._updateLayerPolygonCount(this.activeLayer.id, this.activePolygonCount);

                // Update tree polygon list
                this._layerPolygonLists[this.activeLayer.id] = this._editor.extractPolygonList(
                    this._editor.toFeatureCollection(this.polygonList)
                );
            }
            this._updateLayerGeojsonCache();
            this._flash('Polygon dihapus.', 'success');
        },

        async _deletePolygonFromServer(id) {
            const lid = this.activeLayer.id;
            try {
                await this._editor.deleteLayerPolygon(LAYER_ROUTES.polygonBase, lid, id);
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
            LAYERS_GEOJSON[this.activeLayer.id] = this._editor.toFeatureCollection(this.polygonList);
        },

        zoomToPolygon(layer, poly) {
            if (!this._editor) return;

            // Case 1: polygon is in the active layer — use .layer ref from polygonList
            const polyEntry = this.polygonList.find(p => p.id === poly.id);
            if (polyEntry?.layer) {
                this._editor.zoomToLayer(polyEntry.layer);
                return;
            }

            // Case 2: polygon is in a non-active (display) layer — build bounds from LAYERS_GEOJSON
            const geojson = LAYERS_GEOJSON[layer.id];
            if (geojson?.features?.length) {
                // Find by feature index or by id
                const featureIdx = poly._featureIndex;
                const feature = (featureIdx != null && geojson.features[featureIdx]?.properties?.id === poly.id) ?
                    geojson.features[featureIdx] :
                    geojson.features.find(f => f?.properties?.id === poly.id);

                if (feature?.geometry) {
                    try {
                        const tempLayer = L.geoJSON(feature);
                        const bounds = tempLayer.getBounds();
                        if (bounds.isValid()) {
                            this._editor.fitBoundsNoAnim(bounds, [80, 80]);
                            return;
                        }
                    } catch (_) {}
                }

                // Fallback: zoom to entire layer display bounds
                const displayLayer = this._editor.getDisplayLayer(layer.id);
                if (displayLayer?.getBounds?.().isValid()) {
                    this._editor.fitBoundsNoAnim(displayLayer.getBounds(), [50, 50]);
                }
            }
        },

        // ─── Layer visibility ──────────────────────────
        toggleLayerVisibility(layer) {
            if (!this._editor || !this._editor.map) return;
            layer.visible = !layer.visible;
            const ml = this._editor.getDisplayLayer(layer.id);
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
            if (!await confirmAction('Hapus layer "' + layer.nama + '" beserta semua polygonnya?')) return;
            const url = LAYER_ROUTES.destroyJson + '/' + layer.id + '/destroy-json';
            try {
                await SimPeta.apiDelete(url);

                // Remove from map
                this._removeDisplayLayer(layer.id);
                if (this.activeLayer?.id === layer.id) {
                    if (this._editor) this._editor.clearDrawn();
                    if (this._editor && this._editor.drawControl) {
                        this._editor.removeDrawControl();
                    }
                    this.activeLayer = null;
                    this.polygonList = [];
                    this.activePolygonCount = 0;
                }

                // Remove from list
                this.layers = this.layers.filter(l => l.id !== layer.id);
                delete LAYERS_GEOJSON[layer.id];
                delete this._layerPolygonLists[layer.id];
                this._flash('Layer berhasil dihapus.', 'success');
            } catch (e) {
                this._flash('Gagal menghapus layer.', 'error');
            }
        },

        zoomToLayer(layer) {
            if (!this._editor || !this._editor.map) return;
            const ml = this._editor.getDisplayLayer(layer.id);
            if (ml && ml.getBounds && ml.getBounds().isValid()) {
                this._editor.fitBoundsNoAnim(ml.getBounds(), [50, 50]);
                return;
            }
            // Check if it's the active editable layer
            if (this.activeLayer?.id === layer.id && this._editor.drawnItems && this._editor.drawnItems.getLayers()
                .length > 0) {
                this._editor.fitBoundsNoAnim(this._editor.drawnItems.getBounds(), [50, 50]);
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

                // Reorder map layers — call bringToFront in reverse so top-of-list (index 0) ends up on top
                for (let i = this.layers.length - 1; i >= 0; i--) {
                    this.layers[i].sort_order = i;
                    const ml = this._editor.getDisplayLayer(this.layers[i].id);
                    if (ml) ml.bringToFront();
                }

                this._flash('Urutan layer diperbarui.', 'success');
            } catch (e) {
                this._flash('Gagal menyimpan urutan.', 'error');
            }
        },

        // ─── Polygon Drag & Drop reorder ───────────────
        onPolyDragStart(event, layer, index) {
            this._polyDragIndex = index;
            this._polyDragLayerId = layer.id;
            event.target.classList.add('dragging');
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', 'polygon');
        },

        onPolyDragOver(event, layer, index) {
            if (this._polyDragLayerId !== layer.id) return;
            event.dataTransfer.dropEffect = 'move';
            this._polyDragOver = {
                layerId: layer.id,
                index
            };
        },

        onPolyDragLeave(event) {
            this._polyDragOver = null;
        },

        onPolyDrop(event, layer, targetIndex) {
            this._polyDragOver = null;

            if (this._polyDragLayerId !== layer.id) return;
            if (this._polyDragIndex === null || this._polyDragIndex === targetIndex) return;

            const list = this._layerPolygonLists[layer.id];
            if (!list) return;

            // Reorder array
            const moved = list.splice(this._polyDragIndex, 1)[0];
            list.splice(targetIndex, 0, moved);

            // If this is the active layer, also reorder the polygonList
            if (this.activeLayer?.id === layer.id) {
                const activeEntry = this.polygonList.find(p => p.id === moved.id);
                if (activeEntry) {
                    const oldIdx = this.polygonList.indexOf(activeEntry);
                    if (oldIdx !== -1) {
                        this.polygonList.splice(oldIdx, 1);
                        // Find the correct target position in polygonList
                        const targetItem = list[targetIndex];
                        const targetEntry = this.polygonList.find(p => p.id === targetItem?.id);
                        const newIdx = targetEntry ? this.polygonList.indexOf(targetEntry) : targetIndex;
                        this.polygonList.splice(newIdx >= 0 ? newIdx : this.polygonList.length, 0, activeEntry);
                    }
                }
                this._updateLayerGeojsonCache();
            }

            // Save to server
            this._savePolygonOrder(layer.id, list);
            this._polyDragIndex = null;
            this._polyDragLayerId = null;
        },

        onPolyDragEnd(event) {
            event.target.classList.remove('dragging');
            this._polyDragIndex = null;
            this._polyDragLayerId = null;
            this._polyDragOver = null;
        },

        async _savePolygonOrder(layerId, list) {
            const order = list.map(p => p.id).filter(Boolean);
            if (order.length === 0) return;
            try {
                await this._editor.reorderPolygons(LAYER_ROUTES.polygonBase, layerId, order);
                this._flash('Urutan polygon diperbarui.', 'success');
            } catch (e) {
                this._flash('Gagal menyimpan urutan polygon.', 'error');
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
                            _expanded: true,
                        };
                        this.layers.push(newLayer);
                        LAYERS_GEOJSON[newLayer.id] = {
                            type: 'FeatureCollection',
                            features: []
                        };
                        this._layerPolygonLists[newLayer.id] = [];
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
        resetZoom() {
            if (!this._editor || !this._editor.map) return;
            // Try to fit to all display layers bounds
            let bounds = null;
            Object.values(this._editor.displayLayers).forEach(ml => {
                if (ml && ml.getBounds && ml.getBounds().isValid()) {
                    bounds = bounds ? bounds.extend(ml.getBounds()) : ml.getBounds();
                }
            });
            if (bounds) {
                this._editor.fitBoundsNoAnim(bounds, [30, 30]);
            } else {
                this._editor.setViewNoAnim([-5.155, 119.466], 15);
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