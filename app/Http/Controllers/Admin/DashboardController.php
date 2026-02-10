<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DaftarSuratKeluar;
use App\Models\DataKeluarga;
use App\Models\DataPenduduk;
use App\Models\DataRtRw;
use App\Models\DataUmkm;
use App\Models\PegawaiStaff;
use App\Models\Penandatanganan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $data = [
            // Kependudukan
            'totalPenduduk' => DataPenduduk::count(),
            'totalKK' => DataKeluarga::count(),

            // Persuratan
            'totalSuratBulanIni' => DaftarSuratKeluar::whereMonth('tanggal_surat', now()->month)
                ->whereYear('tanggal_surat', now()->year)->count(),
            'suratMenunggu' => DaftarSuratKeluar::where('status_esign', 'menunggu')->count(),

            // Mutasi bulan ini
            'mutasiLahir' => 0,
            'mutasiMeninggal' => 0,
            'mutasiDatang' => 0,
            'mutasiPindah' => 0,

            // Data usaha
            'totalUsaha' => DataUmkm::count(),
            'usahaAktif' => DataUmkm::count(),
            'usahaTidakAktif' => 0,

            // Wilayah
            'totalRW' => DataRtRw::where('jabatan', 'RW')->count(),
            'totalRT' => DataRtRw::where('jabatan', 'RT')->count(),

            // Pengguna
            'totalUsers' => User::count(),
            'activeUsers' => User::where('is_active', true)->count(),

            // Pegawai & Penandatangan
            'totalPegawai' => PegawaiStaff::count(),
            'totalPenandatangan' => Penandatanganan::where('status', 'aktif')->count(),

            // Users per role
            'usersPerRole' => Role::withCount('users')->get(),

            // Recent users
            'recentUsers' => User::with('role')->latest()->take(5)->get(),
        ];

        return view('admin.dashboard', $data);
    }
}
