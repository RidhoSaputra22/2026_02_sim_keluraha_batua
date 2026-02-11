<?php

namespace App\Http\Controllers\Persuratan;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Models\SuratJenis;
use Illuminate\Http\Request;

class VerifikasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Surat::with(['jenis', 'sifat', 'pemohon.penduduk', 'petugasInput', 'verifikator'])
            ->whereIn('status_esign', ['draft', 'proses']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                    ->orWhere('perihal', 'like', "%{$search}%")
                    ->orWhereHas('pemohon', function ($qp) use ($search) {
                        $qp->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status_esign', $status);
        }

        $surats = $query->latest('tgl_input')->paginate(15)->withQueryString();
        $jenisList = SuratJenis::orderBy('nama')->get();

        return view('persuratan.verifikasi.index', compact('surats', 'jenisList'));
    }

    public function approve(Request $request, Surat $surat)
    {
        if ($surat->status_esign !== 'draft') {
            return redirect()->route('persuratan.verifikasi.index')
                ->with('error', 'Hanya surat berstatus Draft yang dapat diverifikasi.');
        }

        $request->validate([
            'catatan_verifikasi' => ['nullable', 'string', 'max:500'],
        ]);

        $surat->update([
            'status_esign' => 'proses',
            'verifikator_id' => auth()->id(),
            'tgl_verifikasi' => now(),
            'catatan_verifikasi' => $request->get('catatan_verifikasi'),
        ]);

        return redirect()->route('persuratan.verifikasi.index')
            ->with('success', "Surat #{$surat->nomor_surat} berhasil diverifikasi.");
    }

    public function reject(Request $request, Surat $surat)
    {
        if ($surat->status_esign !== 'draft') {
            return redirect()->route('persuratan.verifikasi.index')
                ->with('error', 'Hanya surat berstatus Draft yang dapat ditolak.');
        }

        $request->validate([
            'catatan_verifikasi' => ['required', 'string', 'max:500'],
        ]);

        $surat->update([
            'status_esign' => 'reject',
            'verifikator_id' => auth()->id(),
            'tgl_verifikasi' => now(),
            'catatan_verifikasi' => $request->get('catatan_verifikasi'),
        ]);

        return redirect()->route('persuratan.verifikasi.index')
            ->with('success', "Surat #{$surat->nomor_surat} ditolak.");
    }
}
