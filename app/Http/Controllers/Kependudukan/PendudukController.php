<?php

namespace App\Http\Controllers\Kependudukan;

use App\Http\Controllers\Concerns\HasWilayahScope;
use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use App\Models\Penduduk;
use Illuminate\Http\Request;

class PendudukController extends Controller
{
    use HasWilayahScope;

    public function index(Request $request)
    {
        $query = Penduduk::with(['keluarga', 'rt.rw']);

        // Batasi scope wilayah untuk user RT/RW
        $this->applyWilayahScope($query);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        // Filter RT/RW dropdown hanya untuk admin & operator
        // (RT/RW sudah otomatis dibatasi lewat applyWilayahScope)
        if (! $this->isRtRw()) {
            if ($rtId = $request->get('rt')) {
                $query->where('rt_id', $rtId);
            }

            if ($rwId = $request->get('rw')) {
                $query->whereHas('rt', fn ($q) => $q->where('rw_id', $rwId));
            }
        }

        if ($jk = $request->get('jenis_kelamin')) {
            $query->where('jenis_kelamin', $jk);
        }

        $penduduk = $query->latest()->paginate(15)->withQueryString();

        $rtList = $this->wilayahRtList();
        $rwList = $this->wilayahRwList();

        return view('kependudukan.penduduk.index', compact('penduduk', 'rtList', 'rwList'));
    }

    public function create()
    {
        $keluargaList = Keluarga::orderBy('no_kk')->get();
        $rtList       = $this->wilayahRtList();

        return view('kependudukan.penduduk.create', compact('keluargaList', 'rtList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik'           => ['required', 'string', 'max:32', 'unique:penduduks,nik'],
            'nama'          => ['required', 'string', 'max:255'],
            'keluarga_id'   => ['nullable', 'exists:keluargas,id'],
            'rt_id'         => $this->rtIdRules(),
            'jenis_kelamin' => ['required', 'in:L,P'],
            'gol_darah'     => ['nullable', 'string', 'max:3'],
            'agama'         => ['nullable', 'string', 'max:50'],
            'status_kawin'  => ['nullable', 'string', 'max:30'],
            'pendidikan'    => ['nullable', 'string', 'max:50'],
            'alamat'        => ['nullable', 'string'],
            'status_data'   => ['nullable', 'string', 'max:20'],
        ]);

        $validated['petugas_input_id'] = auth()->id();
        $validated['tgl_input']        = now();

        Penduduk::create($validated);

        return redirect()->route('kependudukan.penduduk.index')
            ->with('success', 'Data penduduk berhasil ditambahkan.');
    }

    public function show(Penduduk $penduduk)
    {
        $this->authorizeWilayah($penduduk);

        $penduduk->load(['keluarga.kepalaKeluarga', 'rt.rw']);

        return view('kependudukan.penduduk.show', compact('penduduk'));
    }

    public function edit(Penduduk $penduduk)
    {
        $this->authorizeWilayah($penduduk);

        $keluargaList = Keluarga::orderBy('no_kk')->get();
        $rtList       = $this->wilayahRtList();

        return view('kependudukan.penduduk.edit', compact('penduduk', 'keluargaList', 'rtList'));
    }

    public function update(Request $request, Penduduk $penduduk)
    {
        $this->authorizeWilayah($penduduk);

        $validated = $request->validate([
            'nik'           => ['required', 'string', 'max:32', "unique:penduduks,nik,{$penduduk->id}"],
            'nama'          => ['required', 'string', 'max:255'],
            'keluarga_id'   => ['nullable', 'exists:keluargas,id'],
            'rt_id'         => $this->rtIdRules(),
            'jenis_kelamin' => ['required', 'in:L,P'],
            'gol_darah'     => ['nullable', 'string', 'max:3'],
            'agama'         => ['nullable', 'string', 'max:50'],
            'status_kawin'  => ['nullable', 'string', 'max:30'],
            'pendidikan'    => ['nullable', 'string', 'max:50'],
            'alamat'        => ['nullable', 'string'],
            'status_data'   => ['nullable', 'string', 'max:20'],
        ]);

        $penduduk->update($validated);

        return redirect()->route('kependudukan.penduduk.index')
            ->with('success', 'Data penduduk berhasil diperbarui.');
    }

    public function destroy(Penduduk $penduduk)
    {
        $this->authorizeWilayah($penduduk);

        $penduduk->delete();

        return redirect()->route('kependudukan.penduduk.index')
            ->with('success', 'Data penduduk berhasil dihapus.');
    }
}
