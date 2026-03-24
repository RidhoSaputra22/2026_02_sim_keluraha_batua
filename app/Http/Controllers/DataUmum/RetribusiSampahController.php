<?php

namespace App\Http\Controllers\DataUmum;

use App\Http\Controllers\Concerns\HasWilayahScope;
use App\Http\Controllers\Controller;
use App\Models\Kelurahan;
use App\Models\RetribusiSampah;
use Illuminate\Http\Request;

class RetribusiSampahController extends Controller
{
    use HasWilayahScope;

    public function index(Request $request)
    {
        $query = RetribusiSampah::with(['kelurahan', 'rw', 'rt']);

        $this->applyWilayahScopeByRtOrRw($query);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_nasabah', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%")
                    ->orWhere('npwr', 'like', "%{$search}%")
                    ->orWhere('no_skrd', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($tahun = $request->get('tahun')) {
            $query->where('tahun', $tahun);
        }

        $summaryQuery = clone $query;
        $retribusiList = (clone $query)->latest()->paginate(15)->withQueryString();

        // Summary stats
        $totalNasabah = (clone $summaryQuery)->count();
        $totalBeban = (clone $summaryQuery)->sum('beban');
        $totalLunas = (clone $summaryQuery)->where('status', 'Lunas')->sum('beban');
        $totalBelum = (clone $summaryQuery)->where('status', 'Belum')->sum('beban');
        $tahunList = RetribusiSampah::distinct()->whereNotNull('tahun')->orderByDesc('tahun')->pluck('tahun');

        return view('data-umum.retribusi-sampah.index', compact(
            'retribusiList', 'totalBeban', 'totalLunas', 'totalBelum', 'totalNasabah', 'tahunList'
        ));
    }

    public function create()
    {
        $kelurahanList = Kelurahan::orderBy('nama')->get();
        $rtList = $this->wilayahRtList();
        $rwList = $this->wilayahRwList();

        return view('data-umum.retribusi-sampah.create', compact('kelurahanList', 'rtList', 'rwList'));
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'kelurahan_id' => ['required', 'exists:kelurahans,id'],
            'rt_id'        => $this->rtIdRules(required: true),
            'rw_id'        => $this->rwIdRules(required: true),
            'nama_nasabah' => ['required', 'string', 'max:255'],
            'npwr'         => ['nullable', 'string', 'max:100'],
            'alamat'       => ['nullable', 'string', 'max:500'],
            'no_skrd'      => ['nullable', 'string', 'max:100'],
            'beban'        => ['nullable', 'numeric', 'min:0'],
            'status'       => ['required', 'in:Lunas,Belum'],
            'tahun'        => ['nullable', 'string', 'max:10'],
            'keterangan'   => ['nullable', 'string', 'max:1000'],
        ]);

        $validated = $this->normalizeWilayahInput($validated);

        RetribusiSampah::create($validated);

        return redirect()->route('data-umum.retribusi-sampah.index')
            ->with('success', 'Data retribusi sampah berhasil ditambahkan.');
    }

    public function edit(RetribusiSampah $retribusiSampah)
    {
        $this->authorizeWilayahByRtOrRwId($retribusiSampah->rt_id, $retribusiSampah->rw_id);

        $kelurahanList = Kelurahan::orderBy('nama')->get();
        $rtList = $this->wilayahRtList();
        $rwList = $this->wilayahRwList();

        // dd($rwList);

        return view('data-umum.retribusi-sampah.edit', compact('retribusiSampah', 'kelurahanList', 'rtList', 'rwList'));
    }

    public function update(Request $request, RetribusiSampah $retribusiSampah)
    {
        $this->authorizeWilayahByRtOrRwId($retribusiSampah->rt_id, $retribusiSampah->rw_id);
        // dd($request->all());


        $validated = $request->validate([
            'kelurahan_id' => ['required', 'exists:kelurahans,id'],
            'rt_id'        => $this->rtIdRules(required: true),
            'rw_id'        => $this->rwIdRules(required: true),
            'nama_nasabah' => ['required', 'string', 'max:255'],
            'npwr'         => ['nullable', 'string', 'max:100'],
            'alamat'       => ['nullable', 'string', 'max:500'],
            'no_skrd'      => ['nullable', 'string', 'max:100'],
            'beban'        => ['nullable', 'numeric', 'min:0'],
            'status'       => ['required', 'in:Lunas,Belum'],
            'tahun'        => ['nullable', 'string', 'max:10'],
            'keterangan'   => ['nullable', 'string', 'max:1000'],
        ]);

        $validated = $this->normalizeWilayahInput($validated);

        $retribusiSampah->update($validated);

        return redirect()->route('data-umum.retribusi-sampah.index')
            ->with('success', 'Data retribusi sampah berhasil diperbarui.');
    }

    public function destroy(RetribusiSampah $retribusiSampah)
    {
        $this->authorizeWilayahByRtOrRwId($retribusiSampah->rt_id, $retribusiSampah->rw_id);

        $retribusiSampah->delete();

        return redirect()->route('data-umum.retribusi-sampah.index')
            ->with('success', 'Data retribusi sampah berhasil dihapus.');
    }
}
