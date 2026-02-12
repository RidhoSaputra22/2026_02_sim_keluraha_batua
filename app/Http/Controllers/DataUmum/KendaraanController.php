<?php

namespace App\Http\Controllers\DataUmum;

use App\Http\Controllers\Controller;
use App\Models\Kelurahan;
use App\Models\Kendaraan;
use Illuminate\Http\Request;

class KendaraanController extends Controller
{
    public function index(Request $request)
    {
        $query = Kendaraan::with('kelurahan');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_pengemudi', 'like', "%{$search}%")
                    ->orWhere('no_polisi', 'like', "%{$search}%")
                    ->orWhere('jenis_barang', 'like', "%{$search}%")
                    ->orWhere('merek_type', 'like', "%{$search}%");
            });
        }

        $kendaraanList = $query->latest()->paginate(15)->withQueryString();

        return view('data-umum.kendaraan.index', compact('kendaraanList'));
    }

    public function create()
    {
        $kelurahanList = Kelurahan::orderBy('nama')->get();
        return view('data-umum.kendaraan.create', compact('kelurahanList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelurahan_id' => ['required', 'exists:kelurahans,id'],
            'jenis_barang' => ['required', 'string', 'max:255'],
            'nama_pengemudi' => ['nullable', 'string', 'max:255'],
            'no_polisi' => ['nullable', 'string', 'max:20'],
            'no_rangka' => ['nullable', 'string', 'max:50'],
            'no_mesin' => ['nullable', 'string', 'max:50'],
            'tahun_perolehan' => ['nullable', 'string', 'max:10'],
            'merek_type' => ['nullable', 'string', 'max:100'],
        ]);

        Kendaraan::create($validated);

        return redirect()->route('data-umum.kendaraan.index')
            ->with('success', 'Data kendaraan berhasil ditambahkan.');
    }

    public function edit(Kendaraan $kendaraan)
    {
        $kelurahanList = Kelurahan::orderBy('nama')->get();
        return view('data-umum.kendaraan.edit', compact('kendaraan', 'kelurahanList'));
    }

    public function update(Request $request, Kendaraan $kendaraan)
    {
        $validated = $request->validate([
            'kelurahan_id' => ['required', 'exists:kelurahans,id'],
            'jenis_barang' => ['required', 'string', 'max:255'],
            'nama_pengemudi' => ['nullable', 'string', 'max:255'],
            'no_polisi' => ['nullable', 'string', 'max:20'],
            'no_rangka' => ['nullable', 'string', 'max:50'],
            'no_mesin' => ['nullable', 'string', 'max:50'],
            'tahun_perolehan' => ['nullable', 'string', 'max:10'],
            'merek_type' => ['nullable', 'string', 'max:100'],
        ]);

        $kendaraan->update($validated);

        return redirect()->route('data-umum.kendaraan.index')
            ->with('success', 'Data kendaraan berhasil diperbarui.');
    }

    public function destroy(Kendaraan $kendaraan)
    {
        $kendaraan->delete();

        return redirect()->route('data-umum.kendaraan.index')
            ->with('success', 'Data kendaraan berhasil dihapus.');
    }
}
