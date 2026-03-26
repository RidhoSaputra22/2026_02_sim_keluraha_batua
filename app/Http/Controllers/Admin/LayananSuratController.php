<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LayananSurat;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LayananSuratController extends Controller
{
    public function index(Request $request)
    {
        $query = LayananSurat::withCount('persyaratans')->ordered();

        if ($search = trim((string) $request->get('search'))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('nama', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhere('biaya', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('is_active', $status === 'active');
        }

        $layananSurat = $query->paginate(10)->withQueryString();

        return view('admin.website.layanan-surat.index', compact('layananSurat'));
    }

    public function create()
    {
        return view('admin.website.layanan-surat.create');
    }

    public function store(Request $request)
    {
        [$validated, $persyaratan] = $this->validateAndNormalize($request);
        $validated['is_active'] = $request->boolean('is_active', true);

        $layananSurat = LayananSurat::create($validated);
        $layananSurat->persyaratans()->createMany($persyaratan);

        return redirect()->route('admin.website.layanan-surat.index')
            ->with('success', 'Layanan surat berhasil ditambahkan.');
    }

    public function edit(LayananSurat $layananSurat)
    {
        $layananSurat->load('persyaratans');

        return view('admin.website.layanan-surat.edit', compact('layananSurat'));
    }

    public function update(Request $request, LayananSurat $layananSurat)
    {
        [$validated, $persyaratan] = $this->validateAndNormalize($request, $layananSurat);
        $validated['is_active'] = $request->boolean('is_active', true);

        $layananSurat->update($validated);
        $layananSurat->persyaratans()->delete();
        $layananSurat->persyaratans()->createMany($persyaratan);

        return redirect()->route('admin.website.layanan-surat.index')
            ->with('success', 'Layanan surat berhasil diperbarui.');
    }

    public function destroy(LayananSurat $layananSurat)
    {
        $layananSurat->delete();

        return redirect()->route('admin.website.layanan-surat.index')
            ->with('success', 'Layanan surat berhasil dihapus.');
    }

    private function validateAndNormalize(Request $request, ?LayananSurat $layananSurat = null): array
    {
        $uniqueSlug = Rule::unique('layanan_surats', 'slug');

        if ($layananSurat) {
            $uniqueSlug->ignore($layananSurat->id);
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', $uniqueSlug],
            'deskripsi' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:50'],
            'estimasi_layanan' => ['nullable', 'string', 'max:100'],
            'biaya' => ['nullable', 'string', 'max:100'],
            'catatan' => ['nullable', 'string'],
            'kontak_petugas' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'persyaratan' => ['required', 'array', 'min:1'],
            'persyaratan.*.nama' => ['nullable', 'string'],
            'persyaratan.*.keterangan' => ['nullable', 'string', 'max:255'],
            'persyaratan.*.is_required' => ['nullable', 'boolean'],
        ]);

        $persyaratan = collect($validated['persyaratan'] ?? [])
            ->map(function (array $item, int $index) {
                return [
                    'nama' => trim((string) ($item['nama'] ?? '')),
                    'keterangan' => blank($item['keterangan'] ?? null) ? null : $item['keterangan'],
                    'is_required' => filter_var($item['is_required'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'sort_order' => ($index + 1) * 10,
                ];
            })
            ->filter(fn (array $item) => $item['nama'] !== '')
            ->values()
            ->all();

        if (count($persyaratan) === 0) {
            return back()
                ->withErrors(['persyaratan' => 'Minimal satu persyaratan harus diisi.'])
                ->withInput()
                ->throwResponse();
        }

        unset($validated['persyaratan']);

        return [$validated, $persyaratan];
    }
}
