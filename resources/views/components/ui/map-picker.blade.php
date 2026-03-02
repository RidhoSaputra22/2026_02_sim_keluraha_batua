{{--
    Reusable Map Picker Component

    Menampilkan peta interaktif untuk memilih lokasi (latitude/longitude).
    User bisa klik peta atau drag marker, dan koordinat otomatis terisi.
    Mendukung pencarian alamat dan tombol "Lokasi Saya".

    @param string $latitude - Nama field latitude (default: 'latitude')
    @param string $longitude - Nama field longitude (default: 'longitude')
    @param float|null $latitudeValue - Nilai latitude saat ini
    @param float|null $longitudeValue - Nilai longitude saat ini
    @param string $label - Label section (default: 'Pilih Lokasi di Peta')
    @param string $height - Tinggi peta (default: '400px')
    @param array $center - Koordinat pusat default [lat, lng]
    @param int $zoom - Zoom level (default: 15)

    Usage:
    <x-ui.map-picker />

    <x-ui.map-picker
        latitude="latitude"
        longitude="longitude"
        :latitudeValue="old('latitude', $model->latitude)"
        :longitudeValue="old('longitude', $model->longitude)"
        label="Lokasi Sekolah"
    />
--}}

@props([
    'latitude' => 'latitude',
    'longitude' => 'longitude',
    'latitudeValue' => null,
    'longitudeValue' => null,
    'label' => 'Pilih Lokasi di Peta',
    'height' => '400px',
    'center' => [-5.1476, 119.4934],
    'zoom' => 15,
])

@php
    $mapId = 'map-picker-' . uniqid();
    $initLat = old($latitude, $latitudeValue) ?: $center[0];
    $initLng = old($longitude, $longitudeValue) ?: $center[1];
    $hasInitial = old($latitude, $latitudeValue) && old($longitude, $longitudeValue);
@endphp

@once
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <style>
            .map-picker-container {
                position: relative;
            }

            .map-picker-container .leaflet-container {
                z-index: 1;
                border-radius: 0.5rem;
            }

            .map-picker-search {
                position: absolute;
                top: 10px;
                left: 50px;
                right: 50px;
                z-index: 1000;
            }

            .map-picker-search input {
                width: 100%;
                padding: 8px 12px;
                border: 2px solid rgba(0, 0, 0, .2);
                border-radius: 6px;
                font-size: 14px;
                background: white;
                box-shadow: 0 2px 6px rgba(0, 0, 0, .15);
            }

            .map-picker-search input:focus {
                outline: none;
                border-color: #3b82f6;
            }

            .map-picker-search .search-results {
                background: white;
                border-radius: 6px;
                margin-top: 4px;
                max-height: 200px;
                overflow-y: auto;
                box-shadow: 0 4px 12px rgba(0, 0, 0, .15);
            }

            .map-picker-search .search-results div {
                padding: 8px 12px;
                cursor: pointer;
                font-size: 13px;
                border-bottom: 1px solid #f0f0f0;
            }

            .map-picker-search .search-results div:hover {
                background: #f0f9ff;
            }

            .map-picker-locate {
                position: absolute;
                bottom: 24px;
                right: 10px;
                z-index: 1000;
                background: white;
                border: 2px solid rgba(0, 0, 0, .2);
                border-radius: 6px;
                width: 36px;
                height: 36px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                box-shadow: 0 2px 6px rgba(0, 0, 0, .15);
            }

            .map-picker-locate:hover {
                background: #f0f9ff;
            }

            .map-picker-rw-label {
                background: none !important;
                border: none !important;
                box-shadow: none !important;
                font-size: 11px;
                font-weight: 700;
                color: #1e293b;
                text-shadow: 1px 1px 2px #fff, -1px -1px 2px #fff, 1px -1px 2px #fff, -1px 1px 2px #fff;
                white-space: nowrap;
                pointer-events: none;
            }

            .map-picker-rw-toggle {
                position: absolute;
                bottom: 24px;
                left: 10px;
                z-index: 1000;
                background: white;
                border: 2px solid rgba(0, 0, 0, .2);
                border-radius: 6px;
                padding: 4px 8px;
                cursor: pointer;
                font-size: 12px;
                font-weight: 600;
                box-shadow: 0 2px 6px rgba(0, 0, 0, .15);
                display: flex;
                align-items: center;
                gap: 4px;
                user-select: none;
            }

            .map-picker-rw-toggle:hover {
                background: #f0f9ff;
            }

            .map-picker-rw-toggle .dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background: #6366f1;
            }
        </style>
    @endpush
@endonce

<div x-data="mapPicker_{{ str_replace('-', '_', $mapId) }}()" class="form-control w-full">
    {{-- Label --}}
    <h3 class="text-lg font-semibold mb-4 border-b pb-2">
        <span class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            {{ $label }}
        </span>
    </h3>

    {{-- Info text --}}
    <p class="text-sm text-base-content/60 mb-3">
        Klik pada peta untuk memilih lokasi, atau gunakan pencarian alamat. Marker bisa di-drag untuk menyesuaikan
        posisi.
    </p>

    {{-- Map container --}}
    <div class="map-picker-container mb-4" style="height: {{ $height }};">




        {{-- RW layer toggle --}}
        <div class="map-picker-rw-toggle" @click="toggleRwLayer()"
            :title="showRw ? 'Sembunyikan batas RW' : 'Tampilkan batas RW'">
            <span class="dot" :style="{ background: showRw ? '#6366f1' : '#94a3b8' }"></span>
            <span x-text="showRw ? 'RW On' : 'RW Off'"></span>
        </div>

        {{-- Leaflet map --}}
        <div id="{{ $mapId }}" class="w-full h-full rounded-lg"></div>
    </div>

    {{-- Coordinate inputs --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="form-control w-full">
            <label class="label"><span class="label-text">Latitude</span></label>
            <input type="text" name="{{ $latitude }}" x-model="lat" readonly
                class="input input-bordered w-full input-sm bg-base-200 cursor-not-allowed @error($latitude) input-error @enderror"
                placeholder="Klik peta untuk mengisi">
            @error($latitude)
                <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
            @enderror
        </div>
        <div class="form-control w-full">
            <label class="label"><span class="label-text">Longitude</span></label>
            <input type="text" name="{{ $longitude }}" x-model="lng" readonly
                class="input input-bordered w-full input-sm bg-base-200 cursor-not-allowed @error($longitude) input-error @enderror"
                placeholder="Klik peta untuk mengisi">
            @error($longitude)
                <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
            @enderror
        </div>
    </div>

    {{-- Clear button --}}
    <div class="mt-2" x-show="lat && lng" x-cloak>
        <button type="button" class="btn btn-ghost btn-xs text-error gap-1" @click="clearLocation()">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Hapus Lokasi
        </button>
    </div>
</div>

@push('scripts')
    <script>
        function mapPicker_{{ str_replace('-', '_', $mapId) }}() {
            return {
                map: null,
                marker: null,
                lat: '{{ $hasInitial ? $initLat : '' }}',
                lng: '{{ $hasInitial ? $initLng : '' }}',
                searchQuery: '',
                searchResults: [],
                searchTimeout: null,
                rwLayer: null,
                rwLabelLayer: null,
                showRw: true,

                init() {
                    this.$nextTick(() => {
                        // Guard against double initialization
                        if (this.map) return;

                        // Initialize map
                        this.map = L.map('{{ $mapId }}', {
                            center: [{{ $initLat }}, {{ $initLng }}],
                            zoom: {{ $hasInitial ? 17 : $zoom }},
                            zoomControl: true,
                        });

                        // Base layers
                        const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; OpenStreetMap contributors',
                            maxZoom: 19,
                        });

                        const satellite = L.tileLayer(
                            'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                                attribution: '&copy; Esri',
                                maxZoom: 19,
                            });

                        osm.addTo(this.map);
                        L.control.layers({
                            'Peta': osm,
                            'Satelit': satellite
                        }, {}, {
                            position: 'topright'
                        }).addTo(this.map);

                        // Load RW polygon overlay
                        this.loadRwLayer();

                        // Place initial marker if we have coordinates
                        @if ($hasInitial)
                            this.placeMarker({{ $initLat }}, {{ $initLng }});
                        @endif

                        // Map click handler
                        this.map.on('click', (e) => {
                            this.placeMarker(e.latlng.lat, e.latlng.lng);
                        });
                    });
                },

                async loadRwLayer() {
                    try {
                        const url = '{{ route('peta.geojson.rw') }}';
                        const resp = await fetch(url, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin',
                        });
                        if (!resp.ok) return;

                        const geojson = await resp.json();
                        if (!geojson.features || geojson.features.length === 0) return;

                        // RW polygon layer
                        this.rwLayer = L.geoJSON(geojson, {
                            style: (feature) => ({
                                color: feature.properties.warna || '#6366f1',
                                weight: 2,
                                opacity: 0.8,
                                fillColor: feature.properties.warna || '#6366f1',
                                fillOpacity: 0.10,
                                dashArray: '5, 5',
                            }),
                            onEachFeature: (feature, layer) => {
                                const p = feature.properties;

                                layer.on('mouseover', () => {
                                    layer.setStyle({
                                        fillOpacity: 0.25,
                                        weight: 3
                                    });
                                });
                                layer.on('mouseout', () => {
                                    layer.setStyle({
                                        fillOpacity: 0.10,
                                        weight: 2
                                    });
                                });
                            },
                        }).addTo(this.map);

                        // RW labels layer (permanent)
                        this.rwLabelLayer = L.layerGroup().addTo(this.map);
                        geojson.features.forEach(f => {
                            if (!f.geometry) return;
                            const bounds = L.geoJSON(f).getBounds();
                            const center = bounds.getCenter();
                            L.marker(center, {
                                icon: L.divIcon({
                                    className: 'map-picker-rw-label',
                                    html: f.properties.RW || '',
                                    iconSize: [50, 16],
                                    iconAnchor: [25, 8],
                                }),
                                interactive: false,
                            }).addTo(this.rwLabelLayer);
                        });

                        // Fit map to RW bounds if no initial marker
                        @if (!$hasInitial)
                            this.map.fitBounds(this.rwLayer.getBounds(), {
                                padding: [20, 20]
                            });
                        @endif
                    } catch (e) {
                        console.warn('Map picker: gagal memuat batas RW', e);
                    }
                },

                toggleRwLayer() {
                    this.showRw = !this.showRw;
                    if (this.rwLayer) {
                        this.showRw ? this.rwLayer.addTo(this.map) : this.map.removeLayer(this.rwLayer);
                    }
                    if (this.rwLabelLayer) {
                        this.showRw ? this.rwLabelLayer.addTo(this.map) : this.map.removeLayer(this.rwLabelLayer);
                    }
                },

                placeMarker(lat, lng) {
                    this.lat = parseFloat(lat).toFixed(7);
                    this.lng = parseFloat(lng).toFixed(7);

                    if (this.marker) {
                        this.marker.setLatLng([lat, lng]);
                    } else {
                        this.marker = L.marker([lat, lng], {
                            draggable: true,
                            autoPan: true,
                        }).addTo(this.map);

                        // Drag handler
                        this.marker.on('dragend', (e) => {
                            const pos = e.target.getLatLng();
                            this.lat = parseFloat(pos.lat).toFixed(7);
                            this.lng = parseFloat(pos.lng).toFixed(7);
                        });
                    }

                    this.marker.bindPopup(
                        `<div class="text-sm"><strong>Lokasi Terpilih</strong><br>Lat: ${this.lat}<br>Lng: ${this.lng}</div>`
                    ).openPopup();
                },

                clearLocation() {
                    this.lat = '';
                    this.lng = '';
                    if (this.marker) {
                        this.map.removeLayer(this.marker);
                        this.marker = null;
                    }
                },


            };
        }
    </script>
@endpush
