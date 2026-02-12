<?php

namespace App\Http\Controllers\DataUmum;

use App\Http\Controllers\Controller;
use App\Models\Kelurahan;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\TempatIbadah;
use Illuminate\Http\Request;

class TempatIbadahController extends Controller
{
    public function index(Request $request)
    {
        $query = TempatIbadah::with(['kelurahan', 'rt', 'rw']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('tempat_ibadah', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%")
                    ->orWhere('pengurus', 'like', "%{$search}%");
            });
        }

        if ($jenis = $request->get('tempat_ibadah')) {
            $query->where('tempat_ibadah', $jenis);
        }

        $tempatIbadahList = $query->latest()->paginate(15)->withQueryString();
        $jenisList = TempatIbadah::distinct()->whereNotNull('tempat_ibadah')->pluck('tempat_ibadah');

        return view('data-umum.tempat-ibadah.index', compact('tempatIbadahList', 'jenisList'));
    }

    public function create()
    {
        $kelurahanList = Kelurahan::orderBy('nama')->get();
        $rwList = Rw::orderBy('nomor')->get();
        $rtList = Rt::with('rw')->orderBy('rw_id')->orderBy('nomor')->get();
        return view('data-umum.tempat-ibadah.create', compact('kelurahanList', 'rwList', 'rtList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelurahan_id' => ['required', 'exists:kelurahans,id'],
            'tempat_ibadah' => ['required', 'string', 'max:100'],
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'rt_id' => ['nullable', 'exists:rts,id'],
            'rw_id' => ['nullable', 'exists:rws,id'],
            'pengurus' => ['nullable', 'string', 'max:255'],
        ]);

        TempatIbadah::create($validated);

        return redirect()->route('data-umum.tempat-ibadah.index')
            ->with('success', 'Data tempat ibadah berhasil ditambahkan.');
    }

    public function edit(TempatIbadah $tempatIbadah)
    {
        $kelurahanList = Kelurahan::orderBy('nama')->get();
        $rwList = Rw::orderBy('nomor')->get();
        $rtList = Rt::with('rw')->orderBy('rw_id')->orderBy('nomor')->get();
        return view('data-umum.tempat-ibadah.edit', compact('tempatIbadah', 'kelurahanList', 'rwList', 'rtList'));
    }

    public function update(Request $request, TempatIbadah $tempatIbadah)
    {
        $validated = $request->validate([
            'kelurahan_id' => ['required', 'exists:kelurahans,id'],
            'tempat_ibadah' => ['required', 'string', 'max:100'],
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'rt_id' => ['nullable', 'exists:rts,id'],
            'rw_id' => ['nullable', 'exists:rws,id'],
            'pengurus' => ['nullable', 'string', 'max:255'],
        ]);

        $tempatIbadah->update($validated);

        return redirect()->route('data-umum.tempat-ibadah.index')
            ->with('success', 'Data tempat ibadah berhasil diperbarui.');
    }

    public function destroy(TempatIbadah $tempatIbadah)
    {
        $tempatIbadah->delete();

        return redirect()->route('data-umum.tempat-ibadah.index')
            ->with('success', 'Data tempat ibadah berhasil dihapus.');
    }
}
