<?php

namespace App\Http\Controllers\RtRw;

use App\Http\Controllers\Controller;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\Surat;
use App\Models\SuratJenis;
use Illuminate\Http\Request;

class PengantarController extends Controller
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
     * Daftar surat pengantar di wilayah.
     */
    public function index(Request $request)
    {
        $rtIds = $this->getRtIds($request);

        $query = Surat::whereHas('pemohon.penduduk', function ($q) use ($rtIds) {
                $q->whereIn('rt_id', $rtIds);
            })
            ->with(['jenis', 'pemohon.penduduk', 'petugasInput']);

        // Filter status
        if ($status = $request->input('status')) {
            $query->where('status_esign', $status);
        }

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                  ->orWhere('perihal', 'like', "%{$search}%")
                  ->orWhereHas('pemohon', function ($q2) use ($search) {
                      $q2->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $surats = $query->latest('tanggal_surat')->paginate(15)->withQueryString();

        return view('rt-rw.pengantar.index', compact('surats'));
    }

    /**
     * Detail surat pengantar.
     */
    public function show(Surat $surat, Request $request)
    {
        $rtIds = $this->getRtIds($request);

        // Check access
        $pemohonPenduduk = $surat->pemohon?->penduduk;
        if ($pemohonPenduduk && ! in_array($pemohonPenduduk->rt_id, $rtIds)) {
            abort(403, 'Surat bukan dari wilayah Anda.');
        }

        $surat->load(['jenis', 'sifat', 'pemohon.penduduk', 'petugasInput', 'verifikator', 'penandatanganPejabat']);

        return view('rt-rw.pengantar.show', compact('surat'));
    }
}
