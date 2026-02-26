<?php

namespace App\Http\Controllers;

use App\Models\Keluarga;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\Umkm;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

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
     * API: Ambil data GeoJSON batas kelurahan.
     */
    public function geojsonKelurahan(): JsonResponse
    {
        $disk = Storage::disk('public');
        $path = 'geojson/lurah.geojson';

        if (! $disk->exists($path)) {
            return response()->json(['error' => 'File GeoJSON kelurahan tidak ditemukan'], 404);
        }

        $geojson = json_decode($disk->get($path), true);

        return response()->json($geojson);
    }

    /**
     * API: Ambil data GeoJSON wilayah RW beserta statistik.
     */
    public function geojsonRw(): JsonResponse
    {
        $disk = Storage::disk('public');
        $path = 'geojson/batua1.geojson';

        if (! $disk->exists($path)) {
            return response()->json(['error' => 'File GeoJSON RW tidak ditemukan'], 404);
        }

        $geojson = json_decode($disk->get($path), true);

        // Ambil data statistik per RW
        $rwStats = $this->getRwStats();

        // Inject statistik ke properties GeoJSON
        foreach ($geojson['features'] as &$feature) {
            $rwName = $feature['properties']['RW'] ?? null;

            if ($rwName && isset($rwStats[$rwName])) {
                $feature['properties'] = array_merge(
                    $feature['properties'],
                    $rwStats[$rwName]
                );
            }
        }

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
