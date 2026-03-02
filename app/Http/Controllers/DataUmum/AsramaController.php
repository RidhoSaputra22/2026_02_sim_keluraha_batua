<?php

namespace App\Http\Controllers\DataUmum;

use App\Http\Controllers\Concerns\HasWilayahScope;
use App\Http\Controllers\Controller;
use App\Models\Asrama;
use App\Models\Kelurahan;
use Illuminate\Http\Request;

class AsramaController extends Controller
{
    use HasWilayahScope;

    public function index(Request $request)
    {
        $query = Asrama::with(['kelurahan', 'rw', 'rt']);

        // Wilayah scoping for RT/RW users
        $this->applyWilayahScope($query);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%")
                    ->orWhere('jenis', 'like', "%{$search}%");
            });
        }

        if ($jenis = $request->get('jenis')) {
            $query->where('jenis', $jenis);
        }

        $asramaList = $query->latest()->paginate(15)->withQueryString();
        $jenisOptions = Asrama::jenisOptions();
        $summaryData = Asrama::selectRaw('jenis, SUM(jumlah) as total')->groupBy('jenis')->pluck('total', 'jenis');

        return view('data-umum.asrama.index', compact('asramaList', 'jenisOptions', 'summaryData'));
    }

    public function create()
    {
        $kelurahanList = Kelurahan::orderBy('nama')->get();
        $rtList = $this->wilayahRtList();
        $rwList = $this->wilayahRwList();
        $jenisOptions = Asrama::jenisOptions();

        return view('data-umum.asrama.create', compact('kelurahanList', 'rtList', 'rwList', 'jenisOptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelurahan_id' => ['required', 'exists:kelurahans,id'],
            'rt_id'        => $this->rtIdRules(),
            'rw_id'        => ['nullable', 'exists:rws,id'],
            'nama'         => ['nullable', 'string', 'max:255'],
            'alamat'       => ['nullable', 'string', 'max:500'],
            'jenis'        => ['required', 'string', 'in:TNI,POLRI,Mahasiswa,Kerukunan'],
            'jumlah'       => ['nullable', 'integer', 'min:0'],
            'keterangan'   => ['nullable', 'string', 'max:1000'],
        ]);

        Asrama::create($validated);

        return redirect()->route('data-umum.asrama.index')
            ->with('success', 'Data asrama berhasil ditambahkan.');
    }

    public function edit(Asrama $asrama)
    {
        $this->authorizeWilayahByRtId($asrama->rt_id);

        $kelurahanList = Kelurahan::orderBy('nama')->get();
        $rtList = $this->wilayahRtList();
        $rwList = $this->wilayahRwList();
        $jenisOptions = Asrama::jenisOptions();

        return view('data-umum.asrama.edit', compact('asrama', 'kelurahanList', 'rtList', 'rwList', 'jenisOptions'));
    }

    public function update(Request $request, Asrama $asrama)
    {
        $this->authorizeWilayahByRtId($asrama->rt_id);

        $validated = $request->validate([
            'kelurahan_id' => ['required', 'exists:kelurahans,id'],
            'rt_id'        => $this->rtIdRules(),
            'rw_id'        => ['nullable', 'exists:rws,id'],
            'nama'         => ['nullable', 'string', 'max:255'],
            'alamat'       => ['nullable', 'string', 'max:500'],
            'jenis'        => ['required', 'string', 'in:TNI,POLRI,Mahasiswa,Kerukunan'],
            'jumlah'       => ['nullable', 'integer', 'min:0'],
            'keterangan'   => ['nullable', 'string', 'max:1000'],
        ]);

        $asrama->update($validated);

        return redirect()->route('data-umum.asrama.index')
            ->with('success', 'Data asrama berhasil diperbarui.');
    }

    public function destroy(Asrama $asrama)
    {
        $this->authorizeWilayahByRtId($asrama->rt_id);

        $asrama->delete();

        return redirect()->route('data-umum.asrama.index')
            ->with('success', 'Data asrama berhasil dihapus.');
    }
}
