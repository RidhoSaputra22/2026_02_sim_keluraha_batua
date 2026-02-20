<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penandatanganan;
use App\Models\PegawaiStaff;
use Illuminate\Http\Request;

class PenandatanganController extends Controller
{
    public function index(Request $request)
    {
        $query = Penandatanganan::with('pegawai');

        if ($search = $request->get('search')) {
            $query->whereHas('pegawai', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $penandatangan = $query->latest()->paginate(15)->withQueryString();

        return view('admin.penandatangan.index', compact('penandatangan'));
    }

    public function create()
    {
        $pegawaiList = PegawaiStaff::orderBy('nama')->get();

        return view('admin.penandatangan.create', compact('pegawaiList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pegawai_id' => ['required', 'exists:pegawai_staff,id'],
            'status'     => ['required', 'in:aktif,nonaktif'],
            'no_telp'    => ['nullable', 'string', 'max:20'],
        ]);

        $validated['petugas_input_id'] = auth()->id();
        $validated['tgl_input'] = now();

        Penandatanganan::create($validated);

        return redirect()->route('master.penandatangan.index')
            ->with('success', 'Data penandatangan berhasil ditambahkan.');
    }

    public function edit(Penandatanganan $penandatangan)
    {
        $penandatangan->load('pegawai');
        $pegawaiList = PegawaiStaff::orderBy('nama')->get();

        return view('admin.penandatangan.edit', compact('penandatangan', 'pegawaiList'));
    }

    public function update(Request $request, Penandatanganan $penandatangan)
    {
        $validated = $request->validate([
            'pegawai_id' => ['required', 'exists:pegawai_staff,id'],
            'status'     => ['required', 'in:aktif,nonaktif'],
            'no_telp'    => ['nullable', 'string', 'max:20'],
        ]);

        $penandatangan->update($validated);

        return redirect()->route('master.penandatangan.index')
            ->with('success', 'Data penandatangan berhasil diperbarui.');
    }

    public function destroy(Penandatanganan $penandatangan)
    {
        $penandatangan->delete();

        return redirect()->route('master.penandatangan.index')
            ->with('success', 'Data penandatangan berhasil dihapus.');
    }
}
