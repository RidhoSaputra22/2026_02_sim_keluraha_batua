<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Kelurahan;
use App\Models\Pemohon;
use App\Models\Penduduk;
use App\Models\Surat;
use App\Models\SuratJenis;
use App\Models\SuratSifat;
use Illuminate\Http\Request;

class PermohonanController extends Controller
{
    /**
     * Tampilkan daftar permohonan milik warga yang login.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Surat::with(['jenis', 'sifat', 'pemohon'])
            ->whereHas('pemohon', function ($q) use ($user) {
                $q->where('nama', $user->name);
            });

        if ($status = $request->get('status')) {
            $query->where('status_esign', $status);
        }

        $surats = $query->latest('tgl_input')->paginate(10)->withQueryString();

        return view('warga.permohonan.index', compact('surats'));
    }

    /**
     * Form ajukan permohonan surat baru.
     */
    public function create()
    {
        $jenisList = SuratJenis::orderBy('nama')->get();
        $sifatList = SuratSifat::orderBy('nama')->get();
        $kelurahan = Kelurahan::first(); // default kelurahan Batua

        return view('warga.permohonan.create', compact('jenisList', 'sifatList', 'kelurahan'));
    }

    /**
     * Simpan permohonan baru dari warga.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_id'    => ['required', 'exists:surat_jenis,id'],
            'perihal'     => ['required', 'string', 'max:255'],
            'uraian'      => ['nullable', 'string'],
            'no_hp'       => ['nullable', 'string', 'max:20'],
        ]);

        $user = $request->user();
        $kelurahan = Kelurahan::first();

        // Create pemohon record from user data
        $pemohon = Pemohon::create([
            'penduduk_id' => $user->nik ? Penduduk::where('nik', $user->nik)->value('id') : null,
            'nama'        => $user->name,
            'no_hp_wa'    => $validated['no_hp'] ?? $user->phone,
            'email'       => $user->email,
        ]);

        // Generate draft number
        $nomorUrut = Surat::whereYear('tgl_input', now()->year)->count() + 1;
        $nomorSurat = sprintf('DRAFT-%03d/%s/%s', $nomorUrut, now()->format('m'), now()->format('Y'));

        Surat::create([
            'kelurahan_id'     => $kelurahan?->id,
            'arah'             => 'keluar',
            'nomor_surat'      => $nomorSurat,
            'tanggal_surat'    => now()->toDateString(),
            'jenis_id'         => $validated['jenis_id'],
            'perihal'          => $validated['perihal'],
            'uraian'           => $validated['uraian'] ?? null,
            'pemohon_id'       => $pemohon->id,
            'status_esign'     => 'draft',
            'tgl_input'        => now(),
            'petugas_input_id' => $user->id,
        ]);

        return redirect()->route('warga.permohonan.index')
            ->with('success', 'Permohonan surat berhasil diajukan. Silakan pantau status melalui halaman riwayat.');
    }

    /**
     * Detail / tracking satu permohonan.
     */
    public function show(Surat $permohonan)
    {
        $user = request()->user();

        // Ensure warga can only see own permohonan
        $permohonan->load(['jenis', 'sifat', 'pemohon.penduduk', 'petugasInput', 'verifikator', 'penandatanganPejabat']);

        if ($permohonan->pemohon?->nama !== $user->name) {
            abort(403);
        }

        return view('warga.permohonan.show', compact('permohonan'));
    }
}
