<?php

namespace App\Http\Controllers\Kependudukan;

use App\Http\Controllers\Concerns\HasWilayahScope;
use App\Http\Controllers\Controller;
use App\Models\Kematian;
use Illuminate\Http\Request;

class KematianController extends Controller
{
    use HasWilayahScope;

    public function index(Request $request)
    {
        $query = Kematian::with(['penduduk.rt.rw', 'petugas']);

        // Kematian tidak punya rt_id langsung — scope melalui relasi penduduk
        $this->applyWilayahScopeViaRelation($query, 'penduduk');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('penduduk', function ($qp) use ($search) {
                    $qp->where('nama', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%");
                })
                ->orWhere('no_akte_kematian', 'like', "%{$search}%")
                ->orWhere('penyebab', 'like', "%{$search}%");
            });
        }

        $kematian = $query->latest('tanggal_meninggal')->paginate(15)->withQueryString();

        return view('kependudukan.kematian.index', compact('kematian'));
    }

    public function create()
    {
        $pendudukList = $this->wilayahPendudukList();

        return view('kependudukan.kematian.create', compact('pendudukList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'penduduk_id'       => ['required', 'exists:penduduks,id'],
            'tanggal_meninggal' => ['required', 'date'],
            'tempat_meninggal'  => ['nullable', 'string', 'max:255'],
            'penyebab'          => ['nullable', 'string', 'max:255'],
            'no_akte_kematian'  => ['nullable', 'string', 'max:100'],
            'keterangan'        => ['nullable', 'string', 'max:500'],
        ]);

        // Pastikan penduduk yang dipilih berada di wilayah RT/RW
        if ($this->isRtRw()) {
            $penduduk = \App\Models\Penduduk::findOrFail($validated['penduduk_id']);
            $this->authorizeWilayah($penduduk);
        }

        $validated['petugas_id'] = auth()->id();

        Kematian::create($validated);

        return redirect()->route('kependudukan.kematian.index')
            ->with('success', 'Data kematian berhasil ditambahkan.');
    }

    public function edit(Kematian $kematian)
    {
        $kematian->load('penduduk');
        $this->authorizeWilayah($kematian->penduduk);

        $pendudukList = $this->wilayahPendudukList();

        return view('kependudukan.kematian.edit', compact('kematian', 'pendudukList'));
    }

    public function update(Request $request, Kematian $kematian)
    {
        $kematian->load('penduduk');
        $this->authorizeWilayah($kematian->penduduk);

        $validated = $request->validate([
            'penduduk_id'       => ['required', 'exists:penduduks,id'],
            'tanggal_meninggal' => ['required', 'date'],
            'tempat_meninggal'  => ['nullable', 'string', 'max:255'],
            'penyebab'          => ['nullable', 'string', 'max:255'],
            'no_akte_kematian'  => ['nullable', 'string', 'max:100'],
            'keterangan'        => ['nullable', 'string', 'max:500'],
        ]);

        $kematian->update($validated);

        return redirect()->route('kependudukan.kematian.index')
            ->with('success', 'Data kematian berhasil diperbarui.');
    }

    public function destroy(Kematian $kematian)
    {
        $kematian->load('penduduk');
        $this->authorizeWilayah($kematian->penduduk);

        $kematian->delete();

        return redirect()->route('kependudukan.kematian.index')
            ->with('success', 'Data kematian berhasil dihapus.');
    }
}
