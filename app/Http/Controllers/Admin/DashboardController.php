<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Faskes;
use App\Models\JenisUsaha;
use App\Models\Keluarga;
use App\Models\Kelahiran;
use App\Models\Kematian;
use App\Models\MutasiPenduduk;
use App\Models\Penduduk;
use App\Models\PegawaiStaff;
use App\Models\Role;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\Sekolah;
use App\Models\TempatIbadah;
use App\Models\Umkm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ── Core Stats ───────────────────────────────────────
        $totalPenduduk = Penduduk::count();
        $totalKK       = Keluarga::count();
        $totalRT       = Rt::count();
        $totalRW       = Rw::count();
        $totalUsers    = User::count();
        $activeUsers   = User::where('is_active', true)->count();
        $totalPegawai  = PegawaiStaff::count();


        // ── Mutasi Bulan Ini ─────────────────────────────────
        $mutasiLahir     = Kelahiran::whereMonth('tanggal_lahir', now()->month)
            ->whereYear('tanggal_lahir', now()->year)->count();
        $mutasiMeninggal = Kematian::whereMonth('tanggal_meninggal', now()->month)
            ->whereYear('tanggal_meninggal', now()->year)->count();
        $mutasiDatang    = MutasiPenduduk::where('jenis_mutasi', 'datang')
            ->whereMonth('tanggal_mutasi', now()->month)
            ->whereYear('tanggal_mutasi', now()->year)->count();
        $mutasiPindah    = MutasiPenduduk::where('jenis_mutasi', 'pindah')
            ->whereMonth('tanggal_mutasi', now()->month)
            ->whereYear('tanggal_mutasi', now()->year)->count();

        // ── Usaha ────────────────────────────────────────────
        $totalUsaha       = Umkm::count();
        $usahaAktif       = Umkm::where('status', 'aktif')->count();
        $usahaTidakAktif  = Umkm::where('status', 'tidak_aktif')->count();

        // ── Users per Role & Recent Users ────────────────────
        $usersPerRole = Role::withCount('users')->get();
        $recentUsers  = User::with('role')->latest()->take(5)->get();

        // ── Demografi: Jenis Kelamin ─────────────────────────
        $genderData = Penduduk::select('jenis_kelamin', DB::raw('count(*) as total'))
            ->groupBy('jenis_kelamin')
            ->pluck('total', 'jenis_kelamin')
            ->toArray();

        // ── Demografi: Agama ─────────────────────────────────
        $agamaData = Penduduk::select('agama', DB::raw('count(*) as total'))
            ->whereNotNull('agama')
            ->groupBy('agama')
            ->orderByDesc('total')
            ->pluck('total', 'agama')
            ->toArray();

        // ── Demografi: Pendidikan ────────────────────────────
        $pendidikanData = Penduduk::select('pendidikan', DB::raw('count(*) as total'))
            ->whereNotNull('pendidikan')
            ->groupBy('pendidikan')
            ->orderByDesc('total')
            ->pluck('total', 'pendidikan')
            ->toArray();

        // ── Demografi: Status Kawin ──────────────────────────
        $statusKawinData = Penduduk::select('status_kawin', DB::raw('count(*) as total'))
            ->whereNotNull('status_kawin')
            ->groupBy('status_kawin')
            ->orderByDesc('total')
            ->pluck('total', 'status_kawin')
            ->toArray();

        // ── Persebaran Per RW ────────────────────────────────
        $pendudukPerRw = Rw::select('rws.id', 'rws.nomor')
            ->leftJoin('rts', 'rts.rw_id', '=', 'rws.id')
            ->leftJoin('penduduks', 'penduduks.rt_id', '=', 'rts.id')
            ->groupBy('rws.id', 'rws.nomor')
            ->orderBy('rws.nomor')
            ->selectRaw('count(penduduks.id) as total_penduduk')
            ->get();

        $kkPerRw = Rw::select('rws.id', 'rws.nomor')
            ->leftJoin('rts', 'rts.rw_id', '=', 'rws.id')
            ->leftJoin('keluargas', 'keluargas.rt_id', '=', 'rts.id')
            ->groupBy('rws.id', 'rws.nomor')
            ->orderBy('rws.nomor')
            ->selectRaw('count(keluargas.id) as total_kk')
            ->get();

        // ── Trend Mutasi 6 Bulan Terakhir ────────────────────
        $mutasiTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->month;
            $year  = $date->year;
            $label = $date->translatedFormat('M Y');

            $mutasiTrend[] = [
                'label'     => $label,
                'lahir'     => Kelahiran::whereMonth('tanggal_lahir', $month)->whereYear('tanggal_lahir', $year)->count(),
                'meninggal' => Kematian::whereMonth('tanggal_meninggal', $month)->whereYear('tanggal_meninggal', $year)->count(),
                'datang'    => MutasiPenduduk::where('jenis_mutasi', 'datang')->whereMonth('tanggal_mutasi', $month)->whereYear('tanggal_mutasi', $year)->count(),
                'pindah'    => MutasiPenduduk::where('jenis_mutasi', 'pindah')->whereMonth('tanggal_mutasi', $month)->whereYear('tanggal_mutasi', $year)->count(),
            ];
        }

        // ── UMKM Per Jenis Usaha ─────────────────────────────
        $umkmPerJenis = JenisUsaha::withCount('umkms')
            ->get()
            ->filter(fn($j) => $j->umkms_count > 0)
            ->sortByDesc('umkms_count')
            ->values()
            ->map(fn($j) => ['nama' => $j->nama, 'total' => $j->umkms_count]);

        // ── UMKM Per RW ──────────────────────────────────────
        $umkmPerRw = Rw::select('rws.id', 'rws.nomor')
            ->leftJoin('rts', 'rts.rw_id', '=', 'rws.id')
            ->leftJoin('umkms', 'umkms.rt_id', '=', 'rts.id')
            ->groupBy('rws.id', 'rws.nomor')
            ->orderBy('rws.nomor')
            ->selectRaw('count(umkms.id) as total_umkm')
            ->get();

        // ── Fasilitas Publik ─────────────────────────────────
        $totalFaskes       = Faskes::count();
        $totalTempatIbadah = TempatIbadah::count();
        $totalSekolah      = Sekolah::count();

        // ── Recent Audit Logs ────────────────────────────────
        $recentLogs = AuditLog::with('user')
            ->latest()
            ->take(8)
            ->get();

        // ── Golongan Darah ───────────────────────────────────
        $golDarahData = Penduduk::select('gol_darah', DB::raw('count(*) as total'))
            ->whereNotNull('gol_darah')
            ->where('gol_darah', '!=', '')
            ->groupBy('gol_darah')
            ->orderByDesc('total')
            ->pluck('total', 'gol_darah')
            ->toArray();

        return view('admin.dashboard', compact(
            'totalPenduduk', 'totalKK', 'totalRT', 'totalRW',
            'totalUsers', 'activeUsers', 'totalPegawai',
            'mutasiLahir', 'mutasiMeninggal', 'mutasiDatang', 'mutasiPindah',
            'totalUsaha', 'usahaAktif', 'usahaTidakAktif',
            'usersPerRole', 'recentUsers',
            'genderData', 'agamaData', 'pendidikanData', 'statusKawinData', 'golDarahData',
            'pendudukPerRw', 'kkPerRw',
            'mutasiTrend',
            'umkmPerJenis', 'umkmPerRw',
            'totalFaskes', 'totalTempatIbadah', 'totalSekolah',
            'recentLogs',
        ));
    }
}
