<?php

namespace App\Http\Controllers\Admin\Website;

use App\Http\Controllers\Controller;
use App\Models\DokumenPublik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DokumenPublikController extends Controller
{
    public function index(Request $request)
    {
        $query = DokumenPublik::query()->latest('published_at')->latest('id');

        if ($search = trim((string) $request->get('search'))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('judul', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        if ($kategori = $request->get('kategori')) {
            $query->where('kategori', $kategori);
        }

        if ($status = $request->get('status')) {
            $query->where('is_published', $status === 'published');
        }

        $dokumenPublik = $query->paginate(10)->withQueryString();

        return view('admin.website.dokumen-publik.index', compact('dokumenPublik'));
    }

    public function create()
    {
        return view('admin.website.dokumen-publik.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);
        $validated['author_id'] = auth()->id();
        $validated['is_published'] = $request->boolean('is_published', true);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('website/dokumen/cover', 'public');
        }

        $file = $request->file('file_dokumen');
        $validated['file_path'] = $file->store('website/dokumen/file', 'public');
        $validated['mime_type'] = $file->getClientMimeType();
        $validated['file_size'] = $file->getSize();

        DokumenPublik::create($validated);

        return redirect()->route('admin.website.dokumen-publik.index')
            ->with('success', 'Dokumen publik berhasil ditambahkan.');
    }

    public function edit(DokumenPublik $dokumenPublik)
    {
        return view('admin.website.dokumen-publik.edit', compact('dokumenPublik'));
    }

    public function update(Request $request, DokumenPublik $dokumenPublik)
    {
        $validated = $this->validateData($request, $dokumenPublik);
        $validated['is_published'] = $request->boolean('is_published', true);

        if ($request->hasFile('cover_image')) {
            if ($dokumenPublik->cover_image) {
                Storage::disk('public')->delete($dokumenPublik->cover_image);
            }

            $validated['cover_image'] = $request->file('cover_image')->store('website/dokumen/cover', 'public');
        }

        if ($request->hasFile('file_dokumen')) {
            if ($dokumenPublik->file_path) {
                Storage::disk('public')->delete($dokumenPublik->file_path);
            }

            $file = $request->file('file_dokumen');
            $validated['file_path'] = $file->store('website/dokumen/file', 'public');
            $validated['mime_type'] = $file->getClientMimeType();
            $validated['file_size'] = $file->getSize();
        }

        $dokumenPublik->update($validated);

        return redirect()->route('admin.website.dokumen-publik.index')
            ->with('success', 'Dokumen publik berhasil diperbarui.');
    }

    public function destroy(DokumenPublik $dokumenPublik)
    {
        if ($dokumenPublik->cover_image) {
            Storage::disk('public')->delete($dokumenPublik->cover_image);
        }

        if ($dokumenPublik->file_path) {
            Storage::disk('public')->delete($dokumenPublik->file_path);
        }

        $dokumenPublik->delete();

        return redirect()->route('admin.website.dokumen-publik.index')
            ->with('success', 'Dokumen publik berhasil dihapus.');
    }

    private function validateData(Request $request, ?DokumenPublik $dokumenPublik = null): array
    {
        $uniqueSlug = Rule::unique('dokumen_publiks', 'slug');

        if ($dokumenPublik) {
            $uniqueSlug->ignore($dokumenPublik->id);
        }

        $fileRules = ['file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,webp'];

        return $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', $uniqueSlug],
            'kategori' => ['required', Rule::in(array_keys(DokumenPublik::kategoriOptions()))],
            'deskripsi' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'file_dokumen' => array_merge([$dokumenPublik ? 'nullable' : 'required'], $fileRules),
            'published_at' => ['nullable', 'date'],
        ]);
    }
}
