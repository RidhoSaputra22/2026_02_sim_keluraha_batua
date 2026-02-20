<?php

namespace App\Http\Controllers\RtRw;

use App\Http\Controllers\Concerns\HasWilayahScope;
use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use App\Models\Penduduk;
use Illuminate\Http\Request;

class WargaController extends Controller
{
    use HasWilayahScope;

    /**
     * Daftar warga di wilayah RT/RW.
     */
    public function index(Request $request)
    {
        $query = Penduduk::with('rt.rw', 'keluarga');

        $this->applyWilayahScope($query);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        if ($jk = $request->input('jenis_kelamin')) {
            $query->where('jenis_kelamin', $jk);
        }

        $penduduk = $query->orderBy('nama')->paginate(15)->withQueryString();

        return view('rt-rw.warga.index', compact('penduduk'));
    }

    /**
     * Detail warga.
     */
    public function show(Penduduk $penduduk)
    {
        $this->authorizeWilayah($penduduk);

        $penduduk->load('rt.rw', 'keluarga.anggota', 'keluarga.kepalaKeluarga');

        return view('rt-rw.warga.show', compact('penduduk'));
    }

    /**
     * Daftar KK di wilayah RT/RW.
     */
    public function keluarga(Request $request)
    {
        $rtIds = $this->wilayahRtIds();

        $query = Keluarga::whereIn('rt_id', $rtIds)->with('kepalaKeluarga', 'rt.rw');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('no_kk', 'like', "%{$search}%")
                    ->orWhereHas('kepalaKeluarga', fn ($q2) => $q2->where('nama', 'like', "%{$search}%"));
            });
        }

        $keluarga = $query->orderBy('no_kk')->paginate(15)->withQueryString();

        return view('rt-rw.warga.keluarga', compact('keluarga'));
    }
}
