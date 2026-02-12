<?php

namespace App\Http\Controllers\DataUmum;

use App\Http\Controllers\Controller;
use App\Models\Kelurahan;
use App\Models\PetugasKebersihan;
use Illuminate\Http\Request;

class PetugasKebersihanController extends Controller
{
    public function index(Request $request)
    {
        $query = PetugasKebersihan::with(['kelurahan', 'penduduk']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('unit_kerja', 'like', "%{$search}%")
                    ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $petugasList = $query->latest()->paginate(15)->withQueryString();

        return view('data-umum.petugas-kebersihan.index', compact('petugasList'));
    }

    public function create()
    {
        $kelurahanList = Kelurahan::orderBy('nama')->get();
        return view('data-umum.petugas-kebersihan.create', compact('kelurahanList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelurahan_id' => ['required', 'exists:kelurahans,id'],
            'penduduk_id' => ['nullable', 'exists:penduduks,id'],
            'nama' => ['required', 'string', 'max:255'],
            'nik' => ['nullable', 'string', 'max:16'],
            'unit_kerja' => ['nullable', 'string', 'max:255'],
            'jenis_kelamin' => ['nullable', 'string', 'max:20'],
            'pekerjaan' => ['nullable', 'string', 'max:100'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:50'],
        ]);

        PetugasKebersihan::create($validated);

        return redirect()->route('data-umum.petugas-kebersihan.index')
            ->with('success', 'Data petugas kebersihan berhasil ditambahkan.');
    }

    public function edit(PetugasKebersihan $petugasKebersihan)
    {
        $kelurahanList = Kelurahan::orderBy('nama')->get();
        return view('data-umum.petugas-kebersihan.edit', compact('petugasKebersihan', 'kelurahanList'));
    }

    public function update(Request $request, PetugasKebersihan $petugasKebersihan)
    {
        $validated = $request->validate([
            'kelurahan_id' => ['required', 'exists:kelurahans,id'],
            'penduduk_id' => ['nullable', 'exists:penduduks,id'],
            'nama' => ['required', 'string', 'max:255'],
            'nik' => ['nullable', 'string', 'max:16'],
            'unit_kerja' => ['nullable', 'string', 'max:255'],
            'jenis_kelamin' => ['nullable', 'string', 'max:20'],
            'pekerjaan' => ['nullable', 'string', 'max:100'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:50'],
        ]);

        $petugasKebersihan->update($validated);

        return redirect()->route('data-umum.petugas-kebersihan.index')
            ->with('success', 'Data petugas kebersihan berhasil diperbarui.');
    }

    public function destroy(PetugasKebersihan $petugasKebersihan)
    {
        $petugasKebersihan->delete();

        return redirect()->route('data-umum.petugas-kebersihan.index')
            ->with('success', 'Data petugas kebersihan berhasil dihapus.');
    }
}
