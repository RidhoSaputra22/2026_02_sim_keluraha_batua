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

        $this->applyWilayahScope($query);

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

        $retribusiList = $query->latest()->paginate(15)->withQueryString();

        // Summary stats
        $totalBeban = RetribusiSampah::sum('beban');
        $totalLunas = RetribusiSampah::where('status', 'Lunas')->sum('beban');
        $totalBelum = RetribusiSampah::where('status', 'Belum')->sum('beban');
        $totalNasabah = RetribusiSampah::count();
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
            'rt_id'        => ['required', 'exists:rts,id'],
            'rw_id'        => ['required', 'exists:rws,id'],
            'nama_nasabah' => ['required', 'string', 'max:255'],
            'npwr'         => ['nullable', 'string', 'max:100'],
            'alamat'       => ['nullable', 'string', 'max:500'],
            'no_skrd'      => ['nullable', 'string', 'max:100'],
            'beban'        => ['nullable', 'numeric', 'min:0'],
            'status'       => ['required', 'in:Lunas,Belum'],
            'tahun'        => ['nullable', 'string', 'max:10'],
            'keterangan'   => ['nullable', 'string', 'max:1000'],
        ]);

        RetribusiSampah::create($validated);

        return redirect()->route('data-umum.retribusi-sampah.index')
            ->with('success', 'Data retribusi sampah berhasil ditambahkan.');
    }

    public function edit(RetribusiSampah $retribusiSampah)
    {
        $this->authorizeWilayahByRtId($retribusiSampah->rt_id);

        $kelurahanList = Kelurahan::orderBy('nama')->get();
        $rtList = $this->wilayahRtList();
        $rwList = $this->wilayahRwList();

        return view('data-umum.retribusi-sampah.edit', compact('retribusiSampah', 'kelurahanList', 'rtList', 'rwList'));
    }

    public function update(Request $request, RetribusiSampah $retribusiSampah)
    {
        $this->authorizeWilayahByRtId($retribusiSampah->rt_id);

        $validated = $request->validate([
            'kelurahan_id' => ['required', 'exists:kelurahans,id'],
            'rt_id'        => ['required', 'exists:rts,id'],
            'rw_id'        => ['required', 'exists:rws,id'],
            'nama_nasabah' => ['required', 'string', 'max:255'],
            'npwr'         => ['nullable', 'string', 'max:100'],
            'alamat'       => ['nullable', 'string', 'max:500'],
            'no_skrd'      => ['nullable', 'string', 'max:100'],
            'beban'        => ['nullable', 'numeric', 'min:0'],
            'status'       => ['required', 'in:Lunas,Belum'],
            'tahun'        => ['nullable', 'string', 'max:10'],
            'keterangan'   => ['nullable', 'string', 'max:1000'],
        ]);

        $retribusiSampah->update($validated);

        return redirect()->route('data-umum.retribusi-sampah.index')
            ->with('success', 'Data retribusi sampah berhasil diperbarui.');
    }

    public function destroy(RetribusiSampah $retribusiSampah)
    {
        $this->authorizeWilayahByRtId($retribusiSampah->rt_id);

        $retribusiSampah->delete();

        return redirect()->route('data-umum.retribusi-sampah.index')
            ->with('success', 'Data retribusi sampah berhasil dihapus.');
    }
}
