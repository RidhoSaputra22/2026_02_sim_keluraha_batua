<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\Surat;
use App\Models\SuratJenis;
use App\Models\Umkm;
use App\Models\PegawaiStaff;
use App\Models\Penandatanganan;
use App\Models\RtRwPengurus;
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

            // Persuratan
            'totalSuratBulanIni' => Surat::whereMonth('tanggal_surat', now()->month)
                ->whereYear('tanggal_surat', now()->year)->count(),
            'suratMenunggu' => Surat::where('status_esign', 'draft')->count(),

            // Mutasi bulan ini (placeholder — no mutasi table yet)
            'mutasiLahir' => 0,
            'mutasiMeninggal' => 0,
            'mutasiDatang' => 0,
            'mutasiPindah' => 0,

            // Data usaha
            'totalUsaha' => Umkm::count(),
            'usahaAktif' => Umkm::count(),
            'usahaTidakAktif' => 0,

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

            // Recent surat for dashboard table
            'recentSurat' => Surat::with(['jenis', 'pemohon'])
                ->latest('tanggal_surat')
                ->take(5)
                ->get(),

            // Surat per jenis (this month) — use whereHas for SQLite compatibility
            'suratPerJenis' => SuratJenis::withCount(['surats' => function ($q) {
                $q->whereMonth('tanggal_surat', now()->month)
                  ->whereYear('tanggal_surat', now()->year);
            }])->whereHas('surats', function ($q) {
                $q->whereMonth('tanggal_surat', now()->month)
                  ->whereYear('tanggal_surat', now()->year);
            })->orderByDesc('surats_count')->get(),
        ];

        return view('admin.dashboard', $data);
    }
}
