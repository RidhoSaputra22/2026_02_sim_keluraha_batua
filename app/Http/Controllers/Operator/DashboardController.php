<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use App\Models\Penduduk;
use App\Models\Surat;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->toDateString();

        $data = [
            'suratHariIni' => Surat::whereDate('tanggal_surat', $today)->count(),
            'suratDraft' => Surat::where('status_esign', 'draft')->count(),
            'suratMenungguCetak' => Surat::where('status_esign', 'siap_cetak')->count(),
            'suratSelesaiHariIni' => Surat::where('status_esign', 'signed')
                ->whereDate('updated_at', $today)->count(),

            'totalPenduduk' => Penduduk::count(),
            'totalKK' => Keluarga::count(),
            'pendudukBaruBulanIni' => Penduduk::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
        ];

        return view('operator.dashboard', $data);
    }
}
