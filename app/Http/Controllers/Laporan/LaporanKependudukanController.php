<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use App\Models\MutasiPenduduk;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\Rw;
use Illuminate\Http\Request;

class LaporanKependudukanController extends Controller
{
    public function index(Request $request)
    {
        $totalPenduduk = Penduduk::count();
        $totalKK = Keluarga::count();
        $totalLaki = Penduduk::where('jenis_kelamin', 'L')->count();
        $totalPerempuan = Penduduk::where('jenis_kelamin', 'P')->count();

        // Per RW
        $perRw = Rw::withCount(['rts'])->orderBy('nomor')->get()->map(function ($rw) {
            $rtIds = $rw->rts->pluck('id');
            $rw->jumlah_penduduk = Penduduk::whereIn('rt_id', $rtIds)->count();
            $rw->jumlah_kk = Keluarga::whereIn('rt_id', $rtIds)->count();
            return $rw;
        });

        // Mutasi bulan ini
        $mutasiBulanIni = MutasiPenduduk::whereMonth('tanggal_mutasi', now()->month)
            ->whereYear('tanggal_mutasi', now()->year)
            ->count();

        // Per agama
        $perAgama = Penduduk::selectRaw('agama, count(*) as total')
            ->whereNotNull('agama')
            ->groupBy('agama')
            ->orderByDesc('total')
            ->get();

        // Per pendidikan
        $perPendidikan = Penduduk::selectRaw('pendidikan, count(*) as total')
            ->whereNotNull('pendidikan')
            ->groupBy('pendidikan')
            ->orderByDesc('total')
            ->get();

        // Per status kawin
        $perStatusKawin = Penduduk::selectRaw('status_kawin, count(*) as total')
            ->whereNotNull('status_kawin')
            ->groupBy('status_kawin')
            ->orderByDesc('total')
            ->get();

        return view('laporan.kependudukan', compact(
            'totalPenduduk', 'totalKK', 'totalLaki', 'totalPerempuan',
            'perRw', 'mutasiBulanIni', 'perAgama', 'perPendidikan', 'perStatusKawin'
        ));
    }
}
