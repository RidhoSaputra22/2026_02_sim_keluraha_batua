<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JabatanRtRw;
use App\Models\Kelurahan;
use App\Models\Penduduk;
use App\Models\Role;
use App\Models\Rt;
use App\Models\RtRwPengurus;
use App\Models\Rw;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RwController extends Controller
{
    public function index(Request $request)
    {
        $query = Rw::with('kelurahan', 'rts', 'petaPolygon')->orderBy('nomor');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhere('alamat_sekretariat', 'like', "%{$search}%");
            });
        }

        if ($kelurahanId = $request->get('kelurahan_id')) {
            $query->where('kelurahan_id', $kelurahanId);
        }

        $rwList = $query->paginate(15)->withQueryString();
        $kelurahanFilter = Kelurahan::orderBy('nama')->get();
        $totalRw = Rw::count();
        $totalRt = Rt::count();

        return view('master.rw.index', compact('rwList', 'kelurahanFilter', 'totalRw', 'totalRt'));
    }

    public function create()
    {
        $kelurahanList = Kelurahan::orderBy('nama')->get();

        return view('master.rw.create', compact('kelurahanList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelurahan_id' => ['required', 'exists:kelurahans,id'],
            'nomor' => ['required', 'integer', 'min:1'],
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

        // Unique check: nomor per kelurahan
        $exists = Rw::where('kelurahan_id', $validated['kelurahan_id'])
            ->where('nomor', $validated['nomor'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors(['nomor' => 'Nomor RW sudah ada di kelurahan ini.']);
        }

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('wilayah/rw', 'public');
        }

        Rw::create($validated);

        return redirect()->route('master.rw.index')
            ->with('success', 'RW ' . str_pad($validated['nomor'], 3, '0', STR_PAD_LEFT) . ' berhasil ditambahkan.');
    }

    public function show(Rw $rw)
    {
        $rw->load('kelurahan', 'rts', 'pengurus.penduduk', 'pengurus.jabatan', 'pengurus.user', 'petaPolygon');
        $totalRt = $rw->rts->count();

        $pendudukList = Penduduk::orderBy('nama')->get(['id', 'nik', 'nama']);
        $jabatanList  = JabatanRtRw::orderBy('nama')->get(['id', 'nama']);
        $kelurahanList = Kelurahan::orderBy('nama')->get(['id', 'nama']);
        $rtRwRole     = Role::where('name', Role::RT_RW)->first();
        $rtRwUserList = $rtRwRole
            ? User::where('role_id', $rtRwRole->id)->orderBy('name')->get(['id', 'name', 'email'])
            : collect();

        return view('master.rw.show', compact(
            'rw', 'totalRt', 'pendudukList', 'jabatanList', 'kelurahanList', 'rtRwUserList'
        ));
    }

    public function edit(Rw $rw)
    {
        $rw->load('kelurahan');
        $kelurahanList = Kelurahan::orderBy('nama')->get();

        return view('master.rw.edit', compact('rw', 'kelurahanList'));
    }

    public function update(Request $request, Rw $rw)
    {
        $validated = $request->validate([
            'nomor' => ['required', 'integer', 'min:1'],
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

        // Unique check: nomor per kelurahan (exclude self)
        $exists = Rw::where('kelurahan_id', $rw->kelurahan_id)
            ->where('nomor', $validated['nomor'])
            ->where('id', '!=', $rw->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors(['nomor' => 'Nomor RW sudah ada di kelurahan ini.']);
        }

        if ($request->hasFile('foto')) {
            if ($rw->foto) {
                Storage::disk('public')->delete($rw->foto);
            }
            $validated['foto'] = $request->file('foto')->store('wilayah/rw', 'public');
        }

        $rw->update($validated);

        return redirect()->route('master.rw.show', $rw)
            ->with('success', 'Data RW ' . str_pad($rw->nomor, 3, '0', STR_PAD_LEFT) . ' berhasil diperbarui.');
    }

    public function destroy(Rw $rw)
    {
        if ($rw->rts()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus RW yang masih memiliki RT. Hapus semua RT terlebih dahulu.');
        }

        if ($rw->foto) {
            Storage::disk('public')->delete($rw->foto);
        }

        $nomor = str_pad($rw->nomor, 3, '0', STR_PAD_LEFT);
        $rw->delete();

        return redirect()->route('master.rw.index')
            ->with('success', 'RW ' . $nomor . ' berhasil dihapus.');
    }

    public function deleteFoto(Rw $rw)
    {
        if ($rw->foto) {
            Storage::disk('public')->delete($rw->foto);
            $rw->update(['foto' => null]);
        }

        return back()->with('success', 'Foto RW berhasil dihapus.');
    }

    // ─── PENGURUS CRUD (nested in RW) ─────────────────────────────

    public function storePengurus(Request $request, Rw $rw)
    {
        $validated = $request->validate([
            'penduduk_id'      => ['required', 'exists:penduduks,id'],
            'jabatan_id'       => ['required', 'exists:jabatan_rt_rw,id'],
            'tgl_mulai'        => ['nullable', 'date'],
            'status'           => ['required', 'in:aktif,nonaktif'],
            'alamat'           => ['nullable', 'string'],
            'no_telp'          => ['nullable', 'string', 'max:20'],
            'no_rekening'      => ['nullable', 'string', 'max:30'],
            'no_npwp'          => ['nullable', 'string', 'max:30'],
            'assign_user_mode' => ['nullable', 'in:none,existing,create_new'],
            'user_id'          => ['nullable', 'exists:users,id', 'unique:rt_rw_pengurus,user_id'],
            'new_user_email'   => ['nullable', 'required_if:assign_user_mode,create_new', 'email', 'unique:users,email', 'max:255'],
        ]);

        $validated['rw_id'] = $rw->id;
        $validated['rt_id'] = null;
        $validated['kelurahan_id'] = $rw->kelurahan_id;

        DB::transaction(function () use ($request, $validated) {
            $userId = null;
            $mode = $request->input('assign_user_mode', 'none');

            if ($mode === 'existing' && $request->filled('user_id')) {
                $userId = $validated['user_id'];
                $this->syncUserWilayah($userId, $validated['rw_id'], null);
            } elseif ($mode === 'create_new' && $request->filled('new_user_email')) {
                $penduduk = Penduduk::findOrFail($validated['penduduk_id']);
                $rtRwRole = Role::where('name', Role::RT_RW)->first();
                $rawPassword = Str::random(10);
                $user = User::create([
                    'name'      => $penduduk->nama,
                    'email'     => $request->input('new_user_email'),
                    'password'  => $rawPassword,
                    'role_id'   => $rtRwRole?->id,
                    'nik'       => $penduduk->nik,
                    'phone'     => $request->input('no_telp'),
                    'is_active' => true,
                ]);
                $this->syncUserWilayah($user->id, $validated['rw_id'], null);
                $userId = $user->id;
                session()->flash('new_user_credential', "Akun berhasil dibuat. Email: {$user->email} | Password sementara: {$rawPassword}");
            }

            $validated['user_id'] = $userId;
            unset($validated['assign_user_mode'], $validated['new_user_email']);
            RtRwPengurus::create($validated);
        });

        return redirect()->route('master.rw.show', $rw)
            ->with('success', 'Pengurus berhasil ditambahkan.');
    }

    public function updatePengurus(Request $request, Rw $rw, RtRwPengurus $penguru)
    {
        $validated = $request->validate([
            'penduduk_id'      => ['required', 'exists:penduduks,id'],
            'jabatan_id'       => ['required', 'exists:jabatan_rt_rw,id'],
            'tgl_mulai'        => ['nullable', 'date'],
            'status'           => ['required', 'in:aktif,nonaktif'],
            'alamat'           => ['nullable', 'string'],
            'no_telp'          => ['nullable', 'string', 'max:20'],
            'no_rekening'      => ['nullable', 'string', 'max:30'],
            'no_npwp'          => ['nullable', 'string', 'max:30'],
            'assign_user_mode' => ['nullable', 'in:none,existing,create_new'],
            'user_id'          => ['nullable', 'exists:users,id', 'unique:rt_rw_pengurus,user_id,' . $penguru->id],
            'new_user_email'   => ['nullable', 'required_if:assign_user_mode,create_new', 'email', 'max:255',
                'unique:users,email,' . ($penguru->user_id ?? 'NULL') . ',id'],
        ]);

        DB::transaction(function () use ($request, $validated, $penguru, $rw) {
            $mode = $request->input('assign_user_mode', 'none');

            if ($mode === 'none') {
                $validated['user_id'] = null;
            } elseif ($mode === 'existing' && $request->filled('user_id')) {
                $validated['user_id'] = $validated['user_id'];
                $this->syncUserWilayah($validated['user_id'], $rw->id, null);
            } elseif ($mode === 'create_new' && $request->filled('new_user_email')) {
                $penduduk = Penduduk::findOrFail($validated['penduduk_id']);
                $rtRwRole = Role::where('name', Role::RT_RW)->first();
                $rawPassword = Str::random(10);
                $user = User::create([
                    'name'      => $penduduk->nama,
                    'email'     => $request->input('new_user_email'),
                    'password'  => $rawPassword,
                    'role_id'   => $rtRwRole?->id,
                    'nik'       => $penduduk->nik,
                    'phone'     => $request->input('no_telp'),
                    'is_active' => true,
                ]);
                $this->syncUserWilayah($user->id, $rw->id, null);
                $validated['user_id'] = $user->id;
                session()->flash('new_user_credential', "Akun berhasil dibuat. Email: {$user->email} | Password sementara: {$rawPassword}");
            }

            unset($validated['assign_user_mode'], $validated['new_user_email']);
            $penguru->update($validated);
        });

        return redirect()->route('master.rw.show', $rw)
            ->with('success', 'Data pengurus berhasil diperbarui.');
    }

    public function destroyPengurus(Rw $rw, RtRwPengurus $penguru)
    {
        $penguru->delete();

        return redirect()->route('master.rw.show', $rw)
            ->with('success', 'Pengurus berhasil dihapus.');
    }

    // ─── Private Helpers ──────────────────────────────────────────

    private function syncUserWilayah(int $userId, ?int $rwId, ?int $rtId): void
    {
        $rw = $rwId ? Rw::find($rwId) : null;
        $rt = $rtId ? Rt::find($rtId) : null;

        User::where('id', $userId)->update([
            'wilayah_rw' => $rw?->nomor,
            'wilayah_rt' => $rt?->nomor,
        ]);
    }
}
