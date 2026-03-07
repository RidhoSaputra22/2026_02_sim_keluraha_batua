{{-- Leaflet JS (CDN) + SimPeta engine (Vite bundle) --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@vite('resources/js/map/index.js')

@php
// Pass route URLs to JS — keeps Blade directives out of JS function bodies.
$petaRoutes = [
'geojsonLayers' => route('peta.geojson.layers'),
'stats' => route('peta.stats'),
];
@endphp

<script>
// ── Server-provided routes ───────────────────────────────
window.PETA_ROUTES = @json($petaRoutes);
</script>
@vite('resources/js/peta/app.js')