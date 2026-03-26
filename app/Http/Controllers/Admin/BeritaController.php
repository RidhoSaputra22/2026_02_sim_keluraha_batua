<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BeritaController extends Controller
{
    public function index(Request $request)
    {
        $query = Berita::query()->latest('published_at')->latest('id');

        if ($search = trim((string) $request->get('search'))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('judul', 'like', "%{$search}%")
                    ->orWhere('ringkasan', 'like', "%{$search}%")
                    ->orWhere('isi', 'like', "%{$search}%");
            });
        }

        if ($kategori = $request->get('kategori')) {
            $query->where('kategori', $kategori);
        }

        if ($status = $request->get('status')) {
            $query->where('is_published', $status === 'published');
        }

        $berita = $query->paginate(10)->withQueryString();

        return view('admin.website.berita.index', compact('berita'));
    }

    public function create()
    {
        return view('admin.website.berita.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);
        $validated['author_id'] = auth()->id();
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_published'] = $request->boolean('is_published', true);

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('website/berita', 'public');
        }

        Berita::create($validated);

        return redirect()->route('admin.website.berita.index')
            ->with('success', 'Berita berhasil ditambahkan.');
    }

    public function edit(Berita $berita)
    {
        return view('admin.website.berita.edit', compact('berita'));
    }

    public function update(Request $request, Berita $berita)
    {
        $validated = $this->validateData($request, $berita);
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_published'] = $request->boolean('is_published', true);

        if ($request->hasFile('gambar')) {
            if ($berita->gambar) {
                Storage::disk('public')->delete($berita->gambar);
            }

            $validated['gambar'] = $request->file('gambar')->store('website/berita', 'public');
        }

        $berita->update($validated);

        return redirect()->route('admin.website.berita.index')
            ->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(Berita $berita)
    {
        if ($berita->gambar) {
            Storage::disk('public')->delete($berita->gambar);
        }

        $berita->delete();

        return redirect()->route('admin.website.berita.index')
            ->with('success', 'Berita berhasil dihapus.');
    }

    private function validateData(Request $request, ?Berita $berita = null): array
    {
        $uniqueSlug = Rule::unique('beritas', 'slug');

        if ($berita) {
            $uniqueSlug->ignore($berita->id);
        }

        return $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', $uniqueSlug],
            'kategori' => ['required', Rule::in(array_keys(Berita::kategoriOptions()))],
            'ringkasan' => ['nullable', 'string'],
            'isi' => ['required', 'string'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'published_at' => ['nullable', 'date'],
        ]);
    }
}
