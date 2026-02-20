<?php

namespace App\Http\Controllers\Kependudukan;

use App\Http\Controllers\Concerns\HasWilayahScope;
use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use App\Models\Penduduk;
use Illuminate\Http\Request;

class KeluargaController extends Controller
{
    use HasWilayahScope;

    public function index(Request $request)
    {
        $query = Keluarga::with(['kepalaKeluarga', 'rt.rw']);

        $this->applyWilayahScope($query);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('no_kk', 'like', "%{$search}%")
                  ->orWhereHas('kepalaKeluarga', function ($qk) use ($search) {
                      $qk->where('nama', 'like', "%{$search}%")
                          ->orWhere('nik', 'like', "%{$search}%");
                  });
            });
        }

        if (! $this->isRtRw()) {
            if ($rtId = $request->get('rt')) {
                $query->where('rt_id', $rtId);
            }

            if ($rwId = $request->get('rw')) {
                $query->whereHas('rt', fn ($q) => $q->where('rw_id', $rwId));
            }
        }

        $keluarga = $query->latest()->paginate(15)->withQueryString();

        $rtList = $this->wilayahRtList();
        $rwList = $this->wilayahRwList();

        return view('admin.keluarga.index', compact('keluarga', 'rtList', 'rwList'));
    }

    public function create()
    {
        $pendudukList = Penduduk::orderBy('nama')->get();
        $rtList       = $this->wilayahRtList();

        return view('admin.keluarga.create', compact('pendudukList', 'rtList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_kk'                   => ['required', 'string', 'max:20', 'unique:keluargas,no_kk'],
            'kepala_keluarga_id'      => ['nullable', 'exists:penduduks,id'],
            'jumlah_anggota_keluarga' => ['nullable', 'integer', 'min:0'],
            'rt_id'                   => $this->rtIdRules(),
            'arsip_path'              => ['nullable', 'string'],
        ]);

        $validated['petugas_input_id'] = auth()->id();
        $validated['tgl_input']        = now();

        Keluarga::create($validated);

        return redirect()->route('kependudukan.keluarga.index')
            ->with('success', 'Data keluarga berhasil ditambahkan.');
    }

    public function show(Keluarga $keluarga)
    {
        $this->authorizeWilayahByRtId($keluarga->rt_id);

        $keluarga->load(['anggota', 'kepalaKeluarga', 'rt.rw']);

        return view('admin.keluarga.show', compact('keluarga'));
    }

    public function edit(Keluarga $keluarga)
    {
        $this->authorizeWilayahByRtId($keluarga->rt_id);

        $pendudukList = Penduduk::orderBy('nama')->get();
        $rtList       = $this->wilayahRtList();

        return view('admin.keluarga.edit', compact('keluarga', 'pendudukList', 'rtList'));
    }

    public function update(Request $request, Keluarga $keluarga)
    {
        $this->authorizeWilayahByRtId($keluarga->rt_id);

        $validated = $request->validate([
            'no_kk'                   => ['required', 'string', 'max:20', "unique:keluargas,no_kk,{$keluarga->id}"],
            'kepala_keluarga_id'      => ['nullable', 'exists:penduduks,id'],
            'jumlah_anggota_keluarga' => ['nullable', 'integer', 'min:0'],
            'rt_id'                   => $this->rtIdRules(),
            'arsip_path'              => ['nullable', 'string'],
        ]);

        $keluarga->update($validated);

        return redirect()->route('kependudukan.keluarga.index')
            ->with('success', 'Data keluarga berhasil diperbarui.');
    }

    public function destroy(Keluarga $keluarga)
    {
        $this->authorizeWilayahByRtId($keluarga->rt_id);

        $keluarga->delete();

        return redirect()->route('kependudukan.keluarga.index')
            ->with('success', 'Data keluarga berhasil dihapus.');
    }
}
