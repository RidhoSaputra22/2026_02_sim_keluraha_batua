<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Models\SuratJenis;
use Illuminate\Http\Request;

class LaporanPersuratanController extends Controller
{
    public function index(Request $request)
    {
        $totalSurat = Surat::count();
        $totalDraft = Surat::where('status_esign', 'draft')->count();
        $totalProses = Surat::where('status_esign', 'proses')->count();
        $totalSigned = Surat::where('status_esign', 'signed')->count();
        $totalReject = Surat::where('status_esign', 'reject')->count();

        // Per jenis surat
        $perJenis = SuratJenis::withCount('surats')->orderByDesc('surats_count')->get();

        // Per bulan (tahun ini)
        $perBulan = Surat::selectRaw("MONTH(tgl_input) as bulan, count(*) as total")
            ->whereYear('tgl_input', now()->year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->mapWithKeys(function ($item) {
                $namaBulan = \Carbon\Carbon::createFromFormat('m', $item->bulan)->translatedFormat('F');
                return [$namaBulan => $item->total];
            });

        // Per status
        $perStatus = Surat::selectRaw('status_esign, count(*) as total')
            ->whereNotNull('status_esign')
            ->groupBy('status_esign')
            ->get();

        // Surat terbaru
        $suratTerbaru = Surat::with(['jenis', 'pemohon'])
            ->latest('tgl_input')
            ->take(10)
            ->get();

        return view('laporan.persuratan', compact(
            'totalSurat', 'totalDraft', 'totalProses', 'totalSigned', 'totalReject',
            'perJenis', 'perBulan', 'perStatus', 'suratTerbaru'
        ));
    }
}
