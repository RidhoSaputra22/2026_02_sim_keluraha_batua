<?php

namespace App\Http\Controllers\Persuratan;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Models\SuratJenis;
use Illuminate\Http\Request;

class ArsipController extends Controller
{
    public function index(Request $request)
    {
        $query = Surat::with(['jenis', 'sifat', 'pemohon.penduduk', 'petugasInput', 'verifikator', 'penandatanganPejabat.pegawai'])
            ->where('status_esign', 'signed');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                    ->orWhere('perihal', 'like', "%{$search}%")
                    ->orWhereHas('pemohon', function ($qp) use ($search) {
                        $qp->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        if ($jenis = $request->get('jenis_id')) {
            $query->where('jenis_id', $jenis);
        }

        if ($from = $request->get('dari_tanggal')) {
            $query->whereDate('tanggal_surat', '>=', $from);
        }

        if ($to = $request->get('sampai_tanggal')) {
            $query->whereDate('tanggal_surat', '<=', $to);
        }

        $surats = $query->latest('tgl_ttd')->paginate(15)->withQueryString();
        $jenisList = SuratJenis::orderBy('nama')->get();

        return view('persuratan.arsip.index', compact('surats', 'jenisList'));
    }
}
