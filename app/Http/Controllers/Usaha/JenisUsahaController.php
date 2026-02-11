<?php

namespace App\Http\Controllers\Usaha;

use App\Http\Controllers\Controller;
use App\Models\JenisUsaha;
use Illuminate\Http\Request;

class JenisUsahaController extends Controller
{
    public function index(Request $request)
    {
        $query = JenisUsaha::withCount('umkms');

        if ($search = $request->get('search')) {
            $query->where('nama', 'like', "%{$search}%");
        }

        $jenisList = $query->orderBy('nama')->paginate(15)->withQueryString();

        return view('usaha.jenis.index', compact('jenisList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:jenis_usaha,nama'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ]);

        JenisUsaha::create($validated);

        return redirect()->route('usaha.jenis.index')
            ->with('success', 'Jenis usaha berhasil ditambahkan.');
    }

    public function update(Request $request, JenisUsaha $jenisUsaha)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:jenis_usaha,nama,' . $jenisUsaha->id],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ]);

        $jenisUsaha->update($validated);

        return redirect()->route('usaha.jenis.index')
            ->with('success', 'Jenis usaha berhasil diperbarui.');
    }

    public function destroy(JenisUsaha $jenisUsaha)
    {
        if ($jenisUsaha->umkms()->count() > 0) {
            return redirect()->route('usaha.jenis.index')
                ->with('error', 'Jenis usaha tidak dapat dihapus karena masih digunakan.');
        }

        $jenisUsaha->delete();

        return redirect()->route('usaha.jenis.index')
            ->with('success', 'Jenis usaha berhasil dihapus.');
    }
}
