window.rwPolygonEditor = function rwPolygonEditor() {
    return {
        _editor: null,
        hasChanges: false,
        hasExisting: window.RW_EDITOR.hasExisting,
        editorReady: false,
        saving: false,
        savingColor: false,
        currentColor: window.RW_EDITOR.currentWarna,
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
            // Create editor via engine
            this._editor = new window.SimPeta.PolygonEditor('rw-polygon-map', {
                color: this.currentColor,
            }).init();

            if (!this._editor?.map || !this._editor?.drawnItems) {
                // Recovery path: stale Leaflet container (e.g. bfcache / double init)
                const container = document.getElementById('rw-polygon-map');
                if (container && container._leaflet_id) {
                    try {
                        const fresh = container.cloneNode(false);
                        fresh.id = 'rw-polygon-map';
                        container.parentNode.replaceChild(fresh, container);
                    } catch (_) {}

                    this._editor = new window.SimPeta.PolygonEditor('rw-polygon-map', {
                        color: this.currentColor,
                    }).init();
                }
            }

            if (!this._editor?.map || !this._editor?.drawnItems) {
                this.editorReady = false;
                this._flash('Editor peta belum siap. Muat ulang halaman lalu coba lagi.', 'error');
                return;
            }
            this.editorReady = true;

            // Kelurahan boundary
            this._editor.addKelurahan(window.RW_EDITOR.kelurahanGeojson);

            // Other RW polygons (reference)
            this._editor.addRwReference(window.RW_EDITOR.allRwPolygons);

            // Existing polygon
            if (window.RW_EDITOR.polygonGeojson) {
                this._editor.loadExisting(window.RW_EDITOR.polygonGeojson, this.currentColor);
            }

            // Sync real state from map layers (avoid stale state from server flag)
            this.hasExisting = !!this._editor.getGeometry();
            this.hasChanges = false;

            // Draw events
            this._editor.onSinglePolygonChange((changed) => {
                this.hasChanges = changed;
                this.hasExisting = !!this._editor.getGeometry();
            });
        },

        async savePolygon() {
            if (!this.editorReady || !this._editor) {
                this._flash('Editor peta belum siap.', 'error');
                return;
            }

            const geojson = this._editor.getGeometry();
            if (!geojson) {
                // If user removed polygon using draw delete toolbar,
                // persist deletion through the same Save action.
                if (this.hasExisting && this.hasChanges) {
                    await this.deletePolygon(false);
                    return;
                }
                this._flash('Gambar polygon terlebih dahulu.', 'error');
                return;
            }
            this.saving = true;
            try {
                const data = await window.SimPeta.apiPut(window.RW_EDITOR.routes.update, {
                    geojson,
                    warna: this.currentColor,
                });
                if (data.success) {
                    this.hasChanges = false;
                    this.hasExisting = true;
                    this._flash(data.message, 'success');
                } else {
                    this._flash(data.message || 'Gagal menyimpan.', 'error');
                }
            } catch (err) {
                this._flash('Terjadi kesalahan: ' + err.message, 'error');
            } finally {
                this.saving = false;
            }
        },

        async deletePolygon(askConfirm = true) {
            if (askConfirm && !await confirmAction('Hapus polygon RW ini?')) return;
            this.saving = true;
            try {
                const data = await window.SimPeta.apiDelete(window.RW_EDITOR.routes.delete);
                if (data.success) {
                    this._editor.clearDrawn();
                    this.hasChanges = false;
                    this.hasExisting = false;
                    this._flash(data.message, 'success');
                } else {
                    this._flash(data.message || 'Gagal menghapus.', 'error');
                }
            } catch (err) {
                this._flash('Terjadi kesalahan: ' + err.message, 'error');
            } finally {
                this.saving = false;
            }
        },

        async saveColor() {
            if (!this.editorReady || !this._editor) {
                this._flash('Editor peta belum siap.', 'error');
                return;
            }
            this.savingColor = true;
            try {
                const data = await window.SimPeta.apiPut(window.RW_EDITOR.routes.colorUpdate, {
                    warna: this.currentColor,
                });
                if (data.success) {
                    this._flash(data.message, 'success');
                    this._editor.updateColor(this.currentColor);
                } else {
                    this._flash(data.message || 'Gagal menyimpan warna.', 'error');
                }
            } catch (err) {
                this._flash('Terjadi kesalahan: ' + err.message, 'error');
            } finally {
                this.savingColor = false;
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
