<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisSurat;
use Illuminate\Http\Request;

class JenisSuratController extends Controller
{
    public function index(Request $request)
    {
        $query = JenisSurat::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%")
                    ->orWhere('format_nomor', 'like', "%{$search}%");
            });
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->get('is_active'));
        }

        $jenisSurat = $query->latest()->paginate(15)->withQueryString();

        return view('admin.jenis-surat.index', compact('jenisSurat'));
    }

    public function create()
    {
        return view('admin.jenis-surat.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:20', 'unique:jenis_surat,kode'],
            'nama' => ['required', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
            'format_nomor' => ['nullable', 'string', 'max:100'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        JenisSurat::create($validated);

        return redirect()->route('admin.jenis-surat.index')
            ->with('success', 'Jenis surat berhasil ditambahkan.');
    }

    public function show(JenisSurat $jenisSurat)
    {
        $jenisSurat->load('templateSurat');

        return view('admin.jenis-surat.show', compact('jenisSurat'));
    }

    public function edit(JenisSurat $jenisSurat)
    {
        return view('admin.jenis-surat.edit', compact('jenisSurat'));
    }

    public function update(Request $request, JenisSurat $jenisSurat)
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:20', "unique:jenis_surat,kode,{$jenisSurat->id}"],
            'nama' => ['required', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
            'format_nomor' => ['nullable', 'string', 'max:100'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $jenisSurat->update($validated);

        return redirect()->route('admin.jenis-surat.index')
            ->with('success', 'Jenis surat berhasil diperbarui.');
    }

    public function destroy(JenisSurat $jenisSurat)
    {
        $jenisSurat->delete();

        return redirect()->route('admin.jenis-surat.index')
            ->with('success', 'Jenis surat berhasil dihapus.');
    }
}
