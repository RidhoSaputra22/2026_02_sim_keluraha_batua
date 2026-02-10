<?php

namespace App\Http\Controllers\Penandatangan;

use App\Http\Controllers\Controller;
use App\Models\DaftarSuratKeluar;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->toDateString();

        $data = [
            'suratMenungguTtd'           => DaftarSuratKeluar::where('status_esign', 'menunggu_ttd')->count(),
            'suratDitandatanganiHariIni' => DaftarSuratKeluar::where('status_esign', 'selesai')
                                                ->whereDate('updated_at', $today)->count(),
            'totalTtdBulanIni'           => DaftarSuratKeluar::where('status_esign', 'selesai')
                                                ->whereMonth('updated_at', now()->month)
                                                ->whereYear('updated_at', now()->year)->count(),
            'suratSelesaiBulanIni'       => DaftarSuratKeluar::where('status_esign', 'selesai')
                                                ->whereMonth('updated_at', now()->month)
                                                ->whereYear('updated_at', now()->year)->count(),
        ];

        return view('penandatangan.dashboard', $data);
    }
}
