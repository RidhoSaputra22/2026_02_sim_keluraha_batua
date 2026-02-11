<?php

namespace App\Http\Controllers\Persuratan;

use App\Http\Controllers\Controller;
use App\Models\Kelurahan;
use App\Models\Pemohon;
use App\Models\Penduduk;
use App\Models\Surat;
use App\Models\SuratJenis;
use App\Models\SuratSifat;
use Illuminate\Http\Request;

class PermohonanController extends Controller
{
    public function index(Request $request)
    {
        $query = Surat::with(['jenis', 'sifat', 'pemohon.penduduk', 'petugasInput']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                    ->orWhere('perihal', 'like', "%{$search}%")
                    ->orWhereHas('pemohon', function ($qp) use ($search) {
                        $qp->where('nama', 'like', "%{$search}%");
                    })
                    ->orWhereHas('pemohon.penduduk', function ($qp) use ($search) {
                        $qp->where('nik', 'like', "%{$search}%");
                    });
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status_esign', $status);
        }

        if ($jenis = $request->get('jenis_id')) {
            $query->where('jenis_id', $jenis);
        }

        $surats = $query->latest('tgl_input')->paginate(15)->withQueryString();
        $jenisList = SuratJenis::orderBy('nama')->get();

        return view('persuratan.permohonan.index', compact('surats', 'jenisList'));
    }

    public function create()
    {
        $jenisList = SuratJenis::orderBy('nama')->get();
        $sifatList = SuratSifat::orderBy('nama')->get();
        $pendudukList = Penduduk::orderBy('nama')->get();
        $kelurahanList = Kelurahan::orderBy('nama')->get();

        return view('persuratan.permohonan.create', compact('jenisList', 'sifatList', 'pendudukList', 'kelurahanList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelurahan_id' => ['required', 'exists:kelurahans,id'],
            'arah' => ['required', 'in:masuk,keluar'],
            'jenis_id' => ['required', 'exists:surat_jenis,id'],
            'sifat_id' => ['nullable', 'exists:surat_sifat,id'],
            'perihal' => ['required', 'string', 'max:255'],
            'uraian' => ['nullable', 'string'],
            'tujuan_surat' => ['nullable', 'string', 'max:255'],
            'nama_dalam_surat' => ['nullable', 'string', 'max:255'],
            'penduduk_id' => ['nullable', 'exists:penduduks,id'],
            'pemohon_nama' => ['required', 'string', 'max:255'],
            'pemohon_no_hp' => ['nullable', 'string', 'max:20'],
            'pemohon_email' => ['nullable', 'email', 'max:255'],
            'tanggal_surat' => ['nullable', 'date'],
        ]);

        // Create or find pemohon
        $pemohon = Pemohon::create([
            'penduduk_id' => $validated['penduduk_id'] ?? null,
            'nama' => $validated['pemohon_nama'],
            'no_hp_wa' => $validated['pemohon_no_hp'] ?? null,
            'email' => $validated['pemohon_email'] ?? null,
        ]);

        // Generate nomor surat temporary (draft)
        $nomorUrut = Surat::whereYear('tgl_input', now()->year)->count() + 1;
        $nomorSurat = sprintf('%03d/SKB/%s/%s', $nomorUrut, now()->format('m'), now()->format('Y'));

        Surat::create([
            'kelurahan_id' => $validated['kelurahan_id'],
            'arah' => $validated['arah'],
            'nomor_surat' => $nomorSurat,
            'tanggal_surat' => $validated['tanggal_surat'] ?? now()->toDateString(),
            'jenis_id' => $validated['jenis_id'],
            'sifat_id' => $validated['sifat_id'] ?? null,
            'perihal' => $validated['perihal'],
            'uraian' => $validated['uraian'] ?? null,
            'tujuan_surat' => $validated['tujuan_surat'] ?? null,
            'nama_dalam_surat' => $validated['nama_dalam_surat'] ?? null,
            'pemohon_id' => $pemohon->id,
            'status_esign' => 'draft',
            'tgl_input' => now(),
            'petugas_input_id' => auth()->id(),
        ]);

        return redirect()->route('persuratan.permohonan.index')
            ->with('success', 'Permohonan surat berhasil dibuat dengan status Draft.');
    }

    public function edit(Surat $permohonan)
    {
        $permohonan->load(['pemohon.penduduk', 'jenis', 'sifat']);
        $jenisList = SuratJenis::orderBy('nama')->get();
        $sifatList = SuratSifat::orderBy('nama')->get();
        $pendudukList = Penduduk::orderBy('nama')->get();
        $kelurahanList = Kelurahan::orderBy('nama')->get();

        return view('persuratan.permohonan.edit', compact('permohonan', 'jenisList', 'sifatList', 'pendudukList', 'kelurahanList'));
    }

    public function update(Request $request, Surat $permohonan)
    {
        $validated = $request->validate([
            'kelurahan_id' => ['required', 'exists:kelurahans,id'],
            'arah' => ['required', 'in:masuk,keluar'],
            'jenis_id' => ['required', 'exists:surat_jenis,id'],
            'sifat_id' => ['nullable', 'exists:surat_sifat,id'],
            'perihal' => ['required', 'string', 'max:255'],
            'uraian' => ['nullable', 'string'],
            'tujuan_surat' => ['nullable', 'string', 'max:255'],
            'nama_dalam_surat' => ['nullable', 'string', 'max:255'],
            'pemohon_nama' => ['required', 'string', 'max:255'],
            'pemohon_no_hp' => ['nullable', 'string', 'max:20'],
            'pemohon_email' => ['nullable', 'email', 'max:255'],
            'tanggal_surat' => ['nullable', 'date'],
        ]);

        // Update pemohon
        if ($permohonan->pemohon) {
            $permohonan->pemohon->update([
                'nama' => $validated['pemohon_nama'],
                'no_hp_wa' => $validated['pemohon_no_hp'] ?? null,
                'email' => $validated['pemohon_email'] ?? null,
            ]);
        }

        $permohonan->update([
            'kelurahan_id' => $validated['kelurahan_id'],
            'arah' => $validated['arah'],
            'jenis_id' => $validated['jenis_id'],
            'sifat_id' => $validated['sifat_id'] ?? null,
            'perihal' => $validated['perihal'],
            'uraian' => $validated['uraian'] ?? null,
            'tujuan_surat' => $validated['tujuan_surat'] ?? null,
            'nama_dalam_surat' => $validated['nama_dalam_surat'] ?? null,
            'tanggal_surat' => $validated['tanggal_surat'] ?? $permohonan->tanggal_surat,
        ]);

        return redirect()->route('persuratan.permohonan.index')
            ->with('success', 'Permohonan surat berhasil diperbarui.');
    }

    public function destroy(Surat $permohonan)
    {
        if ($permohonan->status_esign !== 'draft') {
            return redirect()->route('persuratan.permohonan.index')
                ->with('error', 'Hanya surat berstatus Draft yang dapat dihapus.');
        }

        $permohonan->delete();

        return redirect()->route('persuratan.permohonan.index')
            ->with('success', 'Permohonan surat berhasil dihapus.');
    }
}
