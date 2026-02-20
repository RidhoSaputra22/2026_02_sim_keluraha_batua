<?php

namespace App\Http\Controllers\Kependudukan;

use App\Http\Controllers\Concerns\HasWilayahScope;
use App\Http\Controllers\Controller;
use App\Models\Kelahiran;
use Illuminate\Http\Request;

class KelahiranController extends Controller
{
    use HasWilayahScope;

    public function index(Request $request)
    {
        $query = Kelahiran::with(['ibu', 'ayah', 'rt.rw', 'petugas']);

        $this->applyWilayahScope($query);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_bayi', 'like', "%{$search}%")
                    ->orWhere('no_akte', 'like', "%{$search}%")
                    ->orWhereHas('ibu', fn ($qi) => $qi->where('nama', 'like', "%{$search}%"))
                    ->orWhereHas('ayah', fn ($qa) => $qa->where('nama', 'like', "%{$search}%"));
            });
        }

        if ($jk = $request->get('jenis_kelamin')) {
            $query->where('jenis_kelamin', $jk);
        }

        $kelahiran = $query->latest('tanggal_lahir')->paginate(15)->withQueryString();

        return view('kependudukan.kelahiran.index', compact('kelahiran'));
    }

    public function create()
    {
        $pendudukList = $this->wilayahPendudukList();
        $rtList       = $this->wilayahRtList();

        return view('kependudukan.kelahiran.create', compact('pendudukList', 'rtList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_bayi'     => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'tempat_lahir'  => ['nullable', 'string', 'max:255'],
            'tanggal_lahir' => ['required', 'date'],
            'jam_lahir'     => ['nullable', 'date_format:H:i'],
            'ibu_id'        => ['nullable', 'exists:penduduks,id'],
            'ayah_id'       => ['nullable', 'exists:penduduks,id'],
            'rt_id'         => $this->rtIdRules(),
            'no_akte'       => ['nullable', 'string', 'max:100'],
            'keterangan'    => ['nullable', 'string', 'max:500'],
        ]);

        $validated['petugas_id'] = auth()->id();

        Kelahiran::create($validated);

        return redirect()->route('kependudukan.kelahiran.index')
            ->with('success', 'Data kelahiran berhasil ditambahkan.');
    }

    public function edit(Kelahiran $kelahiran)
    {
        $this->authorizeWilayahByRtId($kelahiran->rt_id);

        $pendudukList = $this->wilayahPendudukList();
        $rtList       = $this->wilayahRtList();

        return view('kependudukan.kelahiran.edit', compact('kelahiran', 'pendudukList', 'rtList'));
    }

    public function update(Request $request, Kelahiran $kelahiran)
    {
        $this->authorizeWilayahByRtId($kelahiran->rt_id);

        $validated = $request->validate([
            'nama_bayi'     => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'tempat_lahir'  => ['nullable', 'string', 'max:255'],
            'tanggal_lahir' => ['required', 'date'],
            'jam_lahir'     => ['nullable', 'date_format:H:i'],
            'ibu_id'        => ['nullable', 'exists:penduduks,id'],
            'ayah_id'       => ['nullable', 'exists:penduduks,id'],
            'rt_id'         => $this->rtIdRules(),
            'no_akte'       => ['nullable', 'string', 'max:100'],
            'keterangan'    => ['nullable', 'string', 'max:500'],
        ]);

        $kelahiran->update($validated);

        return redirect()->route('kependudukan.kelahiran.index')
            ->with('success', 'Data kelahiran berhasil diperbarui.');
    }

    public function destroy(Kelahiran $kelahiran)
    {
        $this->authorizeWilayahByRtId($kelahiran->rt_id);

        $kelahiran->delete();

        return redirect()->route('kependudukan.kelahiran.index')
            ->with('success', 'Data kelahiran berhasil dihapus.');
    }
}
