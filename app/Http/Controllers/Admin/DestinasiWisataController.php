<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DestinasiWisata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DestinasiWisataController extends Controller
{
    public function index(Request $request)
    {
        $query = DestinasiWisata::ordered();

        if ($search = trim((string) $request->get('search'))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('nama', 'like', "%{$search}%")
                    ->orWhere('ringkasan', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        if ($kategori = $request->get('kategori')) {
            $query->where('kategori', $kategori);
        }

        if ($status = $request->get('status')) {
            $query->where('is_published', $status === 'published');
        }

        $destinasiWisata = $query->paginate(10)->withQueryString();

        return view('admin.website.destinasi-wisata.index', compact('destinasiWisata'));
    }

    public function create()
    {
        return view('admin.website.destinasi-wisata.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_published'] = $request->boolean('is_published', true);

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('website/wisata', 'public');
        }

        DestinasiWisata::create($validated);

        return redirect()->route('admin.website.destinasi-wisata.index')
            ->with('success', 'Destinasi wisata berhasil ditambahkan.');
    }

    public function edit(DestinasiWisata $destinasiWisata)
    {
        return view('admin.website.destinasi-wisata.edit', compact('destinasiWisata'));
    }

    public function update(Request $request, DestinasiWisata $destinasiWisata)
    {
        $validated = $this->validateData($request, $destinasiWisata);
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_published'] = $request->boolean('is_published', true);

        if ($request->hasFile('gambar')) {
            if ($destinasiWisata->gambar) {
                Storage::disk('public')->delete($destinasiWisata->gambar);
            }

            $validated['gambar'] = $request->file('gambar')->store('website/wisata', 'public');
        }

        $destinasiWisata->update($validated);

        return redirect()->route('admin.website.destinasi-wisata.index')
            ->with('success', 'Destinasi wisata berhasil diperbarui.');
    }

    public function destroy(DestinasiWisata $destinasiWisata)
    {
        if ($destinasiWisata->gambar) {
            Storage::disk('public')->delete($destinasiWisata->gambar);
        }

        $destinasiWisata->delete();

        return redirect()->route('admin.website.destinasi-wisata.index')
            ->with('success', 'Destinasi wisata berhasil dihapus.');
    }

    private function validateData(Request $request, ?DestinasiWisata $destinasiWisata = null): array
    {
        $uniqueSlug = Rule::unique('destinasi_wisatas', 'slug');

        if ($destinasiWisata) {
            $uniqueSlug->ignore($destinasiWisata->id);
        }

        return $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', $uniqueSlug],
            'kategori' => ['required', Rule::in(array_keys(DestinasiWisata::kategoriOptions()))],
            'ringkasan' => ['required', 'string'],
            'deskripsi' => ['nullable', 'string'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'jam_operasional' => ['nullable', 'string', 'max:255'],
            'harga_tiket' => ['nullable', 'string', 'max:255'],
            'kontak' => ['nullable', 'string', 'max:255'],
            'maps_url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
