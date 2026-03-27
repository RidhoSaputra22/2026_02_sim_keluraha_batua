<?php

namespace App\Http\Controllers\Shared\DataUmum;

use App\Http\Controllers\Concerns\HasWilayahScope;
use App\Http\Controllers\Concerns\SyncsWithPetaLayer;
use App\Http\Controllers\Controller;
use App\Models\Kelurahan;
use App\Models\Kontrakan;
use App\Models\PetaLayer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class KontrakanController extends Controller
{
    use HasWilayahScope, SyncsWithPetaLayer;

    protected function petaLayerSlug(): string
    {
        return PetaLayer::LAYER_KONTRAKAN_KOST;
    }

    protected function petaPolygonNama(Model $model): string
    {
        return $model->nama ?? 'Kontrakan/Kost';
    }

    public function index(Request $request)
    {
        $query = Kontrakan::with(['kelurahan', 'rw', 'rt']);

        // Wilayah scoping for RT/RW users
        $this->applyWilayahScopeByRtOrRw($query);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%")
                    ->orWhere('pemilik', 'like', "%{$search}%");
            });
        }

        $kontrakanList = $query->orderBy('nama')->paginate(15)->withQueryString();

        return view('data-umum.kontrakan.index', compact('kontrakanList'));
    }

    public function create()
    {
        $kelurahanList = Kelurahan::orderBy('nama')->get();
        $rtList = $this->wilayahRtList();
        $rwList = $this->wilayahRwList();

        return view('data-umum.kontrakan.create', compact('kelurahanList', 'rtList', 'rwList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelurahan_id'        => ['required', 'exists:kelurahans,id'],
            'rt_id'               => $this->rtIdRules(),
            'rw_id'               => $this->rwIdRules(),
            'nama'                => ['nullable', 'string', 'max:255'],
            'alamat'              => ['nullable', 'string', 'max:500'],
            'pemilik'             => ['nullable', 'string', 'max:255'],
            'no_hp_pemilik'       => ['nullable', 'string', 'max:20'],
            'jenis_unit'          => ['nullable', 'string', 'in:Kontrakan,Kost Putera,Kost Putri,Kost Campur'],
            'jumlah_kamar'        => ['nullable', 'integer', 'min:0'],
            'keterangan'          => ['nullable', 'string', 'max:1000'],
            'latitude'            => ['nullable', 'numeric'],
            'longitude'           => ['nullable', 'numeric'],
        ]);

        $validated = $this->normalizeWilayahInput($validated);

        $kontrakan = Kontrakan::create($validated);

        $this->syncPetaPolygon($kontrakan, $validated['latitude'] ?? null, $validated['longitude'] ?? null);

        return redirect()->route('data-umum.kontrakan.index')
            ->with('success', 'Data kontrakan/kost berhasil ditambahkan.');
    }

    public function edit(Kontrakan $kontrakan)
    {
        $this->authorizeWilayahByRtOrRwId($kontrakan->rt_id, $kontrakan->rw_id);

        $kelurahanList = Kelurahan::orderBy('nama')->get();
        $rtList = $this->wilayahRtList();
        $rwList = $this->wilayahRwList();

        return view('data-umum.kontrakan.edit', compact('kontrakan', 'kelurahanList', 'rtList', 'rwList'));
    }

    public function update(Request $request, Kontrakan $kontrakan)
    {
        $this->authorizeWilayahByRtOrRwId($kontrakan->rt_id, $kontrakan->rw_id);

        $validated = $request->validate([
            'kelurahan_id'        => ['required', 'exists:kelurahans,id'],
            'rt_id'               => $this->rtIdRules(),
            'rw_id'               => $this->rwIdRules(),
            'nama'                => ['nullable', 'string', 'max:255'],
            'alamat'              => ['nullable', 'string', 'max:500'],
            'pemilik'             => ['nullable', 'string', 'max:255'],
            'no_hp_pemilik'       => ['nullable', 'string', 'max:20'],
            'jenis_unit'          => ['nullable', 'string', 'in:Kontrakan,Kost Putera,Kost Putri,Kost Campur'],
            'jumlah_kamar'        => ['nullable', 'integer', 'min:0'],
            'keterangan'          => ['nullable', 'string', 'max:1000'],
            'latitude'            => ['nullable', 'numeric'],
            'longitude'           => ['nullable', 'numeric'],
        ]);

        $validated = $this->normalizeWilayahInput($validated);

        $kontrakan->update($validated);

        $this->syncPetaPolygon($kontrakan, $validated['latitude'] ?? null, $validated['longitude'] ?? null);

        return redirect()->route('data-umum.kontrakan.index')
            ->with('success', 'Data kontrakan/kost berhasil diperbarui.');
    }

    public function destroy(Kontrakan $kontrakan)
    {
        $this->authorizeWilayahByRtOrRwId($kontrakan->rt_id, $kontrakan->rw_id);

        $this->removePetaPolygon($kontrakan);
        $kontrakan->delete();

        return redirect()->route('data-umum.kontrakan.index')
            ->with('success', 'Data kontrakan/kost berhasil dihapus.');
    }
}
