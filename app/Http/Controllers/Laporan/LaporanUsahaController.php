<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\JenisUsaha;
use App\Models\Rw;
use App\Models\Umkm;
use Illuminate\Http\Request;

class LaporanUsahaController extends Controller
{
    public function index(Request $request)
    {
        $totalUsaha = Umkm::count();
        $totalAktif = Umkm::where('status', 'aktif')->count();
        $totalTidakAktif = Umkm::where('status', 'tidak_aktif')->count();
        $totalJenis = JenisUsaha::count();

        // Per jenis usaha
        $perJenis = JenisUsaha::withCount('umkms')->orderByDesc('umkms_count')->get();

        // Per sektor
        $perSektor = Umkm::selectRaw('sektor_umkm, count(*) as total')
            ->whereNotNull('sektor_umkm')
            ->groupBy('sektor_umkm')
            ->orderByDesc('total')
            ->get();

        // Usaha terbaru
        $usahaTerbaru = Umkm::with(['jenisUsaha', 'rt.rw'])
            ->latest()
            ->take(10)
            ->get();

        return view('laporan.usaha', compact(
            'totalUsaha', 'totalAktif', 'totalTidakAktif', 'totalJenis',
            'perJenis', 'perSektor', 'usahaTerbaru'
        ));
    }
}
