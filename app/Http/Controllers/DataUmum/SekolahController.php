<?php

namespace App\Http\Controllers\DataUmum;

use App\Http\Controllers\Controller;
use App\Models\Kelurahan;
use App\Models\Sekolah;
use Illuminate\Http\Request;

class SekolahController extends Controller
{
    public function index(Request $request)
    {
        $query = Sekolah::with('kelurahan');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_sekolah', 'like', "%{$search}%")
                    ->orWhere('npsn', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        if ($jenjang = $request->get('jenjang')) {
            $query->where('jenjang', $jenjang);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $sekolahList = $query->latest()->paginate(15)->withQueryString();
        $jenjangList = Sekolah::distinct()->whereNotNull('jenjang')->pluck('jenjang');

        return view('data-umum.sekolah.index', compact('sekolahList', 'jenjangList'));
    }

    public function create()
    {
        $kelurahanList = Kelurahan::orderBy('nama')->get();
        return view('data-umum.sekolah.create', compact('kelurahanList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelurahan_id' => ['required', 'exists:kelurahans,id'],
            'npsn' => ['nullable', 'string', 'max:20'],
            'nama_sekolah' => ['required', 'string', 'max:255'],
            'jenjang' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'string', 'max:50'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'tahun_ajar' => ['nullable', 'string', 'max:20'],
            'jumlah_siswa' => ['nullable', 'integer', 'min:0'],
            'rombel' => ['nullable', 'integer', 'min:0'],
            'jumlah_guru' => ['nullable', 'integer', 'min:0'],
            'jumlah_pegawai' => ['nullable', 'integer', 'min:0'],
            'ruang_kelas' => ['nullable', 'integer', 'min:0'],
            'jumlah_r_lab' => ['nullable', 'integer', 'min:0'],
            'jumlah_r_perpus' => ['nullable', 'integer', 'min:0'],
        ]);

        Sekolah::create($validated);

        return redirect()->route('data-umum.sekolah.index')
            ->with('success', 'Data sekolah berhasil ditambahkan.');
    }

    public function edit(Sekolah $sekolah)
    {
        $kelurahanList = Kelurahan::orderBy('nama')->get();
        return view('data-umum.sekolah.edit', compact('sekolah', 'kelurahanList'));
    }

    public function update(Request $request, Sekolah $sekolah)
    {
        $validated = $request->validate([
            'kelurahan_id' => ['required', 'exists:kelurahans,id'],
            'npsn' => ['nullable', 'string', 'max:20'],
            'nama_sekolah' => ['required', 'string', 'max:255'],
            'jenjang' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'string', 'max:50'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'tahun_ajar' => ['nullable', 'string', 'max:20'],
            'jumlah_siswa' => ['nullable', 'integer', 'min:0'],
            'rombel' => ['nullable', 'integer', 'min:0'],
            'jumlah_guru' => ['nullable', 'integer', 'min:0'],
            'jumlah_pegawai' => ['nullable', 'integer', 'min:0'],
            'ruang_kelas' => ['nullable', 'integer', 'min:0'],
            'jumlah_r_lab' => ['nullable', 'integer', 'min:0'],
            'jumlah_r_perpus' => ['nullable', 'integer', 'min:0'],
        ]);

        $sekolah->update($validated);

        return redirect()->route('data-umum.sekolah.index')
            ->with('success', 'Data sekolah berhasil diperbarui.');
    }

    public function destroy(Sekolah $sekolah)
    {
        $sekolah->delete();

        return redirect()->route('data-umum.sekolah.index')
            ->with('success', 'Data sekolah berhasil dihapus.');
    }
}
