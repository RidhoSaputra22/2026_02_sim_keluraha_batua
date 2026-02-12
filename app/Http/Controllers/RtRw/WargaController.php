<?php

namespace App\Http\Controllers\RtRw;

use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\Rw;
use Illuminate\Http\Request;

class WargaController extends Controller
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
     * Daftar warga di wilayah RT/RW.
     */
    public function index(Request $request)
    {
        $rtIds = $this->getRtIds($request);

        $query = Penduduk::whereIn('rt_id', $rtIds)->with('rt.rw', 'keluarga');

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // Filter jenis kelamin
        if ($jk = $request->input('jenis_kelamin')) {
            $query->where('jenis_kelamin', $jk);
        }

        $penduduk = $query->orderBy('nama')->paginate(15)->withQueryString();

        return view('rt-rw.warga.index', compact('penduduk'));
    }

    /**
     * Detail warga.
     */
    public function show(Penduduk $penduduk, Request $request)
    {
        $rtIds = $this->getRtIds($request);

        // Ensure warga belongs to this RT/RW
        if (! in_array($penduduk->rt_id, $rtIds)) {
            abort(403, 'Warga tidak berada di wilayah Anda.');
        }

        $penduduk->load('rt.rw', 'keluarga.anggota', 'keluarga.kepalaKeluarga');

        return view('rt-rw.warga.show', compact('penduduk'));
    }

    /**
     * Daftar KK di wilayah RT/RW.
     */
    public function keluarga(Request $request)
    {
        $rtIds = $this->getRtIds($request);

        $query = Keluarga::whereIn('rt_id', $rtIds)->with('kepalaKeluarga', 'rt.rw');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('no_kk', 'like', "%{$search}%")
                  ->orWhereHas('kepalaKeluarga', function ($q2) use ($search) {
                      $q2->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $keluarga = $query->orderBy('no_kk')->paginate(15)->withQueryString();

        return view('rt-rw.warga.keluarga', compact('keluarga'));
    }
}
