<?php

namespace App\Http\Controllers\DataUmum;

use App\Http\Controllers\Concerns\HasWilayahScope;
use App\Http\Controllers\Controller;
use App\Models\Kelurahan;
use App\Models\Pbb;
use Illuminate\Http\Request;

class PbbController extends Controller
{
    use HasWilayahScope;

    public function index(Request $request)
    {
        $query = Pbb::with(['kelurahan', 'rw', 'rt']);

        $this->applyWilayahScopeByRtOrRw($query);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_wajib_pajak', 'like', "%{$search}%")
                    ->orWhere('objek_pajak', 'like', "%{$search}%")
                    ->orWhere('nob', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($tahun = $request->get('tahun_pajak')) {
            $query->where('tahun_pajak', $tahun);
        }

        $summaryQuery = clone $query;
        $pbbList = (clone $query)->latest()->paginate(15)->withQueryString();

        // Summary stats
        $totalBeban = (clone $summaryQuery)->sum('beban');
        $totalLunas = (clone $summaryQuery)->where('status', 'Lunas')->sum('beban');
        $totalBelum = (clone $summaryQuery)->where('status', 'Belum')->sum('beban');
        $tahunList = Pbb::distinct()->whereNotNull('tahun_pajak')->orderByDesc('tahun_pajak')->pluck('tahun_pajak');

        return view('data-umum.pbb.index', compact('pbbList', 'totalBeban', 'totalLunas', 'totalBelum', 'tahunList'));
    }

    public function create()
    {
        $kelurahanList = Kelurahan::orderBy('nama')->get();
        $rtList = $this->wilayahRtList();
        $rwList = $this->wilayahRwList();

        return view('data-umum.pbb.create', compact('kelurahanList', 'rtList', 'rwList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelurahan_id'      => ['required', 'exists:kelurahans,id'],
            'rt_id'             => $this->rtIdRules(),
            'rw_id'             => $this->rwIdRules(),
            'nama_wajib_pajak'  => ['required', 'string', 'max:255'],
            'objek_pajak'       => ['nullable', 'string', 'max:255'],
            'nob'               => ['nullable', 'string', 'max:100'],
            'beban'             => ['nullable', 'numeric', 'min:0'],
            'status'            => ['required', 'in:Lunas,Belum'],
            'tahun_pajak'       => ['nullable', 'string', 'max:10'],
            'keterangan'        => ['nullable', 'string', 'max:1000'],
        ]);

        $validated = $this->normalizeWilayahInput($validated);

        Pbb::create($validated);

        return redirect()->route('data-umum.pbb.index')
            ->with('success', 'Data PBB berhasil ditambahkan.');
    }

    public function edit(Pbb $pbb)
    {
        $this->authorizeWilayahByRtOrRwId($pbb->rt_id, $pbb->rw_id);

        $kelurahanList = Kelurahan::orderBy('nama')->get();
        $rtList = $this->wilayahRtList();
        $rwList = $this->wilayahRwList();

        return view('data-umum.pbb.edit', compact('pbb', 'kelurahanList', 'rtList', 'rwList'));
    }

    public function update(Request $request, Pbb $pbb)
    {
        $this->authorizeWilayahByRtOrRwId($pbb->rt_id, $pbb->rw_id);

        $validated = $request->validate([
            'kelurahan_id'      => ['required', 'exists:kelurahans,id'],
            'rt_id'             => $this->rtIdRules(),
            'rw_id'             => $this->rwIdRules(),
            'nama_wajib_pajak'  => ['required', 'string', 'max:255'],
            'objek_pajak'       => ['nullable', 'string', 'max:255'],
            'nob'               => ['nullable', 'string', 'max:100'],
            'beban'             => ['nullable', 'numeric', 'min:0'],
            'status'            => ['required', 'in:Lunas,Belum'],
            'tahun_pajak'       => ['nullable', 'string', 'max:10'],
            'keterangan'        => ['nullable', 'string', 'max:1000'],
        ]);

        $validated = $this->normalizeWilayahInput($validated);

        $pbb->update($validated);

        return redirect()->route('data-umum.pbb.index')
            ->with('success', 'Data PBB berhasil diperbarui.');
    }

    public function destroy(Pbb $pbb)
    {
        $this->authorizeWilayahByRtOrRwId($pbb->rt_id, $pbb->rw_id);

        $pbb->delete();

        return redirect()->route('data-umum.pbb.index')
            ->with('success', 'Data PBB berhasil dihapus.');
    }
}
