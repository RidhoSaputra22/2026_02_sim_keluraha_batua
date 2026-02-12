<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->toDateString();
        $startOfMonth = now()->startOfMonth();

        // Statistics
        $suratMenungguVerifikasi = Surat::where('status_esign', 'draft')->count();
        $suratDisetujuiHariIni = Surat::where('status_esign', 'proses')
            ->where('verifikator_id', auth()->id())
            ->whereDate('tgl_verifikasi', $today)->count();
        $suratDitolakHariIni = Surat::where('status_esign', 'reject')
            ->where('verifikator_id', auth()->id())
            ->whereDate('tgl_verifikasi', $today)->count();
        $suratPerluPerbaikan = Surat::where('status_esign', 'reject')
            ->whereNotNull('verifikator_id')->count();

        // Performance this month
        $disetujuiBulanIni = Surat::where('verifikator_id', auth()->id())
            ->where('status_esign', 'proses')
            ->where('tgl_verifikasi', '>=', $startOfMonth)->count();
        $ditolakBulanIni = Surat::where('verifikator_id', auth()->id())
            ->where('status_esign', 'reject')
            ->where('tgl_verifikasi', '>=', $startOfMonth)->count();
        $totalVerifikasiBulanIni = $disetujuiBulanIni + $ditolakBulanIni;

        // Antrian: surat berstatus draft, terbaru dulu
        $antrian = Surat::with(['jenis', 'pemohon.penduduk', 'petugasInput'])
            ->where('status_esign', 'draft')
            ->latest('tgl_input')
            ->take(10)
            ->get();

        // Riwayat verifikasi terbaru oleh user ini
        $riwayat = Surat::with(['jenis', 'pemohon'])
            ->where('verifikator_id', auth()->id())
            ->whereNotNull('tgl_verifikasi')
            ->latest('tgl_verifikasi')
            ->take(5)
            ->get();

        return view('verifikator.dashboard', compact(
            'suratMenungguVerifikasi',
            'suratDisetujuiHariIni',
            'suratDitolakHariIni',
            'suratPerluPerbaikan',
            'totalVerifikasiBulanIni',
            'disetujuiBulanIni',
            'ditolakBulanIni',
            'antrian',
            'riwayat',
        ));
    }
}
