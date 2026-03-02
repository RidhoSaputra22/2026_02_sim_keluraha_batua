<?php

namespace App\Http\Controllers\DataUmum;

use App\Http\Controllers\Concerns\HasWilayahScope;
use App\Http\Controllers\Controller;
use App\Models\Kelurahan;
use App\Models\Kontrakan;
use Illuminate\Http\Request;

class KontrakanController extends Controller
{
    use HasWilayahScope;

    public function index(Request $request)
    {
        $query = Kontrakan::with(['kelurahan', 'rw', 'rt']);

        // Wilayah scoping for RT/RW users
        $this->applyWilayahScope($query);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%")
                    ->orWhere('pemilik', 'like', "%{$search}%");
            });
        }

        $kontrakanList = $query->latest()->paginate(15)->withQueryString();

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
            'rw_id'               => ['nullable', 'exists:rws,id'],
            'nama'                => ['nullable', 'string', 'max:255'],
            'alamat'              => ['nullable', 'string', 'max:500'],
            'pemilik'             => ['nullable', 'string', 'max:255'],
            'jumlah_kontrakan'    => ['nullable', 'integer', 'min:0'],
            'jumlah_kost_putera'  => ['nullable', 'integer', 'min:0'],
            'jumlah_kost_putri'   => ['nullable', 'integer', 'min:0'],
            'jumlah_kost_campur'  => ['nullable', 'integer', 'min:0'],
            'keterangan'          => ['nullable', 'string', 'max:1000'],
        ]);

        Kontrakan::create($validated);

        return redirect()->route('data-umum.kontrakan.index')
            ->with('success', 'Data kontrakan/kost berhasil ditambahkan.');
    }

    public function edit(Kontrakan $kontrakan)
    {
        $this->authorizeWilayahByRtId($kontrakan->rt_id);

        $kelurahanList = Kelurahan::orderBy('nama')->get();
        $rtList = $this->wilayahRtList();
        $rwList = $this->wilayahRwList();

        return view('data-umum.kontrakan.edit', compact('kontrakan', 'kelurahanList', 'rtList', 'rwList'));
    }

    public function update(Request $request, Kontrakan $kontrakan)
    {
        $this->authorizeWilayahByRtId($kontrakan->rt_id);

        $validated = $request->validate([
            'kelurahan_id'        => ['required', 'exists:kelurahans,id'],
            'rt_id'               => $this->rtIdRules(),
            'rw_id'               => ['nullable', 'exists:rws,id'],
            'nama'                => ['nullable', 'string', 'max:255'],
            'alamat'              => ['nullable', 'string', 'max:500'],
            'pemilik'             => ['nullable', 'string', 'max:255'],
            'jumlah_kontrakan'    => ['nullable', 'integer', 'min:0'],
            'jumlah_kost_putera'  => ['nullable', 'integer', 'min:0'],
            'jumlah_kost_putri'   => ['nullable', 'integer', 'min:0'],
            'jumlah_kost_campur'  => ['nullable', 'integer', 'min:0'],
            'keterangan'          => ['nullable', 'string', 'max:1000'],
        ]);

        $kontrakan->update($validated);

        return redirect()->route('data-umum.kontrakan.index')
            ->with('success', 'Data kontrakan/kost berhasil diperbarui.');
    }

    public function destroy(Kontrakan $kontrakan)
    {
        $this->authorizeWilayahByRtId($kontrakan->rt_id);

        $kontrakan->delete();

        return redirect()->route('data-umum.kontrakan.index')
            ->with('success', 'Data kontrakan/kost berhasil dihapus.');
    }
}
