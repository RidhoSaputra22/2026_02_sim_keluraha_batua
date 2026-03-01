<?php

namespace App\Http\Controllers;

use App\Models\Keluarga;
use App\Models\Kelurahan;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\Umkm;
use Illuminate\Http\JsonResponse;
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
     * API: Ambil data GeoJSON batas kelurahan dari database (PostGIS).
     */
    public function geojsonKelurahan(): JsonResponse
    {
        $kelurahan = DB::selectOne(
            'SELECT id, nama, ST_AsGeoJSON(polygon) as geojson FROM kelurahans WHERE polygon IS NOT NULL LIMIT 1'
        );

        if (! $kelurahan || ! $kelurahan->geojson) {
            return response()->json(['error' => 'Data polygon kelurahan belum tersedia'], 404);
        }

        $geojson = [
            'type' => 'FeatureCollection',
            'name' => 'lurah',
            'crs' => [
                'type' => 'name',
                'properties' => ['name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'],
            ],
            'features' => [
                [
                    'type' => 'Feature',
                    'properties' => ['id' => $kelurahan->id],
                    'geometry' => json_decode($kelurahan->geojson, true),
                ],
            ],
        ];

        return response()->json($geojson);
    }

    /**
     * API: Ambil data GeoJSON wilayah RW beserta statistik dari database (PostGIS).
     */
    public function geojsonRw(): JsonResponse
    {
        $rwRows = DB::select(
            'SELECT id, nomor, ST_AsGeoJSON(polygon) as geojson FROM rws WHERE polygon IS NOT NULL ORDER BY nomor'
        );

        if (empty($rwRows)) {
            return response()->json(['error' => 'Data polygon RW belum tersedia'], 404);
        }

        // Get stats per RW
        $rwStats = $this->getRwStats();

        $features = [];

        foreach ($rwRows as $index => $rw) {
            $rwLabel = 'RW '.str_pad($rw->nomor, 2, '0', STR_PAD_LEFT);
            $stats = $rwStats[$rwLabel] ?? [];

            $properties = array_merge(
                [
                    'id' => $index + 1,
                    'RW' => $rwLabel,
                ],
                $stats
            );

            $features[] = [
                'type' => 'Feature',
                'properties' => $properties,
                'geometry' => json_decode($rw->geojson, true),
            ];
        }

        // Add empty trailing feature to match original format
        $features[] = [
            'type' => 'Feature',
            'properties' => ['id' => null, 'RW' => null],
            'geometry' => ['type' => 'MultiPolygon', 'coordinates' => []],
        ];

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
}
