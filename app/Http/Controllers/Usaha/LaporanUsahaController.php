<?php

namespace App\Http\Controllers\Usaha;

use App\Http\Controllers\Concerns\HasWilayahScope;
use App\Http\Controllers\Controller;
use App\Models\JenisUsaha;
use App\Models\Umkm;
use Illuminate\Http\Request;

class LaporanUsahaController extends Controller
{
    use HasWilayahScope;

    public function index(Request $request)
    {
        $baseQuery = Umkm::query();
        if ($this->isRtRw()) {
            $baseQuery->whereIn('rt_id', $this->wilayahRtIds());
        }

        // Per jenis usaha
        $perJenis = JenisUsaha::withCount(['umkms' => function ($q) {
            if ($this->isRtRw()) {
                $q->whereIn('rt_id', $this->wilayahRtIds());
            }
        }])->orderBy('nama')->get();

        // Per sektor
        $perSektor = (clone $baseQuery)->selectRaw('sektor_umkm, COUNT(*) as total')
            ->whereNotNull('sektor_umkm')
            ->groupBy('sektor_umkm')
            ->orderBy('total', 'desc')
            ->get();

        // Summary stats
        $totalUsaha      = (clone $baseQuery)->count();
        $totalAktif      = (clone $baseQuery)->where('status', 'aktif')->count();
        $totalTidakAktif = (clone $baseQuery)->where('status', 'tidak_aktif')->count();
        $totalJenis      = JenisUsaha::count();

        // Usaha terbaru
        $usahaTerbaru = (clone $baseQuery)->with(['jenisUsaha', 'rt.rw'])
            ->latest()
            ->take(10)
            ->get();

        return view('laporan.usaha', compact(
            'perJenis', 'perSektor', 'totalUsaha', 'totalAktif',
            'totalTidakAktif', 'totalJenis', 'usahaTerbaru'
        ));
    }
}
