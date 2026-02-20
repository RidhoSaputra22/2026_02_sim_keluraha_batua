<?php

namespace App\Http\Controllers\Usaha;

use App\Http\Controllers\Concerns\HasWilayahScope;
use App\Http\Controllers\Controller;
use App\Models\JenisUsaha;
use App\Models\Kelurahan;
use App\Models\Umkm;
use Illuminate\Http\Request;

class UsahaController extends Controller
{
    use HasWilayahScope;

    public function index(Request $request)
    {
        $query = Umkm::with(['kelurahan', 'rt.rw', 'penduduk', 'jenisUsaha']);

        $this->applyWilayahScope($query);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_ukm', 'like', "%{$search}%")
                    ->orWhere('nama_pemilik', 'like', "%{$search}%")
                    ->orWhere('nik_pemilik', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        if ($sektor = $request->get('sektor_umkm')) {
            $query->where('sektor_umkm', $sektor);
        }

        if ($jenisId = $request->get('jenis_usaha_id')) {
            $query->where('jenis_usaha_id', $jenisId);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $usahaList = $query->latest()->paginate(15)->withQueryString();
        $jenisUsahaList = JenisUsaha::orderBy('nama')->get();
        $sektorList = Umkm::distinct()->whereNotNull('sektor_umkm')->pluck('sektor_umkm');

        return view('usaha.index', compact('usahaList', 'jenisUsahaList', 'sektorList'));
    }

    public function create()
    {
        $kelurahanList  = Kelurahan::orderBy('nama')->get();
        $rtList         = $this->wilayahRtList();
        $pendudukList   = $this->wilayahPendudukList();
        $jenisUsahaList = JenisUsaha::orderBy('nama')->get();

        return view('usaha.create', compact('kelurahanList', 'rtList', 'pendudukList', 'jenisUsahaList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelurahan_id'   => ['required', 'exists:kelurahans,id'],
            'rt_id'          => $this->rtIdRules(),
            'penduduk_id'    => ['nullable', 'exists:penduduks,id'],
            'nama_pemilik'   => ['required', 'string', 'max:255'],
            'nik_pemilik'    => ['nullable', 'string', 'max:20'],
            'no_hp'          => ['nullable', 'string', 'max:20'],
            'nama_ukm'       => ['required', 'string', 'max:255'],
            'alamat'         => ['nullable', 'string', 'max:500'],
            'sektor_umkm'    => ['nullable', 'string', 'max:255'],
            'jenis_usaha_id' => ['nullable', 'exists:jenis_usaha,id'],
            'status'         => ['nullable', 'string', 'in:aktif,tidak_aktif'],
        ]);

        Umkm::create($validated);

        return redirect()->route('usaha.index')
            ->with('success', 'Data usaha berhasil ditambahkan.');
    }

    public function edit(Umkm $usaha)
    {
        $this->authorizeWilayahByRtId($usaha->rt_id);

        $usaha->load(['kelurahan', 'rt.rw', 'penduduk', 'jenisUsaha']);
        $kelurahanList  = Kelurahan::orderBy('nama')->get();
        $rtList         = $this->wilayahRtList();
        $pendudukList   = $this->wilayahPendudukList();
        $jenisUsahaList = JenisUsaha::orderBy('nama')->get();

        return view('usaha.edit', compact('usaha', 'kelurahanList', 'rtList', 'pendudukList', 'jenisUsahaList'));
    }

    public function update(Request $request, Umkm $usaha)
    {
        $this->authorizeWilayahByRtId($usaha->rt_id);

        $validated = $request->validate([
            'kelurahan_id'   => ['required', 'exists:kelurahans,id'],
            'rt_id'          => $this->rtIdRules(),
            'penduduk_id'    => ['nullable', 'exists:penduduks,id'],
            'nama_pemilik'   => ['required', 'string', 'max:255'],
            'nik_pemilik'    => ['nullable', 'string', 'max:20'],
            'no_hp'          => ['nullable', 'string', 'max:20'],
            'nama_ukm'       => ['required', 'string', 'max:255'],
            'alamat'         => ['nullable', 'string', 'max:500'],
            'sektor_umkm'    => ['nullable', 'string', 'max:255'],
            'jenis_usaha_id' => ['nullable', 'exists:jenis_usaha,id'],
            'status'         => ['nullable', 'string', 'in:aktif,tidak_aktif'],
        ]);

        $usaha->update($validated);

        return redirect()->route('usaha.index')
            ->with('success', 'Data usaha berhasil diperbarui.');
    }

    public function destroy(Umkm $usaha)
    {
        $this->authorizeWilayahByRtId($usaha->rt_id);

        $usaha->delete();

        return redirect()->route('usaha.index')
            ->with('success', 'Data usaha berhasil dihapus.');
    }
}
