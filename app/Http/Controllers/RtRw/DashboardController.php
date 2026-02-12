<?php

namespace App\Http\Controllers\RtRw;

use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use App\Models\Kelahiran;
use App\Models\Kematian;
use App\Models\MutasiPenduduk;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\Surat;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get RT IDs that belong to this user's wilayah.
     */
    private function getRtIds(Request $request): array
    {
        $user = $request->user();
        $rwNomor = (int) $user->wilayah_rw;
        $rtNomor = $user->wilayah_rt ? (int) $user->wilayah_rt : null;

        // Find the RW record
        $rw = Rw::where('nomor', $rwNomor)->first();
        if (! $rw) {
            return [];
        }

        // If user is RW-level (no specific RT), get all RTs in this RW
        if (! $rtNomor) {
            return Rt::where('rw_id', $rw->id)->pluck('id')->toArray();
        }

        // If user is RT-level, get only that specific RT
        $rt = Rt::where('rw_id', $rw->id)->where('nomor', $rtNomor)->first();
        return $rt ? [$rt->id] : [];
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $rtIds = $this->getRtIds($request);

        // Statistics
        $totalWarga = Penduduk::whereIn('rt_id', $rtIds)->count();
        $totalKK    = Keluarga::whereIn('rt_id', $rtIds)->count();

        // Surat pengantar bulan ini (surat with pemohon linked to penduduk in this area)
        $totalPengantarBulanIni = Surat::whereHas('pemohon.penduduk', function ($q) use ($rtIds) {
                $q->whereIn('rt_id', $rtIds);
            })
            ->whereMonth('tanggal_surat', now()->month)
            ->whereYear('tanggal_surat', now()->year)
            ->count();

        // Gender composition
        $lakiLaki  = Penduduk::whereIn('rt_id', $rtIds)->where('jenis_kelamin', 'L')->count();
        $perempuan = Penduduk::whereIn('rt_id', $rtIds)->where('jenis_kelamin', 'P')->count();

        // Recent warga (5 terbaru)
        $recentWarga = Penduduk::whereIn('rt_id', $rtIds)
            ->with('rt.rw')
            ->latest()
            ->take(5)
            ->get();

        // Recent mutasi
        $recentMutasi = MutasiPenduduk::where(function ($q) use ($rtIds) {
                $q->whereIn('rt_asal_id', $rtIds)
                  ->orWhereIn('rt_tujuan_id', $rtIds);
            })
            ->with('penduduk')
            ->latest('tanggal_mutasi')
            ->take(5)
            ->get();

        // Recent kelahiran
        $recentKelahiran = Kelahiran::whereIn('rt_id', $rtIds)
            ->latest('tanggal_lahir')
            ->take(3)
            ->get();

        // Recent kematian
        $recentKematian = Kematian::whereHas('penduduk', function ($q) use ($rtIds) {
                $q->whereIn('rt_id', $rtIds);
            })
            ->with('penduduk')
            ->latest('tanggal_meninggal')
            ->take(3)
            ->get();

        // Recent surat for this area
        $recentSurat = Surat::whereHas('pemohon.penduduk', function ($q) use ($rtIds) {
                $q->whereIn('rt_id', $rtIds);
            })
            ->with(['jenis', 'pemohon.penduduk'])
            ->latest('tanggal_surat')
            ->take(5)
            ->get();

        return view('rt-rw.dashboard', compact(
            'user',
            'totalWarga',
            'totalKK',
            'totalPengantarBulanIni',
            'lakiLaki',
            'perempuan',
            'recentWarga',
            'recentMutasi',
            'recentKelahiran',
            'recentKematian',
            'recentSurat',
            'rtIds',
        ));
    }
}
