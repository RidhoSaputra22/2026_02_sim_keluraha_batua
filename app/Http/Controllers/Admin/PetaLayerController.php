<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PetaLayer;
use App\Models\PetaLayerPolygon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PetaLayerController extends Controller
{
    /**
     * Daftar semua layer peta.
     */
    public function index()
    {
        $layers = PetaLayer::ordered()
            ->withCount('polygons')
            ->get();

        return view('peta.layers.index', compact('layers'));
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
            'SELECT id, nama, deskripsi, properties, ST_AsGeoJSON(polygon) as geojson
             FROM peta_layer_polygons WHERE peta_layer_id = ? ORDER BY id',
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
        $petaLayer->update(['is_active' => !$petaLayer->is_active]);

        return back()->with('success', 'Status layer berhasil diubah.');
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
            'geojson' => ['required', 'array'],
            'geojson.type' => ['required', 'string'],
            'geojson.coordinates' => ['required', 'array'],
        ]);

        $polygon = PetaLayerPolygon::create([
            'peta_layer_id' => $petaLayer->id,
            'nama' => $request->input('nama'),
            'deskripsi' => $request->input('deskripsi'),
        ]);

        // Convert Polygon to MultiPolygon if needed
        $geojson = $request->input('geojson');
        if ($geojson['type'] === 'Polygon') {
            $geojson = [
                'type' => 'MultiPolygon',
                'coordinates' => [$geojson['coordinates']],
            ];
        }

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
            'geojson' => ['nullable', 'array'],
        ]);

        $polygon->update([
            'nama' => $request->input('nama', $polygon->nama),
            'deskripsi' => $request->input('deskripsi', $polygon->deskripsi),
        ]);

        if ($request->has('geojson')) {
            $geojson = $request->input('geojson');
            if ($geojson['type'] === 'Polygon') {
                $geojson = [
                    'type' => 'MultiPolygon',
                    'coordinates' => [$geojson['coordinates']],
                ];
            }
            $polygon->setPolygonFromGeojson($geojson);
        }

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

    // ═══════════════════════════════════════════════════════════
    //  API: GeoJSON endpoint untuk peta utama
    // ═══════════════════════════════════════════════════════════

    /**
     * API: Ambil semua layer aktif dengan polygon sebagai GeoJSON.
     */
    public function geojsonLayers(): JsonResponse
    {
        $layers = PetaLayer::active()->ordered()->get();

        $result = [];

        foreach ($layers as $layer) {
            $polygons = DB::select(
                'SELECT id, nama, deskripsi, ST_AsGeoJSON(polygon) as geojson
                 FROM peta_layer_polygons WHERE peta_layer_id = ? AND polygon IS NOT NULL ORDER BY id',
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
                        ],
                        'geometry' => json_decode($p->geojson, true),
                    ];
                }
            }

            $result[] = [
                'id' => $layer->id,
                'nama' => $layer->nama,
                'slug' => $layer->slug,
                'warna' => $layer->warna,
                'fill_opacity' => $layer->fill_opacity,
                'stroke_width' => $layer->stroke_width,
                'pattern_type' => $layer->pattern_type,
                'geojson' => [
                    'type' => 'FeatureCollection',
                    'features' => $features,
                ],
            ];
        }

        return response()->json($result);
    }
}
