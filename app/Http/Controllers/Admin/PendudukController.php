<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataPenduduk;
use Illuminate\Http\Request;

class PendudukController extends Controller
{
    public function index(Request $request)
    {
        $query = DataPenduduk::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%")
                    ->orWhere('no_kk', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        if ($rt = $request->get('rt')) {
            $query->where('rt', $rt);
        }

        if ($rw = $request->get('rw')) {
            $query->where('rw', $rw);
        }

        if ($jk = $request->get('jenis_kelamin')) {
            $query->where('jenis_kelamin', $jk);
        }

        $penduduk = $query->latest()->paginate(15)->withQueryString();

        // For filter dropdowns
        $rtList = DataPenduduk::select('rt')->distinct()->whereNotNull('rt')->orderBy('rt')->pluck('rt');
        $rwList = DataPenduduk::select('rw')->distinct()->whereNotNull('rw')->orderBy('rw')->pluck('rw');

        return view('admin.penduduk.index', compact('penduduk', 'rtList', 'rwList'));
    }

    public function create()
    {
        return view('admin.penduduk.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => ['required', 'string', 'size:16', 'unique:data_penduduk,nik'],
            'nama' => ['required', 'string', 'max:255'],
            'no_kk' => ['nullable', 'string', 'max:20', 'exists:data_keluarga,no_kk'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'gol_darah' => ['nullable', 'string', 'max:5'],
            'agama' => ['nullable', 'string', 'max:20'],
            'status_kawin' => ['nullable', 'string', 'max:30'],
            'pekerjaan' => ['nullable', 'string', 'max:100'],
            'pendidikan' => ['nullable', 'string', 'max:50'],
            'kewarganegaraan' => ['nullable', 'string', 'max:5'],
            'rt' => ['nullable', 'string', 'max:5'],
            'rw' => ['nullable', 'string', 'max:5'],
            'alamat' => ['nullable', 'string'],
            'kecamatan' => ['nullable', 'string', 'max:100'],
            'kelurahan' => ['nullable', 'string', 'max:100'],
            'status_data' => ['nullable', 'string', 'max:20'],
            'keterangan' => ['nullable', 'string'],
        ]);

        // Remove empty no_kk to avoid FK constraint issues
        if (empty($validated['no_kk'])) {
            unset($validated['no_kk']);
        }

        $validated['petugas_input'] = auth()->id();
        $validated['tgl_input'] = now();

        DataPenduduk::create($validated);

        return redirect()->route('admin.penduduk.index')
            ->with('success', 'Data penduduk berhasil ditambahkan.');
    }

    public function show(DataPenduduk $penduduk)
    {
        $penduduk->load('keluarga');

        // Only load suratKeluar if the relationship table has the nik column
        try {
            $penduduk->load('suratKeluar');
        } catch (\Throwable $e) {
            // suratKeluar relation may not be available yet
        }

        return view('admin.penduduk.show', compact('penduduk'));
    }

    public function edit(DataPenduduk $penduduk)
    {
        return view('admin.penduduk.edit', compact('penduduk'));
    }

    public function update(Request $request, DataPenduduk $penduduk)
    {
        $validated = $request->validate([
            'nik' => ['required', 'string', 'size:16', "unique:data_penduduk,nik,{$penduduk->id}"],
            'nama' => ['required', 'string', 'max:255'],
            'no_kk' => ['nullable', 'string', 'max:20', 'exists:data_keluarga,no_kk'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'gol_darah' => ['nullable', 'string', 'max:5'],
            'agama' => ['nullable', 'string', 'max:20'],
            'status_kawin' => ['nullable', 'string', 'max:30'],
            'pekerjaan' => ['nullable', 'string', 'max:100'],
            'pendidikan' => ['nullable', 'string', 'max:50'],
            'kewarganegaraan' => ['nullable', 'string', 'max:5'],
            'rt' => ['nullable', 'string', 'max:5'],
            'rw' => ['nullable', 'string', 'max:5'],
            'alamat' => ['nullable', 'string'],
            'kecamatan' => ['nullable', 'string', 'max:100'],
            'kelurahan' => ['nullable', 'string', 'max:100'],
            'status_data' => ['nullable', 'string', 'max:20'],
            'keterangan' => ['nullable', 'string'],
        ]);

        // Remove empty no_kk to avoid FK constraint issues
        if (empty($validated['no_kk'])) {
            $validated['no_kk'] = null;
        }

        $penduduk->update($validated);

        return redirect()->route('admin.penduduk.index')
            ->with('success', 'Data penduduk berhasil diperbarui.');
    }

    public function destroy(DataPenduduk $penduduk)
    {
        $penduduk->delete();

        return redirect()->route('admin.penduduk.index')
            ->with('success', 'Data penduduk berhasil dihapus.');
    }
}
