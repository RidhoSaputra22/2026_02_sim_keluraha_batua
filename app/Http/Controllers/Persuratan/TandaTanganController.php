<?php

namespace App\Http\Controllers\Persuratan;

use App\Http\Controllers\Controller;
use App\Models\Penandatanganan;
use App\Models\Surat;
use Illuminate\Http\Request;

class TandaTanganController extends Controller
{
    public function index(Request $request)
    {
        $query = Surat::with(['jenis', 'sifat', 'pemohon.penduduk', 'verifikator', 'penandatanganPejabat.pegawai'])
            ->where('status_esign', 'proses');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                    ->orWhere('perihal', 'like', "%{$search}%")
                    ->orWhereHas('pemohon', function ($qp) use ($search) {
                        $qp->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        $surats = $query->latest('tgl_verifikasi')->paginate(15)->withQueryString();
        $penandatanganList = Penandatanganan::with('pegawai')
            ->where('status', 'aktif')
            ->get();

        return view('persuratan.tanda-tangan.index', compact('surats', 'penandatanganList'));
    }

    public function sign(Request $request, Surat $surat)
    {
        if ($surat->status_esign !== 'proses') {
            return redirect()->route('persuratan.tanda-tangan.index')
                ->with('error', 'Hanya surat terverifikasi yang dapat ditandatangani.');
        }

        $request->validate([
            'penandatangan_pejabat_id' => ['required', 'exists:penandatanganans,id'],
            'catatan_penandatangan' => ['nullable', 'string', 'max:500'],
        ]);

        $surat->update([
            'status_esign' => 'signed',
            'penandatangan_pejabat_id' => $request->get('penandatangan_pejabat_id'),
            'tgl_ttd' => now(),
            'catatan_penandatangan' => $request->get('catatan_penandatangan'),
        ]);

        return redirect()->route('persuratan.tanda-tangan.index')
            ->with('success', "Surat #{$surat->nomor_surat} berhasil ditandatangani.");
    }

    public function reject(Request $request, Surat $surat)
    {
        if ($surat->status_esign !== 'proses') {
            return redirect()->route('persuratan.tanda-tangan.index')
                ->with('error', 'Hanya surat terverifikasi yang dapat ditolak.');
        }

        $request->validate([
            'catatan_penandatangan' => ['required', 'string', 'max:500'],
        ]);

        $surat->update([
            'status_esign' => 'reject',
            'penandatangan_pejabat_id' => $request->get('penandatangan_pejabat_id'),
            'tgl_ttd' => now(),
            'catatan_penandatangan' => $request->get('catatan_penandatangan'),
        ]);

        return redirect()->route('persuratan.tanda-tangan.index')
            ->with('success', "Surat #{$surat->nomor_surat} ditolak oleh penandatangan.");
    }
}
