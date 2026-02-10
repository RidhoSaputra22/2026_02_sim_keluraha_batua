<?php

namespace App\Http\Controllers\RtRw;

use App\Http\Controllers\Controller;
use App\Models\DataPenduduk;
use App\Models\DataKeluarga;
use App\Models\DaftarSuratKeluar;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $rt   = $user->wilayah_rt;
        $rw   = $user->wilayah_rw;

        $data = [
            'totalWarga'      => DataPenduduk::where('rt', $rt)->where('rw', $rw)->count(),
            'totalKK'         => DataKeluarga::where('rt', $rt)->where('rw', $rw)->count(),
            'totalPengantarBulanIni' => DaftarSuratKeluar::where('jenis_surat', 'pengantar')
                                          ->whereMonth('tanggal_surat', now()->month)
                                          ->whereYear('tanggal_surat', now()->year)->count(),
            'laporanMasuk'           => 0, // placeholder
            'rt'              => $rt,
            'rw'              => $rw,
        ];

        return view('rt-rw.dashboard', $data);
    }
}
