<?php

namespace App\Http\Controllers\Ekspedisi;

use App\Http\Controllers\Controller;
use App\Models\Ekspedisi;
use App\Models\Kelurahan;
use Illuminate\Http\Request;

class EkspedisiController extends Controller
{
    public function index(Request $request)
    {
        $query = Ekspedisi::with('kelurahan');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('ekspedisi', 'like', "%{$search}%")
                    ->orWhere('pemilik_usaha', 'like', "%{$search}%")
                    ->orWhere('penanggung_jawab', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        $ekspedisiList = $query->latest()->paginate(15)->withQueryString();

        return view('ekspedisi.index', compact('ekspedisiList'));
    }

    public function create()
    {
        $kelurahanList = Kelurahan::orderBy('nama')->get();
        return view('ekspedisi.create', compact('kelurahanList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelurahan_id' => ['required', 'exists:kelurahans,id'],
            'pemilik_usaha' => ['required', 'string', 'max:255'],
            'ekspedisi' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'penanggung_jawab' => ['nullable', 'string', 'max:255'],
            'telp_hp' => ['nullable', 'string', 'max:20'],
            'kegiatan_ekspedisi' => ['nullable', 'string', 'max:500'],
        ]);

        Ekspedisi::create($validated);

        return redirect()->route('ekspedisi.index')
            ->with('success', 'Data ekspedisi berhasil ditambahkan.');
    }

    public function edit(Ekspedisi $ekspedisi)
    {
        $kelurahanList = Kelurahan::orderBy('nama')->get();
        return view('ekspedisi.edit', compact('ekspedisi', 'kelurahanList'));
    }

    public function update(Request $request, Ekspedisi $ekspedisi)
    {
        $validated = $request->validate([
            'kelurahan_id' => ['required', 'exists:kelurahans,id'],
            'pemilik_usaha' => ['required', 'string', 'max:255'],
            'ekspedisi' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'penanggung_jawab' => ['nullable', 'string', 'max:255'],
            'telp_hp' => ['nullable', 'string', 'max:20'],
            'kegiatan_ekspedisi' => ['nullable', 'string', 'max:500'],
        ]);

        $ekspedisi->update($validated);

        return redirect()->route('ekspedisi.index')
            ->with('success', 'Data ekspedisi berhasil diperbarui.');
    }

    public function destroy(Ekspedisi $ekspedisi)
    {
        $ekspedisi->delete();

        return redirect()->route('ekspedisi.index')
            ->with('success', 'Data ekspedisi berhasil dihapus.');
    }
}
