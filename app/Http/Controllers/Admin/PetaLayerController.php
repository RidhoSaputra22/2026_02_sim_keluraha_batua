<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PetaLayer;
use App\Models\PetaLayerPolygon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PetaLayerController extends Controller
{
    /**
     * Daftar semua layer peta — tampilan QGIS-style (map + sidebar).
     */
    public function index()
    {

        $layers = PetaLayer::ordered()
            ->withCount('polygons')
            ->where('jenis', 'polygon') // Hanya tampilkan layer polygon di daftar utama

            ->get();

        $patternTypes = PetaLayer::patternTypes();

        // Build per-layer GeoJSON collections for the map
        $layersGeojson = [];
        foreach ($layers as $layer) {

            $polygons = DB::select(
                'SELECT id, nama, deskripsi, warna, ST_AsGeoJSON(polygon) as geojson
                 FROM peta_layer_polygons WHERE peta_layer_id = ? AND polygon IS NOT NULL ORDER BY sort_order, id',
                [$layer->id]
            );

            $features = [];
            foreach ($polygons as $p) {
                if ($p->geojson) {
                    $features[] = [
                        'type' => 'Feature',
                        'properties' => [
                            'id' => $p->id,
                            'nama' => $p->nama,
                            'deskripsi' => $p->deskripsi,
                            'warna' => $p->warna,
                        ],
                        'geometry' => json_decode($p->geojson, true),
                    ];
                }
            }

            $layersGeojson[$layer->id] = [
                'type' => 'FeatureCollection',
                'features' => $features,
            ];
        }

        return view('peta.layers.index', compact('layers', 'patternTypes', 'layersGeojson'));
    }

    /**
     * Form buat layer baru.
     */
    public function create()
    {
        $patternTypes = PetaLayer::patternTypes();

        return view('peta.layers.create', compact('patternTypes'));
    }

    /**
     * Simpan layer baru.
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
            'warna' => ['required', 'string', 'max:7', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'fill_opacity' => ['required', 'numeric', 'min:0', 'max:1'],
            'stroke_width' => ['required', 'numeric', 'min:0.5', 'max:10'],
            'pattern_type' => ['required', Rule::in(array_keys(PetaLayer::patternTypes()))],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['slug'] = Str::slug($validated['nama']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['jenis'] = PetaLayer::JENIS_POLYGON; // Default jenis polygon untuk layer baru

        PetaLayer::create($validated);

        return redirect()->route('admin.peta-layer.index')
            ->with('success', 'Layer peta berhasil dibuat.');
    }

    /**
     * Halaman editor polygon untuk layer (gambar polygon di map).
     */
    public function edit(PetaLayer $petaLayer)
    {
        $patternTypes = PetaLayer::patternTypes();

        // Get existing polygons as GeoJSON FeatureCollection
        $polygons = DB::select(
            'SELECT id, nama, deskripsi, warna, properties, ST_AsGeoJSON(polygon) as geojson
             FROM peta_layer_polygons WHERE peta_layer_id = ? ORDER BY sort_order, id',
            [$petaLayer->id]
        );

        $features = [];
        foreach ($polygons as $p) {
            if ($p->geojson) {
                $features[] = [
                    'type' => 'Feature',
                    'properties' => [
                        'id' => $p->id,
                        'nama' => $p->nama,
                        'deskripsi' => $p->deskripsi,
                        'warna' => $p->warna,
                    ],
                    'geometry' => json_decode($p->geojson, true),
                ];
            }
        }

        $geojsonCollection = json_encode([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);

        return view('peta.layers.edit', compact('petaLayer', 'patternTypes', 'geojsonCollection'));
    }

    /**
     * Update setting layer (non-polygon).
     */
    public function update(Request $request, PetaLayer $petaLayer)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
            'warna' => ['required', 'string', 'max:7', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'fill_opacity' => ['required', 'numeric', 'min:0', 'max:1'],
            'stroke_width' => ['required', 'numeric', 'min:0.5', 'max:10'],
            'pattern_type' => ['required', Rule::in(array_keys(PetaLayer::patternTypes()))],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['slug'] = Str::slug($validated['nama']);
        $validated['is_active'] = $request->boolean('is_active', true);

        $petaLayer->update($validated);

        return redirect()->route('admin.peta-layer.index')
            ->with('success', 'Layer peta berhasil diperbarui.');
    }

    /**
     * Hapus layer beserta semua polygonnya.
     */
    public function destroy(PetaLayer $petaLayer)
    {
        $petaLayer->delete();

        return redirect()->route('admin.peta-layer.index')
            ->with('success', 'Layer peta berhasil dihapus.');
    }

    /**
     * Toggle aktif/nonaktif layer.
     */
    public function toggleActive(PetaLayer $petaLayer)
    {
        $petaLayer->update(['is_active' => ! $petaLayer->is_active]);

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'is_active' => $petaLayer->is_active]);
        }

        return back()->with('success', 'Status layer berhasil diubah.');
    }

    /**
     * API: Reorder layers.
     */
    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:peta_layers,id'],
        ]);

        foreach ($request->input('order') as $index => $id) {
            PetaLayer::where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true, 'message' => 'Urutan layer berhasil diperbarui.']);
    }

    /**
     * API: Update layer settings (JSON response for inline editing).
     */
    public function updateJson(Request $request, PetaLayer $petaLayer): JsonResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
            'warna' => ['required', 'string', 'max:7', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'fill_opacity' => ['required', 'numeric', 'min:0', 'max:1'],
            'stroke_width' => ['required', 'numeric', 'min:0.5', 'max:10'],
            'pattern_type' => ['required', Rule::in(array_keys(PetaLayer::patternTypes()))],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['slug'] = Str::slug($validated['nama']);
        $validated['is_active'] = $request->boolean('is_active', true);

        $petaLayer->update($validated);

        return response()->json([
            'success' => true,
            'layer' => $petaLayer->fresh(),
            'message' => 'Layer berhasil diperbarui.',
        ]);
    }

    /**
     * API: Store layer (JSON response).
     */
    public function storeJson(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
            'warna' => ['required', 'string', 'max:7', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'fill_opacity' => ['required', 'numeric', 'min:0', 'max:1'],
            'stroke_width' => ['required', 'numeric', 'min:0.5', 'max:10'],
            'pattern_type' => ['required', Rule::in(array_keys(PetaLayer::patternTypes()))],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['slug'] = Str::slug($validated['nama']);
        $validated['is_active'] = $request->boolean('is_active', true);

        $layer = PetaLayer::create($validated);

        return response()->json([
            'success' => true,
            'layer' => $layer,
            'message' => 'Layer berhasil dibuat.',
        ]);
    }

    /**
     * API: Delete layer (JSON response).
     */
    public function destroyJson(PetaLayer $petaLayer): JsonResponse
    {
        $nama = $petaLayer->nama;
        $petaLayer->delete();

        return response()->json([
            'success' => true,
            'message' => "Layer '{$nama}' berhasil dihapus.",
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    //  API: Polygon CRUD (JSON responses for AJAX from map editor)
    // ═══════════════════════════════════════════════════════════

    /**
     * API: Simpan polygon baru dalam layer.
     */
    public function storePolygon(Request $request, PetaLayer $petaLayer): JsonResponse
    {
        $request->validate([
            'nama' => ['nullable', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string'],
            'warna' => ['nullable', 'string', 'max:7', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'geojson' => ['required', 'array'],
            'geojson.type' => ['required', 'string'],
            'geojson.coordinates' => ['required', 'array'],
        ]);

        // Set sort_order to append at end
        $maxSort = PetaLayerPolygon::where('peta_layer_id', $petaLayer->id)->max('sort_order') ?? -1;

        $geojson = $request->input('geojson');
        $jenis = strtolower($geojson['type'] ?? 'polygon');

        // Convert Polygon to MultiPolygon if needed
        if ($jenis === 'polygon') {
            $geojson = [
                'type' => 'MultiPolygon',
                'coordinates' => [$geojson['coordinates']],
            ];
            $jenis = 'multipolygon';
        }

        $polygon = PetaLayerPolygon::create([
            'peta_layer_id' => $petaLayer->id,
            'nama' => $request->input('nama'),
            'deskripsi' => $request->input('deskripsi'),
            'warna' => $request->input('warna', '#6366f1'),
            'sort_order' => $maxSort + 1,
            'jenis' => $jenis,
        ]);

        $polygon->setPolygonFromGeojson($geojson);

        return response()->json([
            'success' => true,
            'id' => $polygon->id,
            'message' => 'Polygon berhasil disimpan.',
        ]);
    }

    /**
     * API: Update polygon yang sudah ada.
     */
    public function updatePolygon(Request $request, PetaLayer $petaLayer, PetaLayerPolygon $polygon): JsonResponse
    {
        $request->validate([
            'nama' => ['nullable', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string'],
            'warna' => ['nullable', 'string', 'max:7', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'geojson' => ['nullable', 'array'],
        ]);

        $updateData = [
            'nama' => $request->input('nama', $polygon->nama),
            'deskripsi' => $request->input('deskripsi', $polygon->deskripsi),
            'warna' => $request->input('warna', $polygon->warna),
        ];

        if ($request->has('geojson')) {
            $geojson = $request->input('geojson');
            $jenis = strtolower($geojson['type'] ?? 'polygon');
            if ($jenis === 'polygon') {
                $geojson = [
                    'type' => 'MultiPolygon',
                    'coordinates' => [$geojson['coordinates']],
                ];
                $jenis = 'multipolygon';
            }
            $updateData['jenis'] = $jenis;
            $polygon->setPolygonFromGeojson($geojson);
        }

        $polygon->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Polygon berhasil diperbarui.',
        ]);
    }

    /**
     * API: Hapus polygon.
     */
    public function destroyPolygon(PetaLayer $petaLayer, PetaLayerPolygon $polygon): JsonResponse
    {
        $polygon->delete();

        return response()->json([
            'success' => true,
            'message' => 'Polygon berhasil dihapus.',
        ]);
    }

    /**
     * API: Reorder polygons within a layer.
     */
    public function reorderPolygons(Request $request, PetaLayer $petaLayer): JsonResponse
    {
        $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer'],
        ]);

        foreach ($request->input('order') as $index => $id) {
            PetaLayerPolygon::where('id', $id)
                ->where('peta_layer_id', $petaLayer->id)
                ->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true, 'message' => 'Urutan polygon berhasil diperbarui.']);
    }

    // ═══════════════════════════════════════════════════════════
    //  API: GeoJSON endpoint untuk peta utama
    // ═══════════════════════════════════════════════════════════

    /**
     * API: Ambil semua layer aktif dengan polygon sebagai GeoJSON (unified endpoint).
     *
     * Returns ALL layers (kelurahan, RW, custom) sorted by sort_order.
     * Each layer includes a `layer_type` field: 'kelurahan', 'rw', or 'custom'.
     * RW layer features include per-RW statistics.
     */
    public function geojsonLayers(): JsonResponse
    {
        $layers = PetaLayer::active()->has('polygons')->ordered()->get();

        // Precompute RW stats for the RW layer
        $rwStats = [];
        if ($layers->contains('slug', PetaLayer::LAYER_WILAYAH_RW)) {
            $rwStats = $this->getRwStats();
        }

        $result = [];

        foreach ($layers as $layer) {
            $layerType = match ($layer->slug) {
                PetaLayer::LAYER_BATAS_KELURAHAN => 'kelurahan',
                PetaLayer::LAYER_WILAYAH_RW => 'rw',
                default => 'custom',
            };

            $polygons = DB::select(
                'SELECT id, nama, deskripsi, warna, rw_id, kelurahan_id, ST_AsGeoJSON(polygon) as geojson
                 FROM peta_layer_polygons WHERE peta_layer_id = ? AND polygon IS NOT NULL ORDER BY sort_order, id',
                [$layer->id]
            );

            $features = [];
            foreach ($polygons as $index => $p) {
                if (! $p->geojson) {
                    continue;
                }

                $properties = [
                    'id' => $p->id,
                    'nama' => $p->nama,
                    'deskripsi' => $p->deskripsi,
                    'warna' => $p->warna,
                ];

                // Enrich RW features with stats
                if ($layerType === 'rw') {
                    $properties['RW'] = $p->nama;
                    $properties['polygon_id'] = $p->id;
                    $properties['rw_id'] = $p->rw_id;
                    $stats = $rwStats[$p->nama] ?? [];
                    $properties = array_merge($properties, $stats);
                }

                $features[] = [
                    'type' => 'Feature',
                    'properties' => $properties,
                    'geometry' => json_decode($p->geojson, true),
                ];
            }

            $result[] = [
                'id' => $layer->id,
                'nama' => $layer->nama,
                'slug' => $layer->slug,
                'warna' => $layer->warna,
                'fill_opacity' => $layer->fill_opacity,
                'stroke_width' => $layer->stroke_width,
                'pattern_type' => $layer->pattern_type,
                'sort_order' => $layer->sort_order,
                'layer_type' => $layerType,
                'geojson' => [
                    'type' => 'FeatureCollection',
                    'features' => $features,
                ],
            ];
        }

        return response()->json($result);
    }

    /**
     * Hitung statistik per RW dari database.
     */
    private function getRwStats(): array
    {
       return Cache::remember('rw_stats', 300, function () {

            $rows = DB::table('rws')
                ->leftJoin('rts', 'rts.rw_id', '=', 'rws.id')
                ->leftJoin('penduduks', 'penduduks.rt_id', '=', 'rts.id')
                ->leftJoin('keluargas', 'keluargas.rt_id', '=', 'rts.id')
                ->leftJoin('umkms', 'umkms.rt_id', '=', 'rts.id')

                ->selectRaw("
                    rws.id,
                    rws.nomor,
                    rws.foto,
                    rws.luas_area,
                    rws.no_telp,
                    rws.alamat_sekretariat,
                    rws.deskripsi,

                    COUNT(DISTINCT rts.id) as total_rt,
                    COUNT(DISTINCT penduduks.id) as total_penduduk,
                    COUNT(DISTINCT keluargas.id) as total_kk,
                    COUNT(DISTINCT umkms.id) as total_umkm,

                    SUM(CASE WHEN penduduks.jenis_kelamin = 'L' THEN 1 ELSE 0 END) as laki_laki,
                    SUM(CASE WHEN penduduks.jenis_kelamin = 'P' THEN 1 ELSE 0 END) as perempuan
                ")

                ->groupBy(
                    'rws.id',
                    'rws.nomor',
                    'rws.foto',
                    'rws.luas_area',
                    'rws.no_telp',
                    'rws.alamat_sekretariat',
                    'rws.deskripsi'
                )

                ->get();

            $stats = [];

            foreach ($rows as $rw) {

                $rwLabel = 'RW '.str_pad($rw->nomor, 2, '0', STR_PAD_LEFT);

                $stats[$rwLabel] = [
                    'total_penduduk' => (int) $rw->total_penduduk,
                    'total_kk' => (int) $rw->total_kk,
                    'total_rt' => (int) $rw->total_rt,
                    'total_umkm' => (int) $rw->total_umkm,
                    'laki_laki' => (int) $rw->laki_laki,
                    'perempuan' => (int) $rw->perempuan,

                    'profil_rw' => [
                        'foto' => $rw->foto,
                        'luas_area' => $rw->luas_area,
                        'no_telp' => $rw->no_telp,
                        'alamat_sekretariat' => $rw->alamat_sekretariat,
                        'deskripsi' => $rw->deskripsi,
                        'ketua' => $rw->ketua->nama ?? '-',
                    ]
                ];
            }

            return $stats;
        });
    }
}
