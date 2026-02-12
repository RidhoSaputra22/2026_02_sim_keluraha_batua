<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $baseQuery = fn() => Surat::whereHas('pemohon', function ($q) use ($user) {
            $q->where('nama', $user->name);
        });

        $data = [
            'permohonanAktif'   => $baseQuery()->whereNotIn('status_esign', ['signed', 'reject'])->count(),
            'permohonanSelesai'  => $baseQuery()->where('status_esign', 'signed')->count(),
            'permohonanDitolak'  => $baseQuery()->where('status_esign', 'reject')->count(),
            'totalPermohonan'    => $baseQuery()->count(),
        ];

        // Latest 5 permohonan for dashboard table
        $suratTerbaru = $baseQuery()
            ->with(['jenis', 'pemohon'])
            ->latest('tgl_input')
            ->take(5)
            ->get();

        return view('warga.dashboard', array_merge($data, [
            'suratTerbaru' => $suratTerbaru,
        ]));
    }
}
