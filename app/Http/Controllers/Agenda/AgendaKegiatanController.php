<?php

namespace App\Http\Controllers\Agenda;

use App\Http\Controllers\Controller;
use App\Models\AgendaKegiatan;
use App\Models\HasilKegiatan;
use App\Models\Instansi;
use App\Models\Kelurahan;
use Illuminate\Http\Request;

class AgendaKegiatanController extends Controller
{
    public function index(Request $request)
    {
        $query = AgendaKegiatan::with(['kelurahan', 'instansi', 'hasil']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('perihal', 'like', "%{$search}%")
                    ->orWhere('lokasi', 'like', "%{$search}%")
                    ->orWhere('penanggung_jawab', 'like', "%{$search}%");
            });
        }

        if ($bulan = $request->get('bulan')) {
            $query->whereMonth('hari_kegiatan', $bulan);
        }

        if ($tahun = $request->get('tahun')) {
            $query->whereYear('hari_kegiatan', $tahun);
        }

        $agendaList = $query->latest('hari_kegiatan')->paginate(15)->withQueryString();
        $instansiList = Instansi::orderBy('nama')->get();

        return view('agenda.index', compact('agendaList', 'instansiList'));
    }

    public function create()
    {
        $kelurahanList = Kelurahan::orderBy('nama')->get();
        $instansiList = Instansi::orderBy('nama')->get();
        return view('agenda.create', compact('kelurahanList', 'instansiList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelurahan_id' => ['required', 'exists:kelurahans,id'],
            'hari_kegiatan' => ['required', 'date'],
            'jam' => ['nullable', 'string', 'max:20'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'instansi_id' => ['nullable', 'exists:instansis,id'],
            'perihal' => ['required', 'string', 'max:500'],
            'penanggung_jawab' => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
        ]);

        AgendaKegiatan::create($validated);

        return redirect()->route('agenda.index')
            ->with('success', 'Agenda kegiatan berhasil ditambahkan.');
    }

    public function show(AgendaKegiatan $agenda)
    {
        $agenda->load(['kelurahan', 'instansi', 'hasil']);
        return view('agenda.show', compact('agenda'));
    }

    public function edit(AgendaKegiatan $agenda)
    {
        $kelurahanList = Kelurahan::orderBy('nama')->get();
        $instansiList = Instansi::orderBy('nama')->get();
        return view('agenda.edit', compact('agenda', 'kelurahanList', 'instansiList'));
    }

    public function update(Request $request, AgendaKegiatan $agenda)
    {
        $validated = $request->validate([
            'kelurahan_id' => ['required', 'exists:kelurahans,id'],
            'hari_kegiatan' => ['required', 'date'],
            'jam' => ['nullable', 'string', 'max:20'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'instansi_id' => ['nullable', 'exists:instansis,id'],
            'perihal' => ['required', 'string', 'max:500'],
            'penanggung_jawab' => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $agenda->update($validated);

        return redirect()->route('agenda.index')
            ->with('success', 'Agenda kegiatan berhasil diperbarui.');
    }

    public function destroy(AgendaKegiatan $agenda)
    {
        $agenda->delete();

        return redirect()->route('agenda.index')
            ->with('success', 'Agenda kegiatan berhasil dihapus.');
    }

    /**
     * Store hasil kegiatan for an agenda
     */
    public function storeHasil(Request $request, AgendaKegiatan $agenda)
    {
        $validated = $request->validate([
            'hari_tanggal' => ['required', 'date'],
            'notulen' => ['nullable', 'string'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $validated['agenda_id'] = $agenda->id;

        HasilKegiatan::updateOrCreate(
            ['agenda_id' => $agenda->id],
            $validated
        );

        return redirect()->route('agenda.show', $agenda)
            ->with('success', 'Hasil kegiatan berhasil disimpan.');
    }
}
