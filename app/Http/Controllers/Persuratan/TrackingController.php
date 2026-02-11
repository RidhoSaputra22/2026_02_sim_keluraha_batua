<?php

namespace App\Http\Controllers\Persuratan;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function index(Request $request)
    {
        $surat = null;
        $search = $request->get('search');

        if ($search) {
            $surat = Surat::with(['jenis', 'sifat', 'pemohon.penduduk', 'petugasInput', 'verifikator', 'penandatanganPejabat.pegawai'])
                ->where('nomor_surat', 'like', "%{$search}%")
                ->orWhereHas('pemohon', function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%");
                })
                ->orWhereHas('pemohon.penduduk', function ($q) use ($search) {
                    $q->where('nik', 'like', "%{$search}%");
                })
                ->latest('tgl_input')
                ->paginate(10)
                ->withQueryString();
        }

        return view('persuratan.tracking.index', compact('surat', 'search'));
    }
}
