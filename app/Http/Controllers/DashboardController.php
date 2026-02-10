<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard.
     */
    public function index()
    {
        // Data dummy - nanti diganti dengan query dari database
        $data = [
            'totalPenduduk'     => 12450,
            'totalKK'           => 3120,
            'totalSuratBulanIni'=> 87,
            'suratMenunggu'     => 5,
            'mutasiLahir'       => 8,
            'mutasiMeninggal'   => 3,
            'mutasiDatang'      => 12,
            'mutasiPindah'      => 6,
            'totalUsaha'        => 156,
            'usahaAktif'        => 142,
            'usahaTidakAktif'   => 14,
        ];

        return view('dashboard', $data);
    }
}
