<?php

namespace App\Http\Controllers\DataUmum;

use App\Http\Controllers\Concerns\HasWilayahScope;
use App\Http\Controllers\Concerns\SyncsWithPetaLayer;
use App\Http\Controllers\Controller;
use App\Models\Faskes;
use App\Models\Kelurahan;
use App\Models\PetaLayer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class FaskesController extends Controller
{
    use HasWilayahScope, SyncsWithPetaLayer;

    protected function petaLayerSlug(): string
    {
        return PetaLayer::LAYER_FASKES;
    }

    protected function petaPolygonNama(Model $model): string
    {
        return $model->nama_rs;
    }

    public function index(Request $request)
    {
        $query = Faskes::with(['kelurahan', 'rw']);

        $this->applyWilayahScopeByRw($query);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_rs', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%")
                    ->orWhere('jenis', 'like', "%{$search}%");
            });
        }

        if ($jenis = $request->get('jenis')) {
            $query->where('jenis', $jenis);
        }

        $faskesList = $query->orderBy('created_at')->paginate(15)->withQueryString();
        $jenisList = Faskes::distinct()->whereNotNull('jenis')->pluck('jenis');


        return view('data-umum.faskes.index', compact('faskesList', 'jenisList'));
    }

    public function create()
    {
        $kelurahanList = Kelurahan::orderBy('nama')->get();
        $rwList        = $this->wilayahRwList();

        return view('data-umum.faskes.create', compact('kelurahanList', 'rwList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelurahan_id'    => ['required', 'exists:kelurahans,id'],
            'nama_rs'         => ['required', 'string', 'max:255'],
            'alamat'          => ['nullable', 'string', 'max:500'],
            'rw_id'           => ['nullable', 'exists:rws,id'],
            'jenis'           => ['nullable', 'string', 'max:100'],
            'kelas'           => ['nullable', 'string', 'max:50'],
            'jenis_pelayanan' => ['nullable', 'string', 'max:255'],
            'akreditasi'      => ['nullable', 'string', 'max:100'],
            'telp'            => ['nullable', 'string', 'max:20'],
            'latitude'        => ['nullable', 'numeric'],
            'longitude'       => ['nullable', 'numeric'],
        ]);

        $faskes = Faskes::create($validated);

        $this->syncPetaPolygon($faskes, $validated['latitude'] ?? null, $validated['longitude'] ?? null);

        return redirect()->route('data-umum.faskes.index')
            ->with('success', 'Data fasilitas kesehatan berhasil ditambahkan.');
    }

    public function edit(Faskes $faske)
    {
        $this->authorizeWilayahByRwId($faske->rw_id);

        $kelurahanList = Kelurahan::orderBy('nama')->get();
        $rwList        = $this->wilayahRwList();

        // dd($faske, $kelurahanList, $rwList);


        return view('data-umum.faskes.edit', compact('faske', 'kelurahanList', 'rwList'));
    }

    public function update(Request $request, Faskes $faske)
    {
        $this->authorizeWilayahByRwId($faske->rw_id);

        $validated = $request->validate([
            'kelurahan_id'    => ['required', 'exists:kelurahans,id'],
            'nama_rs'         => ['required', 'string', 'max:255'],
            'alamat'          => ['nullable', 'string', 'max:500'],
            'rw_id'           => ['nullable', 'exists:rws,id'],
            'jenis'           => ['nullable', 'string', 'max:100'],
            'kelas'           => ['nullable', 'string', 'max:50'],
            'jenis_pelayanan' => ['nullable', 'string', 'max:255'],
            'akreditasi'      => ['nullable', 'string', 'max:100'],
            'telp'            => ['nullable', 'string', 'max:20'],
            'latitude'        => ['required', 'numeric'],
            'longitude'       => ['required', 'numeric'],
        ]);

        $faske->update($validated);

        $this->syncPetaPolygon($faske, $validated['latitude'] ?? null, $validated['longitude'] ?? null);

        return redirect()->route('data-umum.faskes.index')
            ->with('success', 'Data fasilitas kesehatan berhasil diperbarui.');
    }

    public function destroy(Faskes $faske)
    {
        $this->authorizeWilayahByRwId($faske->rw_id);

        $this->removePetaPolygon($faske);
        $faske->delete();

        return redirect()->route('data-umum.faskes.index')
            ->with('success', 'Data fasilitas kesehatan berhasil dihapus.');
    }
}
