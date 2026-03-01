<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JabatanRtRw;
use App\Models\Kelurahan;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\RtRwPengurus;
use App\Models\Rw;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    public function index(Request $request)
    {
        $query = RtRwPengurus::with(['penduduk', 'jabatan', 'rw', 'rt']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('penduduk', function ($qp) use ($search) {
                    $qp->where('nama', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%");
                })
                    ->orWhere('no_telp', 'like', "%{$search}%");
            });
        }

        if ($jabatanId = $request->get('jabatan')) {
            $query->where('jabatan_id', $jabatanId);
        }

        if ($rwId = $request->get('rw')) {
            $query->where('rw_id', $rwId);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $wilayah = $query->orderBy('rw_id')->orderBy('rt_id')->paginate(15)->withQueryString();

        $rwList = Rw::orderBy('nomor')->get();
        $jabatanList = JabatanRtRw::orderBy('nama')->get();

        // Stats for summary cards
        $totalRT = Rt::count();
        $totalRW = Rw::count();
        $totalAktif = RtRwPengurus::where('status', 'aktif')->count();
        $totalNonaktif = RtRwPengurus::where('status', 'nonaktif')->count();

        return view('wilayah.index', compact('wilayah', 'rwList', 'jabatanList', 'totalRT', 'totalRW', 'totalAktif', 'totalNonaktif'));
    }

    public function create()
    {
        $pendudukList = Penduduk::orderBy('nama')->get();
        $jabatanList = JabatanRtRw::orderBy('nama')->get();
        $rwList = Rw::orderBy('nomor')->get();
        $rtList = Rt::with('rw')->orderBy('rw_id')->orderBy('nomor')->get();
        $kelurahanList = Kelurahan::orderBy('nama')->get();

        return view('wilayah.create', compact('pendudukList', 'jabatanList', 'rwList', 'rtList', 'kelurahanList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelurahan_id' => ['required', 'exists:kelurahans,id'],
            'penduduk_id' => ['required', 'exists:penduduks,id'],
            'jabatan_id' => ['required', 'exists:jabatan_rt_rw,id'],
            'rw_id' => ['nullable', 'exists:rws,id'],
            'rt_id' => ['nullable', 'exists:rts,id'],
            'tgl_mulai' => ['nullable', 'date'],
            'status' => ['required', 'in:aktif,nonaktif'],
            'alamat' => ['nullable', 'string'],
            'no_telp' => ['nullable', 'string', 'max:20'],
            'no_rekening' => ['nullable', 'string', 'max:30'],
            'no_npwp' => ['nullable', 'string', 'max:30'],
        ]);

        RtRwPengurus::create($validated);

        return redirect()->route('master.wilayah.index')
            ->with('success', 'Data pengurus RT/RW berhasil ditambahkan.');
    }

    public function edit(RtRwPengurus $wilayah)
    {
        $wilayah->load(['penduduk', 'jabatan', 'rw', 'rt']);
        $pendudukList = Penduduk::orderBy('nama')->get();
        $jabatanList = JabatanRtRw::orderBy('nama')->get();
        $rwList = Rw::orderBy('nomor')->get();
        $rtList = Rt::with('rw')->orderBy('rw_id')->orderBy('nomor')->get();
        $kelurahanList = Kelurahan::orderBy('nama')->get();

        return view('wilayah.edit', compact('wilayah', 'pendudukList', 'jabatanList', 'rwList', 'rtList', 'kelurahanList'));
    }

    public function update(Request $request, RtRwPengurus $wilayah)
    {
        $validated = $request->validate([
            'kelurahan_id' => ['required', 'exists:kelurahans,id'],
            'penduduk_id' => ['required', 'exists:penduduks,id'],
            'jabatan_id' => ['required', 'exists:jabatan_rt_rw,id'],
            'rw_id' => ['nullable', 'exists:rws,id'],
            'rt_id' => ['nullable', 'exists:rts,id'],
            'tgl_mulai' => ['nullable', 'date'],
            'status' => ['required', 'in:aktif,nonaktif'],
            'alamat' => ['nullable', 'string'],
            'no_telp' => ['nullable', 'string', 'max:20'],
            'no_rekening' => ['nullable', 'string', 'max:30'],
            'no_npwp' => ['nullable', 'string', 'max:30'],
        ]);

        $wilayah->update($validated);

        return redirect()->route('master.wilayah.index')
            ->with('success', 'Data pengurus RT/RW berhasil diperbarui.');
    }

    public function destroy(RtRwPengurus $wilayah)
    {
        $wilayah->delete();

        return redirect()->route('master.wilayah.index')
            ->with('success', 'Data pengurus RT/RW berhasil dihapus.');
    }
}
