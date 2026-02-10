<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataRtRw;
use App\Enums\JabatanRtRwEnum;
use App\Enums\StatusAktifEnum;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    public function index(Request $request)
    {
        $query = DataRtRw::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('no_telp', 'like', "%{$search}%");
            });
        }

        if ($jabatan = $request->get('jabatan')) {
            $query->where('jabatan', $jabatan);
        }

        if ($rw = $request->get('rw')) {
            $query->where('rw', $rw);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $wilayah = $query->orderBy('rw')->orderBy('rt')->paginate(15)->withQueryString();

        $rwList = DataRtRw::select('rw')->distinct()->whereNotNull('rw')->orderBy('rw')->pluck('rw');

        // Stats for summary cards
        $totalRT = DataRtRw::where('jabatan', 'RT')->count();
        $totalRW = DataRtRw::where('jabatan', 'RW')->count();
        $totalAktif = DataRtRw::where('status', 'aktif')->count();
        $totalNonaktif = DataRtRw::where('status', 'nonaktif')->count();

        return view('admin.wilayah.index', compact('wilayah', 'rwList', 'totalRT', 'totalRW', 'totalAktif', 'totalNonaktif'));
    }

    public function create()
    {
        return view('admin.wilayah.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik'          => ['nullable', 'string', 'size:16', 'exists:data_penduduk,nik'],
            'nama'         => ['required', 'string', 'max:255'],
            'jabatan'      => ['required', 'in:RT,RW'],
            'rt'           => ['nullable', 'string', 'max:5'],
            'rw'           => ['required', 'string', 'max:5'],
            'kelurahan'    => ['nullable', 'string', 'max:100'],
            'kecamatan'    => ['nullable', 'string', 'max:100'],
            'alamat'       => ['nullable', 'string'],
            'no_telp'      => ['nullable', 'string', 'max:20'],
            'email'        => ['nullable', 'email', 'max:100'],
            'tgl_mulai'    => ['nullable', 'date'],
            'tgl_selesai'  => ['nullable', 'date', 'after_or_equal:tgl_mulai'],
            'periode'      => ['nullable', 'string', 'max:20'],
            'status'       => ['required', 'in:aktif,nonaktif'],
            'no_rekening'  => ['nullable', 'string', 'max:30'],
            'no_npwp'      => ['nullable', 'string', 'max:30'],
            'keterangan'   => ['nullable', 'string'],
        ]);

        // Remove empty nik to avoid FK constraint issues
        if (empty($validated['nik'])) {
            unset($validated['nik']);
        }

        $validated['petugas_input'] = auth()->id();
        $validated['tgl_input'] = now();

        DataRtRw::create($validated);

        return redirect()->route('admin.wilayah.index')
            ->with('success', 'Data wilayah RT/RW berhasil ditambahkan.');
    }

    public function edit(DataRtRw $wilayah)
    {
        return view('admin.wilayah.edit', compact('wilayah'));
    }

    public function update(Request $request, DataRtRw $wilayah)
    {
        $validated = $request->validate([
            'nik'          => ['nullable', 'string', 'size:16', 'exists:data_penduduk,nik'],
            'nama'         => ['required', 'string', 'max:255'],
            'jabatan'      => ['required', 'in:RT,RW'],
            'rt'           => ['nullable', 'string', 'max:5'],
            'rw'           => ['required', 'string', 'max:5'],
            'kelurahan'    => ['nullable', 'string', 'max:100'],
            'kecamatan'    => ['nullable', 'string', 'max:100'],
            'alamat'       => ['nullable', 'string'],
            'no_telp'      => ['nullable', 'string', 'max:20'],
            'email'        => ['nullable', 'email', 'max:100'],
            'tgl_mulai'    => ['nullable', 'date'],
            'tgl_selesai'  => ['nullable', 'date', 'after_or_equal:tgl_mulai'],
            'periode'      => ['nullable', 'string', 'max:20'],
            'status'       => ['required', 'in:aktif,nonaktif'],
            'no_rekening'  => ['nullable', 'string', 'max:30'],
            'no_npwp'      => ['nullable', 'string', 'max:30'],
            'keterangan'   => ['nullable', 'string'],
        ]);

        // Remove empty nik to avoid FK constraint issues
        if (empty($validated['nik'])) {
            $validated['nik'] = null;
        }

        $wilayah->update($validated);

        return redirect()->route('admin.wilayah.index')
            ->with('success', 'Data wilayah RT/RW berhasil diperbarui.');
    }

    public function destroy(DataRtRw $wilayah)
    {
        $wilayah->delete();

        return redirect()->route('admin.wilayah.index')
            ->with('success', 'Data wilayah RT/RW berhasil dihapus.');
    }
}
