<?php

namespace App\Http\Controllers\Usaha;

use App\Http\Controllers\Controller;
use App\Models\JenisUsaha;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\Umkm;
use Illuminate\Http\Request;

class LaporanUsahaController extends Controller
{
    public function index(Request $request)
    {
        // Per jenis usaha
        $perJenis = JenisUsaha::withCount('umkms')->orderBy('nama')->get();

        // Per sektor
        $perSektor = Umkm::selectRaw('sektor_umkm, COUNT(*) as total')
            ->whereNotNull('sektor_umkm')
            ->groupBy('sektor_umkm')
            ->orderBy('total', 'desc')
            ->get();

        // Summary stats
        $totalUsaha = Umkm::count();
        $totalAktif = Umkm::where('status', 'aktif')->count();
        $totalTidakAktif = Umkm::where('status', 'tidak_aktif')->count();
        $totalJenis = JenisUsaha::count();

        // Usaha terbaru
        $usahaTerbaru = Umkm::with(['jenisUsaha', 'rt.rw'])
            ->latest()
            ->take(10)
            ->get();

        return view('laporan.usaha', compact(
            'perJenis', 'perSektor', 'totalUsaha', 'totalAktif',
            'totalTidakAktif', 'totalJenis', 'usahaTerbaru'
        ));
    }
}
