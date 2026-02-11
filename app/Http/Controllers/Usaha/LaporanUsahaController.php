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

        // Per RW
        $perRw = Rw::withCount(['rts as umkm_count' => function ($q) {
            $q->withCount('umkms');
        }])->orderBy('nomor')->get();

        // Summary stats
        $totalUsaha = Umkm::count();
        $totalAktif = Umkm::where('status', 'aktif')->count();
        $totalTidakAktif = Umkm::where('status', 'tidak_aktif')->count();
        $totalJenis = JenisUsaha::count();

        // Recent usaha
        $recentUsaha = Umkm::with(['jenisUsaha', 'rt.rw'])
            ->latest()
            ->take(10)
            ->get();

        return view('usaha.laporan', compact(
            'perJenis', 'perRw', 'totalUsaha', 'totalAktif',
            'totalTidakAktif', 'totalJenis', 'recentUsaha'
        ));
    }
}
