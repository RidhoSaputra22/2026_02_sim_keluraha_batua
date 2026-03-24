<?php

namespace App\Http\Controllers;

use App\Models\Keluarga;
use App\Models\PetaLayer;
use App\Models\PetaLayerPolygon;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\Umkm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PetaController extends Controller
{
    /**
     * Tampilkan halaman peta kelurahan interaktif.
     */
    public function index()
    {
        return view('peta.index');
    }

    /**
     * API: Ambil data GeoJSON batas kelurahan dari peta_layer_polygons.
     */
    public function geojsonKelurahan(): JsonResponse
    {
        $kelLayer = PetaLayer::where('slug', PetaLayer::LAYER_BATAS_KELURAHAN)->first();
        $geojsonSelect = PetaLayerPolygon::geojsonSelectExpression('plp.polygon');

        if (! $kelLayer) {
            return response()->json(['error' => 'Layer batas kelurahan belum tersedia'], 404);
        }

        $rows = DB::select(
            "SELECT plp.id, plp.nama, plp.kelurahan_id, {$geojsonSelect}
             FROM peta_layer_polygons plp
             WHERE plp.peta_layer_id = ? AND plp.polygon IS NOT NULL
             ORDER BY plp.id",
            [$kelLayer->id]
        );

        if (empty($rows)) {
            return response()->json(['error' => 'Data polygon kelurahan belum tersedia'], 404);
        }

        $features = [];
        foreach ($rows as $row) {
            $features[] = [
                'type' => 'Feature',
                'properties' => [
                    'id' => $row->kelurahan_id ?? $row->id,
                    'nama' => $row->nama,
                ],
                'geometry' => json_decode($row->geojson, true),
            ];
        }

        $geojson = [
            'type' => 'FeatureCollection',
            'name' => 'lurah',
            'crs' => [
                'type' => 'name',
                'properties' => ['name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'],
            ],
            'features' => $features,
        ];

        return response()->json($geojson);
    }

    /**
     * API: Ambil data GeoJSON wilayah RW dari peta_layer_polygons (layer "wilayah-rw").
     */
    public function geojsonRw(): JsonResponse
    {
        $rwLayer = PetaLayer::where('slug', PetaLayer::LAYER_WILAYAH_RW)->first();
        $geojsonSelect = PetaLayerPolygon::geojsonSelectExpression('plp.polygon');

        if (! $rwLayer) {
            return response()->json(['error' => 'Layer RW belum tersedia'], 404);
        }

        $rows = DB::select(
            "SELECT plp.id, plp.nama, plp.warna, plp.rw_id, {$geojsonSelect}
             FROM peta_layer_polygons plp
             WHERE plp.peta_layer_id = ? AND plp.polygon IS NOT NULL
             ORDER BY plp.nama",
            [$rwLayer->id]
        );

        if (empty($rows)) {
            return response()->json(['error' => 'Data polygon RW belum tersedia'], 404);
        }

        // Get stats per RW
        $rwStats = $this->getRwStats();

        $features = [];

        foreach ($rows as $index => $row) {
            $rwLabel = $row->nama; // e.g. "RW 05"
            $stats = $rwStats[$rwLabel] ?? [];

            $properties = array_merge(
                [
                    'id' => $index + 1,
                    'RW' => $rwLabel,
                    'warna' => $row->warna ?? '#6b7280',
                    'polygon_id' => $row->id,
                    'rw_id' => $row->rw_id,
                ],
                $stats
            );

            $features[] = [
                'type' => 'Feature',
                'properties' => $properties,
                'geometry' => json_decode($row->geojson, true),
            ];
        }

        $geojson = [
            'type' => 'FeatureCollection',
            'name' => 'batua1',
            'crs' => [
                'type' => 'name',
                'properties' => ['name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'],
            ],
            'features' => $features,
        ];

        return response()->json($geojson);
    }

    /**
     * API: Ambil statistik ringkasan seluruh kelurahan.
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total_penduduk' => Penduduk::count(),
            'total_kk' => Keluarga::count(),
            'total_rw' => Rw::count(),
            'total_rt' => Rt::count(),
            'total_umkm' => Umkm::count(),
            'laki_laki' => Penduduk::where('jenis_kelamin', 'L')->count(),
            'perempuan' => Penduduk::where('jenis_kelamin', 'P')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Hitung statistik per RW dari database.
     */
    private function getRwStats(): array
    {
        $rwList = Rw::with(['rts'])->get();
        $stats = [];

        foreach ($rwList as $rw) {
            $rwLabel = 'RW '.str_pad($rw->nomor, 2, '0', STR_PAD_LEFT);
            $rtIds = $rw->rts->pluck('id')->toArray();

            $totalPenduduk = Penduduk::whereIn('rt_id', $rtIds)->count();
            $totalKK = Keluarga::whereIn('rt_id', $rtIds)->count();
            $totalUmkm = Umkm::whereIn('rt_id', $rtIds)->count();
            $lakiLaki = Penduduk::whereIn('rt_id', $rtIds)->where('jenis_kelamin', 'L')->count();
            $perempuan = Penduduk::whereIn('rt_id', $rtIds)->where('jenis_kelamin', 'P')->count();

            $stats[$rwLabel] = [
                'total_penduduk' => $totalPenduduk,
                'total_kk' => $totalKK,
                'total_rt' => count($rtIds),
                'total_umkm' => $totalUmkm,
                'laki_laki' => $lakiLaki,
                'perempuan' => $perempuan,
            ];
        }

        return $stats;
    }

    // ═══════════════════════════════════════════════════════════
    //  RW Polygon Management API (via peta_layer_polygons)
    // ═══════════════════════════════════════════════════════════

    /**
     * Halaman kelola polygon RW.
     */
    public function editRwPolygon(Rw $rw)
    {
        $rwList = Rw::orderBy('nomor')->get();
        $rwLayer = PetaLayer::where('slug', PetaLayer::LAYER_WILAYAH_RW)->first();
        $geojsonSelect = PetaLayerPolygon::geojsonSelectExpression('polygon');
        $layerGeojsonSelect = PetaLayerPolygon::geojsonSelectExpression('plp.polygon');

        // Get current polygon from peta_layer_polygons
        $polygonGeojson = null;
        $currentPolygonRecord = null;
        if ($rwLayer) {
            $currentPolygonRecord = DB::selectOne(
                "SELECT id, warna, {$geojsonSelect} FROM peta_layer_polygons WHERE peta_layer_id = ? AND rw_id = ? AND polygon IS NOT NULL",
                [$rwLayer->id, $rw->id]
            );
            if ($currentPolygonRecord && $currentPolygonRecord->geojson) {
                $polygonGeojson = $currentPolygonRecord->geojson;
            }
        }

        // Get all RW polygons for reference overlay
        $allRwPolygons = [];
        if ($rwLayer) {
            $allRwPolygons = DB::select(
                "SELECT plp.id, plp.nama, plp.warna, plp.rw_id, {$layerGeojsonSelect}
                 FROM peta_layer_polygons plp
                 WHERE plp.peta_layer_id = ? AND plp.polygon IS NOT NULL
                 ORDER BY plp.nama",
                [$rwLayer->id]
            );
        }

        // Get kelurahan boundary from peta_layer_polygons
        $kelurahanGeojson = null;
        $kelLayer = PetaLayer::where('slug', PetaLayer::LAYER_BATAS_KELURAHAN)->first();
        if ($kelLayer) {
            $kel = DB::selectOne(
                "SELECT {$geojsonSelect} FROM peta_layer_polygons WHERE peta_layer_id = ? AND polygon IS NOT NULL LIMIT 1",
                [$kelLayer->id]
            );
            if ($kel && $kel->geojson) {
                $kelurahanGeojson = $kel->geojson;
            }
        }

        $rwWarna = $currentPolygonRecord->warna ?? '#6366f1';

        return view('peta.rw-polygon', compact('rw', 'rwList', 'polygonGeojson', 'allRwPolygons', 'kelurahanGeojson', 'rwWarna'));
    }

    /**
     * API: Simpan/update polygon RW (into peta_layer_polygons).
     */
    public function updateRwPolygon(Request $request, Rw $rw): JsonResponse
    {
        $request->validate([
            'geojson' => ['required', 'array'],
            'geojson.type' => ['required', 'string'],
            'geojson.coordinates' => ['required', 'array'],
            'warna' => ['sometimes', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        $geojson = $request->input('geojson');

        // Convert Polygon → MultiPolygon if needed
        if ($geojson['type'] === 'Polygon') {
            $geojson = [
                'type' => 'MultiPolygon',
                'coordinates' => [$geojson['coordinates']],
            ];
        }

        $rwLayer = PetaLayer::firstOrCreate(
            ['slug' => PetaLayer::LAYER_WILAYAH_RW],
            [
                'nama'         => 'Wilayah RW',
                'deskripsi'    => 'Batas wilayah RW',
                'warna'        => '#6366f1',
                'fill_opacity' => 0.30,
                'stroke_width' => 2.5,
                'pattern_type' => 'solid',
                'is_active'    => true,
                'sort_order'   => 1,
            ]
        );

        $rwNama = 'RW ' . str_pad($rw->nomor, 2, '0', STR_PAD_LEFT);
        $warna = $request->input('warna', '#6366f1');

        // Find or create polygon record
        $polygon = PetaLayerPolygon::firstOrCreate(
            ['peta_layer_id' => $rwLayer->id, 'rw_id' => $rw->id],
            ['nama' => $rwNama, 'warna' => $warna]
        );

        $polygon->update(['nama' => $rwNama, 'warna' => $warna]);
        $polygon->setPolygonFromGeojson($geojson);

        return response()->json([
            'success' => true,
            'message' => 'Polygon ' . $rwNama . ' berhasil disimpan.',
        ]);
    }

    /**
     * API: Hapus polygon RW.
     */
    public function deleteRwPolygon(Rw $rw): JsonResponse
    {
        $rwLayer = PetaLayer::where('slug', PetaLayer::LAYER_WILAYAH_RW)->first();
        $rwNama = 'RW ' . str_pad($rw->nomor, 2, '0', STR_PAD_LEFT);

        if ($rwLayer) {
            $polygon = PetaLayerPolygon::where('peta_layer_id', $rwLayer->id)
                ->where('rw_id', $rw->id)
                ->first();

            if ($polygon) {
                DB::statement('UPDATE peta_layer_polygons SET polygon = NULL WHERE id = ?', [$polygon->id]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Polygon ' . $rwNama . ' berhasil dihapus.',
        ]);
    }

    /**
     * API: Update warna RW saja (tanpa polygon).
     */
    public function updateRwColor(Request $request, Rw $rw): JsonResponse
    {
        $request->validate([
            'warna' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        $rwLayer = PetaLayer::where('slug', PetaLayer::LAYER_WILAYAH_RW)->first();
        $rwNama = 'RW ' . str_pad($rw->nomor, 2, '0', STR_PAD_LEFT);

        if ($rwLayer) {
            PetaLayerPolygon::where('peta_layer_id', $rwLayer->id)
                ->where('rw_id', $rw->id)
                ->update(['warna' => $request->input('warna')]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Warna ' . $rwNama . ' berhasil diperbarui.',
        ]);
    }
}
