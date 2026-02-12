<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    /**
     * Tampilkan riwayat semua permohonan warga.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Surat::with(['jenis', 'sifat', 'pemohon'])
            ->whereHas('pemohon', function ($q) use ($user) {
                $q->where('nama', $user->name);
            });

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                  ->orWhere('perihal', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status_esign', $status);
        }

        $surats = $query->latest('tgl_input')->paginate(15)->withQueryString();

        return view('warga.riwayat.index', compact('surats'));
    }
}
