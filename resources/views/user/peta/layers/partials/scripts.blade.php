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
window.LAYER_ROUTES = @json($layerRoutes);
window.INITIAL_LAYERS = @json($initialLayer);
window.LAYERS_GEOJSON = @json($layersGeojson);
</script>
@vite('resources/js/peta/layer-manager.js')
