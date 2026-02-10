<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\DaftarSuratKeluar;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $data = [
            'permohonanAktif'  => DaftarSuratKeluar::where('nama_pemohon', $user->name)
                                    ->whereNotIn('status_esign', ['selesai', 'ditolak'])->count(),
            'permohonanSelesai' => DaftarSuratKeluar::where('nama_pemohon', $user->name)
                                    ->where('status_esign', 'selesai')->count(),
            'permohonanDitolak' => DaftarSuratKeluar::where('nama_pemohon', $user->name)
                                    ->where('status_esign', 'ditolak')->count(),
            'totalPermohonan'   => DaftarSuratKeluar::where('nama_pemohon', $user->name)->count(),
        ];

        return view('warga.dashboard', $data);
    }
}
