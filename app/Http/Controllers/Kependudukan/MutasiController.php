<?php

namespace App\Http\Controllers\Kependudukan;

use App\Http\Controllers\Controller;
use App\Models\MutasiPenduduk;
use App\Models\Penduduk;
use App\Models\Rt;
use Illuminate\Http\Request;

class MutasiController extends Controller
{
    public function index(Request $request)
    {
        $query = MutasiPenduduk::with(['penduduk', 'rtAsal.rw', 'rtTujuan.rw', 'petugas']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('penduduk', function ($qp) use ($search) {
                    $qp->where('nama', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%");
                })
                ->orWhere('no_surat_pindah', 'like', "%{$search}%")
                ->orWhere('alasan', 'like', "%{$search}%");
            });
        }

        if ($jenis = $request->get('jenis_mutasi')) {
            $query->where('jenis_mutasi', $jenis);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $mutasi = $query->latest('tanggal_mutasi')->paginate(15)->withQueryString();

        return view('kependudukan.mutasi.index', compact('mutasi'));
    }

    public function create()
    {
        $pendudukList = Penduduk::orderBy('nama')->get();
        $rtList = Rt::with('rw')->orderBy('rw_id')->orderBy('nomor')->get();

        return view('kependudukan.mutasi.create', compact('pendudukList', 'rtList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'penduduk_id' => ['required', 'exists:penduduks,id'],
            'jenis_mutasi' => ['required', 'in:pindah,datang'],
            'tanggal_mutasi' => ['required', 'date'],
            'alamat_asal' => ['nullable', 'string', 'max:255'],
            'alamat_tujuan' => ['nullable', 'string', 'max:255'],
            'rt_asal_id' => ['nullable', 'exists:rts,id'],
            'rt_tujuan_id' => ['nullable', 'exists:rts,id'],
            'alasan' => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'no_surat_pindah' => ['nullable', 'string', 'max:100'],
        ]);

        $validated['petugas_id'] = auth()->id();
        $validated['status'] = 'proses';

        MutasiPenduduk::create($validated);

        return redirect()->route('kependudukan.mutasi.index')
            ->with('success', 'Data mutasi penduduk berhasil ditambahkan.');
    }

    public function edit(MutasiPenduduk $mutasi)
    {
        $pendudukList = Penduduk::orderBy('nama')->get();
        $rtList = Rt::with('rw')->orderBy('rw_id')->orderBy('nomor')->get();

        return view('kependudukan.mutasi.edit', compact('mutasi', 'pendudukList', 'rtList'));
    }

    public function update(Request $request, MutasiPenduduk $mutasi)
    {
        $validated = $request->validate([
            'penduduk_id' => ['required', 'exists:penduduks,id'],
            'jenis_mutasi' => ['required', 'in:pindah,datang'],
            'tanggal_mutasi' => ['required', 'date'],
            'alamat_asal' => ['nullable', 'string', 'max:255'],
            'alamat_tujuan' => ['nullable', 'string', 'max:255'],
            'rt_asal_id' => ['nullable', 'exists:rts,id'],
            'rt_tujuan_id' => ['nullable', 'exists:rts,id'],
            'alasan' => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'no_surat_pindah' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:proses,selesai,batal'],
        ]);

        $mutasi->update($validated);

        return redirect()->route('kependudukan.mutasi.index')
            ->with('success', 'Data mutasi penduduk berhasil diperbarui.');
    }

    public function destroy(MutasiPenduduk $mutasi)
    {
        $mutasi->delete();

        return redirect()->route('kependudukan.mutasi.index')
            ->with('success', 'Data mutasi penduduk berhasil dihapus.');
    }
}
