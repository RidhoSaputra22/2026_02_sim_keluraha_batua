<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Route to appropriate dashboard based on user role.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $role = $user->getRoleName();

        return match ($role) {
            Role::ADMIN         => $this->adminDashboard(),
            Role::OPERATOR      => $this->operatorDashboard(),
            Role::VERIFIKATOR   => $this->verifikatorDashboard(),
            Role::PENANDATANGAN => $this->penandatanganDashboard(),
            Role::RT_RW         => $this->rtRwDashboard(),
            Role::WARGA         => $this->wargaDashboard(),
            default             => $this->wargaDashboard(),
        };
    }

    /**
     * Admin Dashboard — Full system overview.
     */
    private function adminDashboard()
    {
        $data = [
            'totalPenduduk'      => 12450,
            'totalKK'            => 3120,
            'totalSuratBulanIni' => 87,
            'suratMenunggu'      => 5,
            'mutasiLahir'        => 8,
            'mutasiMeninggal'    => 3,
            'mutasiDatang'       => 12,
            'mutasiPindah'       => 6,
            'totalUsaha'         => 156,
            'usahaAktif'         => 142,
            'usahaTidakAktif'    => 14,
            'totalUsers'         => 24,
            'activeUsers'        => 20,
            'totalRW'            => 8,
            'totalRT'            => 42,
        ];

        return view('dashboard.admin.index', $data);
    }

    /**
     * Operator Dashboard — Data entry & surat processing.
     */
    private function operatorDashboard()
    {
        $data = [
            'totalPenduduk'       => 12450,
            'totalKK'             => 3120,
            'suratHariIni'        => 12,
            'suratDraft'          => 3,
            'suratMenungguCetak'  => 4,
            'suratSelesaiHariIni' => 8,
            'pendudukBaruBulanIni'=> 15,
        ];

        return view('dashboard.operator.index', $data);
    }

    /**
     * Verifikator Dashboard — Verification queue.
     */
    private function verifikatorDashboard()
    {
        $data = [
            'suratMenungguVerifikasi' => 7,
            'suratDisetujuiHariIni'   => 5,
            'suratDitolakHariIni'     => 1,
            'suratPerluPerbaikan'     => 2,
            'totalVerifikasiBulanIni' => 65,
        ];

        return view('dashboard.verifikator.index', $data);
    }

    /**
     * Penandatangan Dashboard — Signing queue.
     */
    private function penandatanganDashboard()
    {
        $data = [
            'suratMenungguTTD'           => 8,
            'suratDitandatanganiHariIni' => 6,
            'totalTTDBulanIni'           => 72,
            'suratSelesaiBulanIni'       => 70,
        ];

        return view('dashboard.penandatangan.index', $data);
    }

    /**
     * RT/RW Dashboard — Community monitoring.
     */
    private function rtRwDashboard()
    {
        $data = [
            'totalWarga'        => 320,
            'totalKKWilayah'    => 85,
            'pengantarBulanIni' => 12,
            'laporanMasuk'      => 3,
            'mutasiDatang'      => 4,
            'mutasiPindah'      => 2,
            'usahaWilayah'      => 18,
        ];

        return view('dashboard.rt-rw.index', $data);
    }

    /**
     * Warga Dashboard — Service portal.
     */
    private function wargaDashboard()
    {
        $data = [
            'permohonanAktif' => 2,
            'suratSelesai'    => 5,
            'totalPermohonan' => 7,
        ];

        return view('dashboard.warga.index', $data);
    }
}
