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
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Get RT IDs that belong to this user's wilayah.
     */
    private function getRtIds(Request $request): array
    {
        $user = $request->user();
        $rwNomor = (int) $user->wilayah_rw;
        $rtNomor = $user->wilayah_rt ? (int) $user->wilayah_rt : null;

        $rw = Rw::where('nomor', $rwNomor)->first();
        if (! $rw) {
            return [];
        }

        if (! $rtNomor) {
            return Rt::where('rw_id', $rw->id)->pluck('id')->toArray();
        }

        $rt = Rt::where('rw_id', $rw->id)->where('nomor', $rtNomor)->first();
        return $rt ? [$rt->id] : [];
    }

    /**
     * Laporan statistik wilayah.
     */
    public function index(Request $request)
    {
        $rtIds = $this->getRtIds($request);

        // Total statistics
        $totalWarga   = Penduduk::whereIn('rt_id', $rtIds)->count();
        $totalKK      = Keluarga::whereIn('rt_id', $rtIds)->count();
        $lakiLaki     = Penduduk::whereIn('rt_id', $rtIds)->where('jenis_kelamin', 'L')->count();
        $perempuan    = Penduduk::whereIn('rt_id', $rtIds)->where('jenis_kelamin', 'P')->count();

        // Mutasi statistics (current year)
        $mutasiDatang  = MutasiPenduduk::whereIn('rt_tujuan_id', $rtIds)
            ->where('jenis_mutasi', 'datang')
            ->whereYear('tanggal_mutasi', now()->year)->count();
        $mutasiPindah  = MutasiPenduduk::whereIn('rt_asal_id', $rtIds)
            ->where('jenis_mutasi', 'pindah')
            ->whereYear('tanggal_mutasi', now()->year)->count();
        $kelahiranTahunIni = Kelahiran::whereIn('rt_id', $rtIds)
            ->whereYear('tanggal_lahir', now()->year)->count();
        $kematianTahunIni  = Kematian::whereHas('penduduk', function ($q) use ($rtIds) {
            $q->whereIn('rt_id', $rtIds);
        })
            ->whereYear('tanggal_meninggal', now()->year)->count();

        // Per-RT breakdown
        $perRtStats = Rt::whereIn('id', $rtIds)
            ->with('rw')
            ->withCount(['penduduks' => function ($q) {
                // No extra filter needed since we're already filtering by rt_id
            }])
            ->get()
            ->map(function ($rt) {
                $rt->jumlah_kk = Keluarga::where('rt_id', $rt->id)->count();
                return $rt;
            });

        // Agama breakdown
        $agamaStats = Penduduk::whereIn('rt_id', $rtIds)
            ->selectRaw('agama, count(*) as total')
            ->groupBy('agama')
            ->orderByDesc('total')
            ->get();

        // Pendidikan breakdown
        $pendidikanStats = Penduduk::whereIn('rt_id', $rtIds)
            ->selectRaw('pendidikan, count(*) as total')
            ->groupBy('pendidikan')
            ->orderByDesc('total')
            ->get();

        // Status kawin breakdown
        $statusKawinStats = Penduduk::whereIn('rt_id', $rtIds)
            ->selectRaw('status_kawin, count(*) as total')
            ->groupBy('status_kawin')
            ->orderByDesc('total')
            ->get();

        return view('rt-rw.laporan.index', compact(
            'totalWarga',
            'totalKK',
            'lakiLaki',
            'perempuan',
            'mutasiDatang',
            'mutasiPindah',
            'kelahiranTahunIni',
            'kematianTahunIni',
            'perRtStats',
            'agamaStats',
            'pendidikanStats',
            'statusKawinStats',
        ));
    }
}
