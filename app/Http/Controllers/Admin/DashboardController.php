<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use App\Models\Kelahiran;
use App\Models\Kematian;
use App\Models\MutasiPenduduk;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\Umkm;
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
            'totalPenduduk' => Penduduk::count(),
            'totalKK' => Keluarga::count(),

            // Mutasi bulan ini
            'mutasiLahir' => Kelahiran::whereMonth('tanggal_lahir', now()->month)
                ->whereYear('tanggal_lahir', now()->year)->count(),
            'mutasiMeninggal' => Kematian::whereMonth('tanggal_meninggal', now()->month)
                ->whereYear('tanggal_meninggal', now()->year)->count(),
            'mutasiDatang' => MutasiPenduduk::where('jenis_mutasi', 'datang')
                ->whereMonth('tanggal_mutasi', now()->month)
                ->whereYear('tanggal_mutasi', now()->year)->count(),
            'mutasiPindah' => MutasiPenduduk::where('jenis_mutasi', 'pindah')
                ->whereMonth('tanggal_mutasi', now()->month)
                ->whereYear('tanggal_mutasi', now()->year)->count(),

            // Data usaha
            'totalUsaha' => Umkm::count(),
            'usahaAktif' => Umkm::where('status', 'aktif')->count(),
            'usahaTidakAktif' => Umkm::where('status', 'tidak_aktif')->count(),

            // Wilayah
            'totalRW' => Rw::count(),
            'totalRT' => Rt::count(),

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
