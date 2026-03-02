<?php

namespace App\Http\Controllers\RtRw;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HasWilayahScope;
use App\Models\Faskes;
use App\Models\Keluarga;
use App\Models\Kelahiran;
use App\Models\Kematian;
use App\Models\MutasiPenduduk;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\TempatIbadah;
use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use HasWilayahScope;

    public function index(Request $request)
    {
        $user  = $request->user();
        $rtIds = $this->wilayahRtIds();
        $rwIds = $this->wilayahRwIds();

        // ── Label wilayah ────────────────────────────────────
        $wilayahLabel = 'RW ' . str_pad($user->wilayah_rw ?? '-', 2, '0', STR_PAD_LEFT);
        if ($user->wilayah_rt) {
            $wilayahLabel = 'RT ' . str_pad($user->wilayah_rt, 2, '0', STR_PAD_LEFT) . ' / ' . $wilayahLabel;
        }

        // ── Core Stats ───────────────────────────────────────
        $totalWarga = Penduduk::whereIn('rt_id', $rtIds)->count();
        $totalKK    = Keluarga::whereIn('rt_id', $rtIds)->count();
        $lakiLaki   = Penduduk::whereIn('rt_id', $rtIds)->where('jenis_kelamin', 'L')->count();
        $perempuan  = Penduduk::whereIn('rt_id', $rtIds)->where('jenis_kelamin', 'P')->count();
        $totalUmkm  = Umkm::whereIn('rt_id', $rtIds)->count();
        $umkmAktif  = Umkm::whereIn('rt_id', $rtIds)->where('status', 'aktif')->count();

        // ── RT breakdown (hanya jika user RW — melihat semua RT) ──
        $rtBreakdown = [];
        if (! $user->wilayah_rt && count($rtIds) > 1) {
            $rtBreakdown = Rt::select('rts.id', 'rts.nomor')
                ->whereIn('rts.id', $rtIds)
                ->withCount(['penduduks', 'keluargas', 'umkms'])
                ->orderBy('rts.nomor')
                ->get();
        }

        // ── Mutasi Bulan Ini ─────────────────────────────────
        $mutasiLahir = Kelahiran::whereIn('rt_id', $rtIds)
            ->whereMonth('tanggal_lahir', now()->month)
            ->whereYear('tanggal_lahir', now()->year)->count();
        $mutasiMeninggal = Kematian::whereHas('penduduk', fn ($q) => $q->whereIn('rt_id', $rtIds))
            ->whereMonth('tanggal_meninggal', now()->month)
            ->whereYear('tanggal_meninggal', now()->year)->count();
        $mutasiDatang = MutasiPenduduk::where('jenis_mutasi', 'datang')
            ->whereIn('rt_tujuan_id', $rtIds)
            ->whereMonth('tanggal_mutasi', now()->month)
            ->whereYear('tanggal_mutasi', now()->year)->count();
        $mutasiPindah = MutasiPenduduk::where('jenis_mutasi', 'pindah')
            ->whereIn('rt_asal_id', $rtIds)
            ->whereMonth('tanggal_mutasi', now()->month)
            ->whereYear('tanggal_mutasi', now()->year)->count();

        // ── Demografi: Jenis Kelamin (sudah ada lakiLaki/perempuan) ──
        $genderData = [];
        if ($lakiLaki > 0) $genderData['Laki-laki'] = $lakiLaki;
        if ($perempuan > 0) $genderData['Perempuan'] = $perempuan;

        // ── Demografi: Agama ─────────────────────────────────
        $agamaData = Penduduk::whereIn('rt_id', $rtIds)
            ->select('agama', DB::raw('count(*) as total'))
            ->whereNotNull('agama')
            ->groupBy('agama')
            ->orderByDesc('total')
            ->pluck('total', 'agama')
            ->toArray();

        // ── Demografi: Pendidikan ────────────────────────────
        $pendidikanData = Penduduk::whereIn('rt_id', $rtIds)
            ->select('pendidikan', DB::raw('count(*) as total'))
            ->whereNotNull('pendidikan')
            ->groupBy('pendidikan')
            ->orderByDesc('total')
            ->pluck('total', 'pendidikan')
            ->toArray();

        // ── Demografi: Status Kawin ──────────────────────────
        $statusKawinData = Penduduk::whereIn('rt_id', $rtIds)
            ->select('status_kawin', DB::raw('count(*) as total'))
            ->whereNotNull('status_kawin')
            ->groupBy('status_kawin')
            ->orderByDesc('total')
            ->pluck('total', 'status_kawin')
            ->toArray();

        // ── Trend Mutasi 6 Bulan Terakhir ────────────────────
        $mutasiTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $month = $date->month;
            $year  = $date->year;

            $mutasiTrend[] = [
                'label'     => $date->translatedFormat('M Y'),
                'lahir'     => Kelahiran::whereIn('rt_id', $rtIds)->whereMonth('tanggal_lahir', $month)->whereYear('tanggal_lahir', $year)->count(),
                'meninggal' => Kematian::whereHas('penduduk', fn ($q) => $q->whereIn('rt_id', $rtIds))->whereMonth('tanggal_meninggal', $month)->whereYear('tanggal_meninggal', $year)->count(),
                'datang'    => MutasiPenduduk::where('jenis_mutasi', 'datang')->whereIn('rt_tujuan_id', $rtIds)->whereMonth('tanggal_mutasi', $month)->whereYear('tanggal_mutasi', $year)->count(),
                'pindah'    => MutasiPenduduk::where('jenis_mutasi', 'pindah')->whereIn('rt_asal_id', $rtIds)->whereMonth('tanggal_mutasi', $month)->whereYear('tanggal_mutasi', $year)->count(),
            ];
        }

        // ── Fasilitas di wilayah ─────────────────────────────
        $totalFaskes       = Faskes::whereIn('rw_id', $rwIds)->count();
        $totalTempatIbadah = TempatIbadah::whereIn('rt_id', $rtIds)->count();

        // ── Recent data ──────────────────────────────────────
        $recentWarga = Penduduk::whereIn('rt_id', $rtIds)
            ->with('rt.rw')
            ->latest()
            ->take(5)
            ->get();

        $recentMutasi = MutasiPenduduk::where(function ($q) use ($rtIds) {
                $q->whereIn('rt_asal_id', $rtIds)
                  ->orWhereIn('rt_tujuan_id', $rtIds);
            })
            ->with('penduduk')
            ->latest('tanggal_mutasi')
            ->take(5)
            ->get();

        $recentKelahiran = Kelahiran::whereIn('rt_id', $rtIds)
            ->latest('tanggal_lahir')
            ->take(3)
            ->get();

        $recentKematian = Kematian::whereHas('penduduk', fn ($q) => $q->whereIn('rt_id', $rtIds))
            ->with('penduduk')
            ->latest('tanggal_meninggal')
            ->take(3)
            ->get();

        return view('rt-rw.dashboard', compact(
            'user', 'wilayahLabel',
            'totalWarga', 'totalKK', 'lakiLaki', 'perempuan',
            'totalUmkm', 'umkmAktif',
            'rtBreakdown',
            'mutasiLahir', 'mutasiMeninggal', 'mutasiDatang', 'mutasiPindah',
            'genderData', 'agamaData', 'pendidikanData', 'statusKawinData',
            'mutasiTrend',
            'totalFaskes', 'totalTempatIbadah',
            'recentWarga', 'recentMutasi', 'recentKelahiran', 'recentKematian',
            'rtIds',
        ));
    }
}
