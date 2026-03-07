window.layerPolygonEditor = function layerPolygonEditor() {
    return {
        _editor: null,
        polygonList: [],
        polygonCount: 0,
        message: '',
        messageType: 'success',

        init() {
            this._waitForDeps();
        },

        _waitForDeps() {
            if (window.SimPeta && window.L && window.L.Draw) {
                this._bootstrap();
            } else {
                requestAnimationFrame(() => this._waitForDeps());
            }
        },

        _bootstrap() {
            this._editor = new window.SimPeta.PolygonEditor('layer-polygon-map', {
                color: window.LAYER_CONFIG.color,
                fillOpacity: window.LAYER_CONFIG.opacity,
                strokeWidth: window.LAYER_CONFIG.strokeWidth,
                rectangle: true,
            }).init();

            // Load existing polygons
            this.polygonList = this._editor.loadExistingCollection(window.LAYER_CONFIG.existingData);
            this.polygonCount = this.polygonList.length;

            // Draw events (multi-polygon mode)
            this._editor.onMultiPolygonChange({
                onCreated: (layer) => this._saveNewPolygon(layer),
                onEdited: (layer) => this._updateGeometry(layer),
                onDeleted: (layer) => {
                    const poly = this.polygonList.find(p => p.layer === layer);
                    if (poly && poly.id) this._deleteFromServer(poly.id);
                    this.polygonList = this.polygonList.filter(p => p.layer !== layer);
                    this.polygonCount = this.polygonList.length;
                },
            });
        },

        zoomToPolygon(poly) {
            this._editor.zoomToLayer(poly.layer);
        },

        async _saveNewPolygon(layer) {
            try {
                const geojson = layer.toGeoJSON().geometry;
                const data = await window.SimPeta.apiPost(window.LAYER_ROUTES.polygonStore, {
                    geojson,
                    nama: 'Polygon ' + (this.polygonList.length + 1),
                });
                if (data.success) {
                    this.polygonList.push({
                        id: data.id,
                        nama: 'Polygon ' + this.polygonList.length,
                        layer
                    });
                    this.polygonCount = this.polygonList.length;
                    this._flash('Polygon berhasil disimpan.', 'success');
                }
            } catch (e) {
                this._flash('Gagal menyimpan polygon: ' + e.message, 'error');
            }
        },

        async _updateGeometry(layer) {
            const poly = this.polygonList.find(p => p.layer === layer);
            if (!poly || !poly.id) return;
            try {
                await window.SimPeta.apiPut(window.LAYER_ROUTES.polygonBase + '/' + poly.id, {
                    geojson: layer.toGeoJSON().geometry,
                });
                this._flash('Polygon berhasil diperbarui.', 'success');
            } catch (e) {
                this._flash('Gagal memperbarui polygon.', 'error');
            }
        },

        async updatePolygonName(poly, newName) {
            if (!poly.id) return;
            poly.nama = newName;
            try {
                await window.SimPeta.apiPut(window.LAYER_ROUTES.polygonBase + '/' + poly.id, {
                    nama: newName
                });
            } catch (e) {
                console.error('Failed to update polygon name:', e);
            }
        },

        async deletePolygon(poly, index) {
            if (!await confirmAction('Hapus polygon ini?')) return;
            if (poly.id) await this._deleteFromServer(poly.id);
            if (poly.layer) this._editor.drawnItems.removeLayer(poly.layer);
            this.polygonList.splice(index, 1);
            this.polygonCount = this.polygonList.length;
            this._flash('Polygon berhasil dihapus.', 'success');
        },

        async _deleteFromServer(id) {
            try {
                await window.SimPeta.apiDelete(window.LAYER_ROUTES.polygonBase + '/' + id);
            } catch (e) {
                console.error('Failed to delete polygon:', e);
            }
        },

        _flash(msg, type = 'success') {
            this.message = msg;
            this.messageType = type;
            setTimeout(() => {
                this.message = '';
            }, 4000);
        },
    };
};
