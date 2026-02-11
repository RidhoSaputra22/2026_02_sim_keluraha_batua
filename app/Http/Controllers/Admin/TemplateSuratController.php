<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TemplateSurat;
use App\Models\JenisSurat;
use Illuminate\Http\Request;

class TemplateSuratController extends Controller
{
    public function index(Request $request)
    {
        $query = TemplateSurat::with('jenisSurat');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhereHas('jenisSurat', function ($qjs) use ($search) {
                        $qjs->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        if ($jenisSuratId = $request->get('jenis_surat_id')) {
            $query->where('jenis_surat_id', $jenisSuratId);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->get('is_active'));
        }

        $templates = $query->latest()->paginate(15)->withQueryString();
        $jenisSuratList = JenisSurat::active()->orderBy('nama')->get();

        return view('admin.template-surat.index', compact('templates', 'jenisSuratList'));
    }

    public function create()
    {
        $jenisSuratList = JenisSurat::active()->orderBy('nama')->get();

        return view('admin.template-surat.create', compact('jenisSuratList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_surat_id' => ['required', 'exists:jenis_surat,id'],
            'nama' => ['required', 'string', 'max:255'],
            'isi_template' => ['required', 'string'],
            'field_mapping' => ['nullable', 'string'],
            'is_default' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        // Parse field_mapping jika dalam format string (JSON)
        if (!empty($validated['field_mapping'])) {
            $validated['field_mapping'] = json_decode($validated['field_mapping'], true) ?? [];
        }

        $validated['is_default'] = $request->has('is_default') ? true : false;
        $validated['is_active'] = $request->has('is_active') ? true : false;

        TemplateSurat::create($validated);

        return redirect()->route('admin.template-surat.index')
            ->with('success', 'Template surat berhasil ditambahkan.');
    }

    public function show(TemplateSurat $templateSurat)
    {
        $templateSurat->load('jenisSurat');

        return view('admin.template-surat.show', compact('templateSurat'));
    }

    public function edit(TemplateSurat $templateSurat)
    {
        $jenisSuratList = JenisSurat::active()->orderBy('nama')->get();

        return view('admin.template-surat.edit', compact('templateSurat', 'jenisSuratList'));
    }

    public function update(Request $request, TemplateSurat $templateSurat)
    {
        $validated = $request->validate([
            'jenis_surat_id' => ['required', 'exists:jenis_surat,id'],
            'nama' => ['required', 'string', 'max:255'],
            'isi_template' => ['required', 'string'],
            'field_mapping' => ['nullable', 'string'],
            'is_default' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        // Parse field_mapping jika dalam format string (JSON)
        if (!empty($validated['field_mapping'])) {
            $validated['field_mapping'] = json_decode($validated['field_mapping'], true) ?? [];
        }

        $validated['is_default'] = $request->has('is_default') ? true : false;
        $validated['is_active'] = $request->has('is_active') ? true : false;

        $templateSurat->update($validated);

        return redirect()->route('admin.template-surat.index')
            ->with('success', 'Template surat berhasil diperbarui.');
    }

    public function destroy(TemplateSurat $templateSurat)
    {
        $templateSurat->delete();

        return redirect()->route('admin.template-surat.index')
            ->with('success', 'Template surat berhasil dihapus.');
    }
}
