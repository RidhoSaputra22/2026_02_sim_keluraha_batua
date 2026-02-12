<?php

namespace App\Http\Controllers\Survey;

use App\Http\Controllers\Controller;
use App\Models\Kelurahan;
use App\Models\SurveyKepuasan;
use App\Models\SurveyLayanan;
use Illuminate\Http\Request;

class SurveyKepuasanController extends Controller
{
    public function index(Request $request)
    {
        $query = SurveyKepuasan::with(['kelurahan', 'jenisLayanan']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('pekerjaan', 'like', "%{$search}%")
                    ->orWhere('pendidikan', 'like', "%{$search}%");
            });
        }

        if ($layanan = $request->get('jenis_layanan_id')) {
            $query->where('jenis_layanan_id', $layanan);
        }

        $surveyList = $query->latest()->paginate(15)->withQueryString();
        $layananList = SurveyLayanan::orderBy('nama')->get();

        // Statistik ringkasan
        $totalSurvey = SurveyKepuasan::count();
        $rataRata = SurveyKepuasan::avg('nilai_rata_rata');
        $rekapPerLayanan = SurveyLayanan::withCount('surveyKepuasan')
            ->withAvg('surveyKepuasan', 'nilai_rata_rata')
            ->get();

        return view('survey.index', compact(
            'surveyList',
            'layananList',
            'totalSurvey',
            'rataRata',
            'rekapPerLayanan'
        ));
    }

    public function create()
    {
        $kelurahanList = Kelurahan::orderBy('nama')->get();
        $layananList = SurveyLayanan::orderBy('nama')->get();
        return view('survey.create', compact('kelurahanList', 'layananList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelurahan_id' => ['required', 'exists:kelurahans,id'],
            'jenis_kelamin' => ['nullable', 'string', 'max:20'],
            'umur' => ['nullable', 'integer', 'min:0'],
            'pendidikan' => ['nullable', 'string', 'max:50'],
            'pekerjaan' => ['nullable', 'string', 'max:100'],
            'jenis_layanan_id' => ['required', 'exists:survey_layanans,id'],
            'jumlah_nilai' => ['nullable', 'integer', 'min:0'],
            'nilai_rata_rata' => ['nullable', 'numeric', 'min:0', 'max:5'],
        ]);

        SurveyKepuasan::create($validated);

        return redirect()->route('survey.index')
            ->with('success', 'Data survey kepuasan berhasil ditambahkan.');
    }

    public function edit(SurveyKepuasan $survey)
    {
        $survey->load('jenisLayanan');
        $kelurahanList = Kelurahan::orderBy('nama')->get();
        $layananList = SurveyLayanan::orderBy('nama')->get();
        return view('survey.edit', compact('survey', 'kelurahanList', 'layananList'));
    }

    public function update(Request $request, SurveyKepuasan $survey)
    {
        $validated = $request->validate([
            'kelurahan_id' => ['required', 'exists:kelurahans,id'],
            'jenis_kelamin' => ['nullable', 'string', 'max:20'],
            'umur' => ['nullable', 'integer', 'min:0'],
            'pendidikan' => ['nullable', 'string', 'max:50'],
            'pekerjaan' => ['nullable', 'string', 'max:100'],
            'jenis_layanan_id' => ['required', 'exists:survey_layanans,id'],
            'jumlah_nilai' => ['nullable', 'integer', 'min:0'],
            'nilai_rata_rata' => ['nullable', 'numeric', 'min:0', 'max:5'],
        ]);

        $survey->update($validated);

        return redirect()->route('survey.index')
            ->with('success', 'Data survey kepuasan berhasil diperbarui.');
    }

    public function destroy(SurveyKepuasan $survey)
    {
        $survey->delete();

        return redirect()->route('survey.index')
            ->with('success', 'Data survey kepuasan berhasil dihapus.');
    }
}
