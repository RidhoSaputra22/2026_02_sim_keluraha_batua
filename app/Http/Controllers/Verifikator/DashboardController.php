<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\DaftarSuratKeluar;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $data = [
            'suratMenungguVerifikasi' => DaftarSuratKeluar::where('status_esign', 'menunggu_verifikasi')->count(),
            'suratDisetujuiHariIni'   => DaftarSuratKeluar::where('status_esign', 'diverifikasi')
                                            ->whereDate('updated_at', now()->toDateString())->count(),
            'suratDitolakHariIni'     => DaftarSuratKeluar::where('status_esign', 'ditolak')
                                            ->whereDate('updated_at', now()->toDateString())->count(),
            'suratPerluPerbaikan'     => DaftarSuratKeluar::where('status_esign', 'perlu_perbaikan')->count(),
        ];

        return view('verifikator.dashboard', $data);
    }
}
