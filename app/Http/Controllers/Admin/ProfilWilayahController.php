<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelurahan;
use App\Models\Rt;
use App\Models\Rw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilWilayahController extends Controller
{
    // ╔══════════════════════════════════════════════════════════════╗
    // ║  KELURAHAN                                                  ║
    // ╚══════════════════════════════════════════════════════════════╝

    public function kelurahanShow(Kelurahan $kelurahan)
    {
        $kelurahan->load('kecamatan', 'rws.rts');

        $totalRw = $kelurahan->rws->count();
        $totalRt = $kelurahan->rws->sum(fn($rw) => $rw->rts->count());

        return view('profil-wilayah.kelurahan.show', compact('kelurahan', 'totalRw', 'totalRt'));
    }

    public function kelurahanEdit(Kelurahan $kelurahan)
    {
        $kelurahan->load('kecamatan');

        return view('profil-wilayah.kelurahan.edit', compact('kelurahan'));
    }

    public function kelurahanUpdate(Request $request, Kelurahan $kelurahan)
    {
        $validated = $request->validate([
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'kode_pos' => ['nullable', 'string', 'max:10'],
            'luas_area' => ['nullable', 'numeric', 'min:0'],
            'alamat_kantor' => ['nullable', 'string', 'max:255'],
            'no_telp' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'nama_lurah' => ['nullable', 'string', 'max:255'],
            'nip_lurah' => ['nullable', 'string', 'max:30'],
            'visi' => ['nullable', 'string'],
            'misi' => ['nullable', 'string'],
            'deskripsi' => ['nullable', 'string'],
            'batas_utara' => ['nullable', 'string', 'max:255'],
            'batas_selatan' => ['nullable', 'string', 'max:255'],
            'batas_timur' => ['nullable', 'string', 'max:255'],
            'batas_barat' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->hasFile('foto')) {
            // Delete old photo
            if ($kelurahan->foto) {
                Storage::disk('public')->delete($kelurahan->foto);
            }
            $validated['foto'] = $request->file('foto')->store('wilayah/kelurahan', 'public');
        }

        $kelurahan->update($validated);

        return redirect()->route('master.profil-wilayah.kelurahan.show', $kelurahan)
            ->with('success', 'Profil kelurahan berhasil diperbarui.');
    }

    public function kelurahanDeleteFoto(Kelurahan $kelurahan)
    {
        if ($kelurahan->foto) {
            Storage::disk('public')->delete($kelurahan->foto);
            $kelurahan->update(['foto' => null]);
        }

        return back()->with('success', 'Foto kelurahan berhasil dihapus.');
    }

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  RW                                                         ║
    // ╚══════════════════════════════════════════════════════════════╝

    public function rwIndex(Request $request)
    {
        $query = Rw::with('kelurahan', 'rts')->orderBy('nomor');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhere('alamat_sekretariat', 'like', "%{$search}%");
            });
        }

        $rwList = $query->paginate(15)->withQueryString();

        return view('profil-wilayah.rw.index', compact('rwList'));
    }

    public function rwShow(Rw $rw)
    {
        $rw->load('kelurahan', 'rts');

        $totalRt = $rw->rts->count();

        return view('profil-wilayah.rw.show', compact('rw', 'totalRt'));
    }

    public function rwEdit(Rw $rw)
    {
        return view('profil-wilayah.rw.edit', compact('rw'));
    }

    public function rwUpdate(Request $request, Rw $rw)
    {
        $validated = $request->validate([
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'luas_area' => ['nullable', 'numeric', 'min:0'],
            'alamat_sekretariat' => ['nullable', 'string', 'max:255'],
            'no_telp' => ['nullable', 'string', 'max:20'],
            'deskripsi' => ['nullable', 'string'],
            'fasilitas' => ['nullable', 'string'],
            'batas_utara' => ['nullable', 'string', 'max:255'],
            'batas_selatan' => ['nullable', 'string', 'max:255'],
            'batas_timur' => ['nullable', 'string', 'max:255'],
            'batas_barat' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->hasFile('foto')) {
            if ($rw->foto) {
                Storage::disk('public')->delete($rw->foto);
            }
            $validated['foto'] = $request->file('foto')->store('wilayah/rw', 'public');
        }

        $rw->update($validated);

        return redirect()->route('master.profil-wilayah.rw.show', $rw)
            ->with('success', 'Profil RW ' . str_pad($rw->nomor, 3, '0', STR_PAD_LEFT) . ' berhasil diperbarui.');
    }

    public function rwDeleteFoto(Rw $rw)
    {
        if ($rw->foto) {
            Storage::disk('public')->delete($rw->foto);
            $rw->update(['foto' => null]);
        }

        return back()->with('success', 'Foto RW berhasil dihapus.');
    }

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  RT                                                          ║
    // ╚══════════════════════════════════════════════════════════════╝

    public function rtIndex(Request $request)
    {
        $query = Rt::with('rw.kelurahan')->orderBy('rw_id')->orderBy('nomor');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhere('alamat_pos', 'like', "%{$search}%");
            });
        }

        if ($rwId = $request->get('rw_id')) {
            $query->where('rw_id', $rwId);
        }

        $rtList = $query->paginate(15)->withQueryString();
        $rwFilter = Rw::orderBy('nomor')->get();

        return view('profil-wilayah.rt.index', compact('rtList', 'rwFilter'));
    }

    public function rtShow(Rt $rt)
    {
        $rt->load('rw.kelurahan');

        return view('profil-wilayah.rt.show', compact('rt'));
    }

    public function rtEdit(Rt $rt)
    {
        $rt->load('rw');

        return view('profil-wilayah.rt.edit', compact('rt'));
    }

    public function rtUpdate(Request $request, Rt $rt)
    {
        $validated = $request->validate([
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'luas_area' => ['nullable', 'numeric', 'min:0'],
            'alamat_pos' => ['nullable', 'string', 'max:255'],
            'no_telp' => ['nullable', 'string', 'max:20'],
            'deskripsi' => ['nullable', 'string'],
            'fasilitas' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('foto')) {
            if ($rt->foto) {
                Storage::disk('public')->delete($rt->foto);
            }
            $validated['foto'] = $request->file('foto')->store('wilayah/rt', 'public');
        }

        $rt->update($validated);

        return redirect()->route('master.profil-wilayah.rt.show', $rt)
            ->with('success', 'Profil RT ' . str_pad($rt->nomor, 3, '0', STR_PAD_LEFT) . ' / RW ' . str_pad($rt->rw->nomor, 3, '0', STR_PAD_LEFT) . ' berhasil diperbarui.');
    }

    public function rtDeleteFoto(Rt $rt)
    {
        if ($rt->foto) {
            Storage::disk('public')->delete($rt->foto);
            $rt->update(['foto' => null]);
        }

        return back()->with('success', 'Foto RT berhasil dihapus.');
    }
}
