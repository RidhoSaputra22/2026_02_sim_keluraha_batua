<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PegawaiStaff;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $query = PegawaiStaff::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status_pegawai')) {
            $query->where('status_pegawai', $status);
        }

        $pegawai = $query->orderBy('no_urut')->paginate(15)->withQueryString();

        return view('admin.pegawai.index', compact('pegawai'));
    }

    public function create()
    {
        return view('admin.pegawai.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip'             => ['required', 'string', 'max:30', 'unique:pegawai_staff,nip'],
            'nama'            => ['required', 'string', 'max:255'],
            'jabatan'         => ['required', 'string', 'max:100'],
            'gol'             => ['nullable', 'string', 'max:20'],
            'pangkat'         => ['nullable', 'string', 'max:50'],
            'status_pegawai'  => ['required', 'in:aktif,nonaktif'],
            'no_urut'         => ['nullable', 'integer'],
        ]);

        $validated['petugas_input_id'] = auth()->id();
        $validated['tgl_input'] = now();

        PegawaiStaff::create($validated);

        return redirect()->route('master.pegawai.index')
            ->with('success', 'Data pegawai berhasil ditambahkan.');
    }

    public function edit(PegawaiStaff $pegawai)
    {
        return view('admin.pegawai.edit', compact('pegawai'));
    }

    public function update(Request $request, PegawaiStaff $pegawai)
    {
        $validated = $request->validate([
            'nip'             => ['required', 'string', 'max:30', "unique:pegawai_staff,nip,{$pegawai->id}"],
            'nama'            => ['required', 'string', 'max:255'],
            'jabatan'         => ['required', 'string', 'max:100'],
            'gol'             => ['nullable', 'string', 'max:20'],
            'pangkat'         => ['nullable', 'string', 'max:50'],
            'status_pegawai'  => ['required', 'in:aktif,nonaktif'],
            'no_urut'         => ['nullable', 'integer'],
        ]);

        $pegawai->update($validated);

        return redirect()->route('master.pegawai.index')
            ->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy(PegawaiStaff $pegawai)
    {
        $pegawai->delete();

        return redirect()->route('master.pegawai.index')
            ->with('success', 'Data pegawai berhasil dihapus.');
    }
}
