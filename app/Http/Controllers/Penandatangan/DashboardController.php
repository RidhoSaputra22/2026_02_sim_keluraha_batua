<?php

namespace App\Http\Controllers\Penandatangan;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Models\SuratJenis;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->toDateString();
        $startOfMonth = now()->startOfMonth();

        // Statistics
        $suratMenungguTtd = Surat::where('status_esign', 'proses')->count();
        $suratDitandatanganiHariIni = Surat::where('status_esign', 'signed')
            ->whereDate('tgl_ttd', $today)->count();
        $totalTtdBulanIni = Surat::where('status_esign', 'signed')
            ->where('tgl_ttd', '>=', $startOfMonth)->count();
        $suratDitolakPenandatangan = Surat::where('status_esign', 'reject')
            ->whereNotNull('catatan_penandatangan')
            ->where('tgl_ttd', '>=', $startOfMonth)->count();

        // Antrian: surat terverifikasi menunggu TTD
        $antrian = Surat::with(['jenis', 'sifat', 'pemohon.penduduk', 'verifikator'])
            ->where('status_esign', 'proses')
            ->latest('tgl_verifikasi')
            ->take(10)
            ->get();

        // Riwayat tanda tangan terbaru
        $riwayat = Surat::with(['jenis', 'pemohon'])
            ->where('status_esign', 'signed')
            ->whereNotNull('tgl_ttd')
            ->latest('tgl_ttd')
            ->take(5)
            ->get();

        // Rekap per jenis surat bulan ini
        $rekapJenis = Surat::where('status_esign', 'signed')
            ->where('tgl_ttd', '>=', $startOfMonth)
            ->selectRaw('jenis_id, COUNT(*) as total')
            ->groupBy('jenis_id')
            ->with('jenis')
            ->orderByDesc('total')
            ->take(6)
            ->get();

        // Status hari ini summary
        $dikembalikanHariIni = Surat::where('status_esign', 'reject')
            ->whereNotNull('catatan_penandatangan')
            ->whereDate('tgl_ttd', $today)->count();

        return view('penandatangan.dashboard', compact(
            'suratMenungguTtd',
            'suratDitandatanganiHariIni',
            'totalTtdBulanIni',
            'suratDitolakPenandatangan',
            'antrian',
            'riwayat',
            'rekapJenis',
            'dikembalikanHariIni',
        ));
    }
}
