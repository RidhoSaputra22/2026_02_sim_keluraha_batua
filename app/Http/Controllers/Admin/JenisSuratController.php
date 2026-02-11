<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuratJenis;
use Illuminate\Http\Request;

class JenisSuratController extends Controller
{
    public function index(Request $request)
    {
        $query = SuratJenis::withCount('surats');

        if ($search = $request->get('search')) {
            $query->where('nama', 'like', "%{$search}%");
        }

        $jenisSurat = $query->orderBy('nama')->paginate(15)->withQueryString();

        return view('admin.jenis-surat.index', compact('jenisSurat'));
    }

    public function create()
    {
        return view('admin.jenis-surat.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:surat_jenis,nama'],
        ]);

        SuratJenis::create($validated);

        return redirect()->route('admin.jenis-surat.index')
            ->with('success', 'Jenis surat berhasil ditambahkan.');
    }

    public function show(SuratJenis $jenisSurat)
    {
        $jenisSurat->loadCount('surats');

        return view('admin.jenis-surat.show', compact('jenisSurat'));
    }

    public function edit(SuratJenis $jenisSurat)
    {
        return view('admin.jenis-surat.edit', compact('jenisSurat'));
    }

    public function update(Request $request, SuratJenis $jenisSurat)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', "unique:surat_jenis,nama,{$jenisSurat->id}"],
        ]);

        $jenisSurat->update($validated);

        return redirect()->route('admin.jenis-surat.index')
            ->with('success', 'Jenis surat berhasil diperbarui.');
    }

    public function destroy(SuratJenis $jenisSurat)
    {
        $jenisSurat->delete();

        return redirect()->route('admin.jenis-surat.index')
            ->with('success', 'Jenis surat berhasil dihapus.');
    }
}
