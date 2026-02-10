<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\DataPenduduk;
use App\Models\DataKeluarga;
use App\Models\DaftarSuratKeluar;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->toDateString();

        $data = [
            'suratHariIni'         => DaftarSuratKeluar::whereDate('tanggal_surat', $today)->count(),
            'suratDraft'           => DaftarSuratKeluar::where('status_esign', 'draft')->count(),
            'suratMenungguCetak'   => DaftarSuratKeluar::where('status_esign', 'siap_cetak')->count(),
            'suratSelesaiHariIni'  => DaftarSuratKeluar::where('status_esign', 'selesai')
                                        ->whereDate('updated_at', $today)->count(),

            'totalPenduduk'         => DataPenduduk::count(),
            'totalKK'               => DataKeluarga::count(),
            'pendudukBaruBulanIni'  => DataPenduduk::whereMonth('tgl_input', now()->month)
                                        ->whereYear('tgl_input', now()->year)->count(),
        ];

        return view('operator.dashboard', $data);
    }
}
