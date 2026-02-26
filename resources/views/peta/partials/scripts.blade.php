{{-- Leaflet JS & Alpine.js petaApp() logic --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    function petaApp() {
        return {
            // ─── State ─────────────────────────────────────────
            map: null,
            svgRenderer: null,
            rwLayer: null,
            kelurahanLayer: null,
            labelLayer: null,
            showKelurahan: true,
            showLabels: true,
            loading: true,
            selectedRw: null,
            selectedStats: {},
            highlightedLayer: null,
            rwLayerMap: {},
            rwDataList: [],
            batuaBounds: null,
            globalStats: {
                total_penduduk: 0,
                total_kk: 0,
                total_rw: 0,
                total_rt: 0,
                total_umkm: 0,
                laki_laki: 0,
                perempuan: 0,
            },

            // ─── RW Color Map ──────────────────────────────────
            rwColors: {
                'RW 01': '#6366f1',
                'RW 02': '#8b5cf6',
                'RW 03': '#ec4899',
                'RW 04': '#f43f5e',
                'RW 05': '#f97316',
                'RW 06': '#eab308',
                'RW 07': '#22c55e',
                'RW 08': '#14b8a6',
                'RW 09': '#06b6d4',
                'RW 10': '#3b82f6',
                'RW 11': '#a855f7',
                'RW 12': '#d946ef',
                'RW 13': '#78716c',
            },

            // ─── Computed ──────────────────────────────────────
            get sortedRwList() {
                return [...this.rwDataList].sort((a, b) => {
                    return parseInt(a.name.replace('RW ', '')) - parseInt(b.name.replace('RW ', ''));
                });
            },

            // ─── Lifecycle ─────────────────────────────────────
            init() {
                this.$nextTick(() => {
                    this.initMap();
                    this.loadData();
                });
            },

            // ─── Map Initialization ────────────────────────────
            initMap() {
                this.map = L.map('map', {
                    center: [-5.155, 119.466],
                    zoom: 15,
                    zoomControl: false,
                    scrollWheelZoom: false,
                    doubleClickZoom: false,
                    touchZoom: false,
                    boxZoom: false,
                    keyboard: false,
                    dragging: false,
                    preferCanvas: false,
                });

                this.svgRenderer = L.svg({ padding: 0.5 });

                const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>',
                    maxZoom: 19,
                });

                const satelliteLayer = L.tileLayer(
                    'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                        attribution: '&copy; Esri',
                        maxZoom: 19,
                    });

                osmLayer.addTo(this.map);

                L.control.layers({
                    'Peta': osmLayer,
                    'Satelit': satelliteLayer,
                }, null, {
                    position: 'topright'
                }).addTo(this.map);
            },

            // ─── Data Loading ──────────────────────────────────
            async loadData() {
                this.loading = true;
                try {
                    const [rwRes, kelRes, statsRes] = await Promise.all([
                        fetch('{{ route("peta.geojson.rw") }}'),
                        fetch('{{ route("peta.geojson.kelurahan") }}'),
                        fetch('{{ route("peta.stats") }}'),
                    ]);

                    if (!rwRes.ok || !kelRes.ok || !statsRes.ok) {
                        console.error('Fetch error');
                        return;
                    }

                    const rwData = await rwRes.json();
                    const kelData = await kelRes.json();
                    const statsData = await statsRes.json();

                    this.globalStats = statsData;

                    this.renderKelurahan(kelData);
                    this.renderRw(rwData);
                    this.injectHatchPatterns();
                    this.applyHatchPatterns();
                } catch (error) {
                    console.error('Error loading map data:', error);
                } finally {
                    this.loading = false;
                    this.$nextTick(() => {
                        if (this.map) this.map.invalidateSize();
                    });
                }
            },

            // ─── Render Kelurahan Boundary ─────────────────────
            renderKelurahan(data) {
                this.kelurahanLayer = L.geoJSON(data, {
                    style: {
                        color: '#1e293b',
                        weight: 3,
                        fillOpacity: 0.02,
                        fillColor: '#64748b',
                        dashArray: '10, 6',
                    },
                }).addTo(this.map);

                this.batuaBounds = this.kelurahanLayer.getBounds();
                this.map.fitBounds(this.batuaBounds, { padding: [15, 15] });
                this.map.setMaxBounds(this.batuaBounds.pad(0.05));
                this.map.setMinZoom(this.map.getZoom());
                this.map.setMaxZoom(this.map.getZoom());
            },

            // ─── Render RW Polygons ────────────────────────────
            renderRw(data) {
                const self = this;
                this.labelLayer = L.layerGroup().addTo(this.map);

                this.rwLayer = L.geoJSON(data, {
                    renderer: this.svgRenderer,
                    style: (feature) => {
                        const rw = feature.properties.RW;
                        const color = self.rwColors[rw] || '#6b7280';
                        return {
                            color: color,
                            weight: 2.5,
                            opacity: 0.9,
                            fillOpacity: 0.3,
                            fillColor: color,
                        };
                    },
                    filter: (feature) => {
                        return feature.properties && feature.properties.RW !== null && feature.properties.RW !== undefined;
                    },
                    onEachFeature: (feature, layer) => {
                        const props = feature.properties;
                        if (!props.RW) return;

                        self.rwLayerMap[props.RW] = layer;

                        // Collect data for sidebar list
                        self.rwDataList.push({
                            name: props.RW,
                            total_penduduk: props.total_penduduk || 0,
                            total_kk: props.total_kk || 0,
                            total_rt: props.total_rt || 0,
                            total_umkm: props.total_umkm || 0,
                            laki_laki: props.laki_laki || 0,
                            perempuan: props.perempuan || 0,
                        });

                        // Tooltip
                        layer.bindTooltip(props.RW, {
                            permanent: false,
                            direction: 'center',
                            className: 'rw-label',
                            sticky: true,
                        });

                        // Permanent label
                        const center = layer.getBounds().getCenter();
                        const label = L.marker(center, {
                            icon: L.divIcon({
                                className: 'rw-label',
                                html: `<span>${props.RW}</span>`,
                                iconSize: [50, 18],
                                iconAnchor: [25, 9],
                            }),
                            interactive: false,
                        });
                        self.labelLayer.addLayer(label);

                        // Hover effects
                        layer.on('mouseover', function(e) {
                            if (self.highlightedLayer === this) return;
                            this.setStyle({ weight: 4, fillOpacity: 0.5, opacity: 1 });
                            if (this._path) {
                                const color = self.rwColors[props.RW] || '#6b7280';
                                this._path.style.fill = color;
                                this._path.style.fillOpacity = '0.5';
                            }
                            this.bringToFront();
                            if (self.kelurahanLayer) self.kelurahanLayer.bringToFront();
                        });

                        layer.on('mouseout', function(e) {
                            if (self.highlightedLayer !== this) {
                                self.rwLayer.resetStyle(this);
                                self.applyHatchToLayer(this);
                            }
                        });

                        layer.on('click', function(e) {
                            self.selectRw(props.RW);
                        });
                    },
                }).addTo(this.map);
            },

            // ─── RW Selection ──────────────────────────────────
            selectRw(rwName) {
                if (this.selectedRw === rwName) {
                    this.resetView();
                    return;
                }

                // Reset previous
                if (this.highlightedLayer) {
                    this.rwLayer.resetStyle(this.highlightedLayer);
                    this.applyHatchToLayer(this.highlightedLayer);
                }

                const layer = this.rwLayerMap[rwName];
                if (!layer) return;

                this.highlightedLayer = layer;
                this.selectedRw = rwName;

                const rwData = this.rwDataList.find(r => r.name === rwName);
                this.selectedStats = rwData ? { ...rwData } : {};

                // Highlight
                layer.setStyle({ weight: 4, fillOpacity: 0.55, opacity: 1 });
                if (layer._path) {
                    const color = this.rwColors[rwName] || '#6b7280';
                    layer._path.style.fill = color;
                    layer._path.style.fillOpacity = '0.55';
                }
                layer.bringToFront();
                if (this.kelurahanLayer) this.kelurahanLayer.bringToFront();

                // Scroll sidebar
                this.$nextTick(() => {
                    const activeItem = this.$el.querySelector('.rw-list-item.active');
                    if (activeItem) {
                        activeItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                });
            },

            // ─── SVG Hatch Patterns ────────────────────────────
            injectHatchPatterns() {
                const svgEl = this.svgRenderer._container;
                if (!svgEl) return;

                let defs = svgEl.querySelector('defs');
                if (!defs) {
                    defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
                    svgEl.insertBefore(defs, svgEl.firstChild);
                }

                Object.entries(this.rwColors).forEach(([rw, color]) => {
                    const patternId = 'hatch-' + rw.replace(/\s/g, '-');

                    const pattern = document.createElementNS('http://www.w3.org/2000/svg', 'pattern');
                    pattern.setAttribute('id', patternId);
                    pattern.setAttribute('patternUnits', 'userSpaceOnUse');
                    pattern.setAttribute('width', '10');
                    pattern.setAttribute('height', '10');
                    pattern.setAttribute('patternTransform', 'rotate(45)');

                    const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                    rect.setAttribute('width', '10');
                    rect.setAttribute('height', '10');
                    rect.setAttribute('fill', color);
                    rect.setAttribute('fill-opacity', '0.18');

                    const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                    line.setAttribute('x1', '0');
                    line.setAttribute('y1', '0');
                    line.setAttribute('x2', '0');
                    line.setAttribute('y2', '10');
                    line.setAttribute('stroke', color);
                    line.setAttribute('stroke-width', '3');
                    line.setAttribute('stroke-opacity', '0.55');

                    pattern.appendChild(rect);
                    pattern.appendChild(line);
                    defs.appendChild(pattern);
                });
            },

            applyHatchPatterns() {
                if (!this.rwLayer) return;
                const self = this;
                this.rwLayer.eachLayer(layer => { self.applyHatchToLayer(layer); });
            },

            applyHatchToLayer(layer) {
                if (!layer.feature || !layer.feature.properties || !layer.feature.properties.RW) return;
                if (!layer._path) return;
                const rw = layer.feature.properties.RW;
                const patternId = 'hatch-' + rw.replace(/\s/g, '-');
                layer._path.style.fill = `url(#${patternId})`;
                layer._path.style.fillOpacity = '1';
            },

            // ─── Toggle Controls ───────────────────────────────
            toggleKelurahan() {
                this.showKelurahan = !this.showKelurahan;
                if (this.kelurahanLayer) {
                    this.showKelurahan ? this.map.addLayer(this.kelurahanLayer) : this.map.removeLayer(this.kelurahanLayer);
                }
            },

            toggleLabels() {
                this.showLabels = !this.showLabels;
                if (this.labelLayer) {
                    this.showLabels ? this.map.addLayer(this.labelLayer) : this.map.removeLayer(this.labelLayer);
                }
            },

            // ─── Reset View ────────────────────────────────────
            resetView() {
                if (this.highlightedLayer) {
                    this.rwLayer.resetStyle(this.highlightedLayer);
                    this.applyHatchToLayer(this.highlightedLayer);
                    this.highlightedLayer = null;
                }
                this.selectedRw = null;
                this.selectedStats = {};

                if (this.batuaBounds) {
                    this.map.fitBounds(this.batuaBounds, { padding: [15, 15] });
                }
            },

            // ─── Helpers ───────────────────────────────────────
            formatNumber(num) {
                if (!num && num !== 0) return '-';
                return new Intl.NumberFormat('id-ID').format(num);
            },
        }
    }
</script>
