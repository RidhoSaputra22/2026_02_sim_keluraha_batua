<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataKeluarga;
use App\Models\DataPenduduk;
use Illuminate\Http\Request;

class KeluargaController extends Controller
{
    public function index(Request $request)
    {
        $query = DataKeluarga::with('kepalaKeluarga');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('no_kk', 'like', "%{$search}%")
                  ->orWhere('nama_kepala_keluarga', 'like', "%{$search}%")
                  ->orWhere('nik_kepala_keluarga', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        if ($rt = $request->get('rt')) {
            $query->where('rt', $rt);
        }

        if ($rw = $request->get('rw')) {
            $query->where('rw', $rw);
        }

        $keluarga = $query->latest()->paginate(15)->withQueryString();

        $rtList = DataKeluarga::select('rt')->distinct()->whereNotNull('rt')->orderBy('rt')->pluck('rt');
        $rwList = DataKeluarga::select('rw')->distinct()->whereNotNull('rw')->orderBy('rw')->pluck('rw');

        return view('admin.keluarga.index', compact('keluarga', 'rtList', 'rwList'));
    }

    public function create()
    {
        return view('admin.keluarga.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_kk'                   => ['required', 'string', 'max:20', 'unique:data_keluarga,no_kk'],
            'nama_kepala_keluarga'    => ['required', 'string', 'max:255'],
            'nik_kepala_keluarga'     => ['nullable', 'string', 'max:20'],
            'jumlah_anggota_keluarga' => ['nullable', 'integer', 'min:1'],
            'alamat'                  => ['nullable', 'string'],
            'rt'                      => ['nullable', 'string', 'max:5'],
            'rw'                      => ['nullable', 'string', 'max:5'],
            'kecamatan'               => ['nullable', 'string', 'max:100'],
            'kelurahan'               => ['nullable', 'string', 'max:100'],
            'status'                  => ['nullable', 'string', 'max:20'],
        ]);

        $validated['petugas_input'] = auth()->id();
        $validated['tgl_input'] = now();

        DataKeluarga::create($validated);

        return redirect()->route('admin.keluarga.index')
            ->with('success', 'Data keluarga berhasil ditambahkan.');
    }

    public function show(DataKeluarga $keluarga)
    {
        $keluarga->load('penduduk', 'kepalaKeluarga');
        return view('admin.keluarga.show', compact('keluarga'));
    }

    public function edit(DataKeluarga $keluarga)
    {
        return view('admin.keluarga.edit', compact('keluarga'));
    }

    public function update(Request $request, DataKeluarga $keluarga)
    {
        $validated = $request->validate([
            'no_kk'                   => ['required', 'string', 'max:20', "unique:data_keluarga,no_kk,{$keluarga->id}"],
            'nama_kepala_keluarga'    => ['required', 'string', 'max:255'],
            'nik_kepala_keluarga'     => ['nullable', 'string', 'max:20'],
            'jumlah_anggota_keluarga' => ['nullable', 'integer', 'min:1'],
            'alamat'                  => ['nullable', 'string'],
            'rt'                      => ['nullable', 'string', 'max:5'],
            'rw'                      => ['nullable', 'string', 'max:5'],
            'kecamatan'               => ['nullable', 'string', 'max:100'],
            'kelurahan'               => ['nullable', 'string', 'max:100'],
            'status'                  => ['nullable', 'string', 'max:20'],
        ]);

        $keluarga->update($validated);

        return redirect()->route('admin.keluarga.index')
            ->with('success', 'Data keluarga berhasil diperbarui.');
    }

    public function destroy(DataKeluarga $keluarga)
    {
        $keluarga->delete();

        return redirect()->route('admin.keluarga.index')
            ->with('success', 'Data keluarga berhasil dihapus.');
    }
}
