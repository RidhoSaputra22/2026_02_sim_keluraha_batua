<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penandatanganan;
use Illuminate\Http\Request;

class PenandatanganController extends Controller
{
    public function index(Request $request)
    {
        $query = Penandatanganan::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
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
        return view('admin.penandatangan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik'          => ['nullable', 'string', 'max:20'],
            'nip'          => ['nullable', 'string', 'max:30'],
            'nama'         => ['required', 'string', 'max:255'],
            'jabatan'      => ['required', 'string', 'max:100'],
            'pangkat'      => ['nullable', 'string', 'max:50'],
            'golongan'     => ['nullable', 'string', 'max:20'],
            'status'       => ['required', 'in:aktif,nonaktif'],
            'alamat'       => ['nullable', 'string'],
            'no_telp'      => ['nullable', 'string', 'max:20'],
            'email'        => ['nullable', 'email', 'max:100'],
            'tgl_mulai'    => ['nullable', 'date'],
            'tgl_selesai'  => ['nullable', 'date', 'after_or_equal:tgl_mulai'],
        ]);

        $validated['petugas_input'] = auth()->id();
        $validated['tgl_input'] = now();

        Penandatanganan::create($validated);

        return redirect()->route('admin.penandatangan.index')
            ->with('success', 'Data penandatangan berhasil ditambahkan.');
    }

    public function edit(Penandatanganan $penandatangan)
    {
        return view('admin.penandatangan.edit', compact('penandatangan'));
    }

    public function update(Request $request, Penandatanganan $penandatangan)
    {
        $validated = $request->validate([
            'nik'          => ['nullable', 'string', 'max:20'],
            'nip'          => ['nullable', 'string', 'max:30'],
            'nama'         => ['required', 'string', 'max:255'],
            'jabatan'      => ['required', 'string', 'max:100'],
            'pangkat'      => ['nullable', 'string', 'max:50'],
            'golongan'     => ['nullable', 'string', 'max:20'],
            'status'       => ['required', 'in:aktif,nonaktif'],
            'alamat'       => ['nullable', 'string'],
            'no_telp'      => ['nullable', 'string', 'max:20'],
            'email'        => ['nullable', 'email', 'max:100'],
            'tgl_mulai'    => ['nullable', 'date'],
            'tgl_selesai'  => ['nullable', 'date', 'after_or_equal:tgl_mulai'],
        ]);

        $penandatangan->update($validated);

        return redirect()->route('admin.penandatangan.index')
            ->with('success', 'Data penandatangan berhasil diperbarui.');
    }

    public function destroy(Penandatanganan $penandatangan)
    {
        $penandatangan->delete();

        return redirect()->route('admin.penandatangan.index')
            ->with('success', 'Data penandatangan berhasil dihapus.');
    }
}
