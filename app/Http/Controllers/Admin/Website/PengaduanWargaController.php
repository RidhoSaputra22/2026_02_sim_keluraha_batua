<?php

namespace App\Http\Controllers\Admin\Website;

use App\Http\Controllers\Controller;
use App\Models\PengaduanWarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PengaduanWargaController extends Controller
{
    public function index(Request $request)
    {
        $query = PengaduanWarga::query()->latest();

        if ($search = trim((string) $request->get('search'))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('kode_pengaduan', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%")
                    ->orWhere('subjek', 'like', "%{$search}%");
            });
        }

        if ($kategori = $request->get('kategori')) {
            $query->where('kategori', $kategori);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $pengaduanWarga = $query->paginate(12)->withQueryString();

        return view('admin.website.pengaduan-warga.index', compact('pengaduanWarga'));
    }

    public function show(PengaduanWarga $pengaduanWarga)
    {
        return view('admin.website.pengaduan-warga.show', compact('pengaduanWarga'));
    }

    public function update(Request $request, PengaduanWarga $pengaduanWarga)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(PengaduanWarga::statusOptions()))],
            'catatan_admin' => ['nullable', 'string'],
        ]);

        $pengaduanWarga->update($validated);
        $pengaduanWarga->markAsHandledIfNeeded();

        return redirect()->route('admin.website.pengaduan-warga.show', $pengaduanWarga)
            ->with('success', 'Status pengaduan berhasil diperbarui.');
    }

    public function destroy(PengaduanWarga $pengaduanWarga)
    {
        if ($pengaduanWarga->lampiran) {
            Storage::disk('public')->delete($pengaduanWarga->lampiran);
        }

        $pengaduanWarga->delete();

        return redirect()->route('admin.website.pengaduan-warga.index')
            ->with('success', 'Pengaduan warga berhasil dihapus.');
    }
}
