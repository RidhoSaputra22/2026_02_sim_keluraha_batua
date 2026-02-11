<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\Rw;
use Illuminate\Http\Request;

class KeluargaController extends Controller
{
    public function index(Request $request)
    {
        $query = Keluarga::with(['kepalaKeluarga', 'rt.rw']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('no_kk', 'like', "%{$search}%")
                  ->orWhereHas('kepalaKeluarga', function ($qk) use ($search) {
                      $qk->where('nama', 'like', "%{$search}%")
                          ->orWhere('nik', 'like', "%{$search}%");
                  });
            });
        }

        if ($rtId = $request->get('rt')) {
            $query->where('rt_id', $rtId);
        }

        if ($rwId = $request->get('rw')) {
            $query->whereHas('rt', function ($q) use ($rwId) {
                $q->where('rw_id', $rwId);
            });
        }

        $keluarga = $query->latest()->paginate(15)->withQueryString();

        $rtList = Rt::with('rw')->orderBy('rw_id')->orderBy('nomor')->get();
        $rwList = Rw::orderBy('nomor')->get();

        return view('admin.keluarga.index', compact('keluarga', 'rtList', 'rwList'));
    }

    public function create()
    {
        $pendudukList = Penduduk::orderBy('nama')->get();
        $rtList = Rt::with('rw')->orderBy('rw_id')->orderBy('nomor')->get();

        return view('admin.keluarga.create', compact('pendudukList', 'rtList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_kk' => ['required', 'string', 'max:20', 'unique:keluargas,no_kk'],
            'kepala_keluarga_id' => ['nullable', 'exists:penduduks,id'],
            'jumlah_anggota_keluarga' => ['nullable', 'integer', 'min:0'],
            'rt_id' => ['nullable', 'exists:rts,id'],
            'arsip_path' => ['nullable', 'string'],
        ]);

        $validated['petugas_input_id'] = auth()->id();
        $validated['tgl_input'] = now();

        Keluarga::create($validated);

        return redirect()->route('admin.keluarga.index')
            ->with('success', 'Data keluarga berhasil ditambahkan.');
    }

    public function show(Keluarga $keluarga)
    {
        $keluarga->load(['anggota', 'kepalaKeluarga', 'rt.rw']);
        return view('admin.keluarga.show', compact('keluarga'));
    }

    public function edit(Keluarga $keluarga)
    {
        $pendudukList = Penduduk::orderBy('nama')->get();
        $rtList = Rt::with('rw')->orderBy('rw_id')->orderBy('nomor')->get();

        return view('admin.keluarga.edit', compact('keluarga', 'pendudukList', 'rtList'));
    }

    public function update(Request $request, Keluarga $keluarga)
    {
        $validated = $request->validate([
            'no_kk' => ['required', 'string', 'max:20', "unique:keluargas,no_kk,{$keluarga->id}"],
            'kepala_keluarga_id' => ['nullable', 'exists:penduduks,id'],
            'jumlah_anggota_keluarga' => ['nullable', 'integer', 'min:0'],
            'rt_id' => ['nullable', 'exists:rts,id'],
            'arsip_path' => ['nullable', 'string'],
        ]);

        $keluarga->update($validated);

        return redirect()->route('admin.keluarga.index')
            ->with('success', 'Data keluarga berhasil diperbarui.');
    }

    public function destroy(Keluarga $keluarga)
    {
        $keluarga->delete();

        return redirect()->route('admin.keluarga.index')
            ->with('success', 'Data keluarga berhasil dihapus.');
    }
}
