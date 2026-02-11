<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\Rw;
use Illuminate\Http\Request;

class PendudukController extends Controller
{
    public function index(Request $request)
    {
        $query = Penduduk::with(['keluarga', 'rt.rw']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        if ($rtId = $request->get('rt')) {
            $query->where('rt_id', $rtId);
        }

        if ($rwId = $request->get('rw')) {
            $query->whereHas('rt', function ($q) use ($rwId) {
                $q->where('rw_id', $rwId);
            });
        }

        if ($jk = $request->get('jenis_kelamin')) {
            $query->where('jenis_kelamin', $jk);
        }

        $penduduk = $query->latest()->paginate(15)->withQueryString();

        // For filter dropdowns — list of RT and RW with their nomor
        $rtList = Rt::with('rw')->orderBy('rw_id')->orderBy('nomor')->get();
        $rwList = Rw::orderBy('nomor')->get();

        return view('admin.penduduk.index', compact('penduduk', 'rtList', 'rwList'));
    }

    public function create()
    {
        $keluargaList = Keluarga::orderBy('no_kk')->get();
        $rtList = Rt::with('rw')->orderBy('rw_id')->orderBy('nomor')->get();

        return view('admin.penduduk.create', compact('keluargaList', 'rtList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => ['required', 'string', 'max:32', 'unique:penduduks,nik'],
            'nama' => ['required', 'string', 'max:255'],
            'keluarga_id' => ['nullable', 'exists:keluargas,id'],
            'rt_id' => ['nullable', 'exists:rts,id'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'gol_darah' => ['nullable', 'string', 'max:3'],
            'agama' => ['nullable', 'string', 'max:50'],
            'status_kawin' => ['nullable', 'string', 'max:30'],
            'pendidikan' => ['nullable', 'string', 'max:50'],
            'alamat' => ['nullable', 'string'],
            'status_data' => ['nullable', 'string', 'max:20'],
        ]);

        $validated['petugas_input_id'] = auth()->id();
        $validated['tgl_input'] = now();

        Penduduk::create($validated);

        return redirect()->route('admin.penduduk.index')
            ->with('success', 'Data penduduk berhasil ditambahkan.');
    }

    public function show(Penduduk $penduduk)
    {
        $penduduk->load(['keluarga.kepalaKeluarga', 'rt.rw']);

        return view('admin.penduduk.show', compact('penduduk'));
    }

    public function edit(Penduduk $penduduk)
    {
        $keluargaList = Keluarga::orderBy('no_kk')->get();
        $rtList = Rt::with('rw')->orderBy('rw_id')->orderBy('nomor')->get();

        return view('admin.penduduk.edit', compact('penduduk', 'keluargaList', 'rtList'));
    }

    public function update(Request $request, Penduduk $penduduk)
    {
        $validated = $request->validate([
            'nik' => ['required', 'string', 'max:32', "unique:penduduks,nik,{$penduduk->id}"],
            'nama' => ['required', 'string', 'max:255'],
            'keluarga_id' => ['nullable', 'exists:keluargas,id'],
            'rt_id' => ['nullable', 'exists:rts,id'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'gol_darah' => ['nullable', 'string', 'max:3'],
            'agama' => ['nullable', 'string', 'max:50'],
            'status_kawin' => ['nullable', 'string', 'max:30'],
            'pendidikan' => ['nullable', 'string', 'max:50'],
            'alamat' => ['nullable', 'string'],
            'status_data' => ['nullable', 'string', 'max:20'],
        ]);

        $penduduk->update($validated);

        return redirect()->route('admin.penduduk.index')
            ->with('success', 'Data penduduk berhasil diperbarui.');
    }

    public function destroy(Penduduk $penduduk)
    {
        $penduduk->delete();

        return redirect()->route('admin.penduduk.index')
            ->with('success', 'Data penduduk berhasil dihapus.');
    }
}
