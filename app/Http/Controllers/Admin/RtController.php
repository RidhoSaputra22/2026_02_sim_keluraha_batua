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

class RtController extends Controller
{
    public function index(Request $request)
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
        $rwFilter = Rw::with('kelurahan')->orderBy('nomor')->get();
        $totalRt = Rt::count();

        return view('master.rt.index', compact('rtList', 'rwFilter', 'totalRt'));
    }

    public function create(Request $request)
    {
        $rwList = Rw::with('kelurahan')->orderBy('nomor')->get();
        $selectedRw = $request->get('rw_id');

        return view('master.rt.create', compact('rwList', 'selectedRw'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rw_id' => ['required', 'exists:rws,id'],
            'nomor' => ['required', 'integer', 'min:1'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'luas_area' => ['nullable', 'numeric', 'min:0'],
            'alamat_pos' => ['nullable', 'string', 'max:255'],
            'no_telp' => ['nullable', 'string', 'max:20'],
            'deskripsi' => ['nullable', 'string'],
            'fasilitas' => ['nullable', 'string'],
        ]);

        // Unique check: nomor per RW
        $exists = Rt::where('rw_id', $validated['rw_id'])
            ->where('nomor', $validated['nomor'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors(['nomor' => 'Nomor RT sudah ada di RW ini.']);
        }

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('wilayah/rt', 'public');
        }

        Rt::create($validated);

        return redirect()->route('master.rt.index')
            ->with('success', 'RT ' . str_pad($validated['nomor'], 3, '0', STR_PAD_LEFT) . ' berhasil ditambahkan.');
    }

    public function show(Rt $rt)
    {
        $rt->load(['rw.kelurahan', 'pengurus.penduduk', 'pengurus.jabatan', 'pengurus.user']);

        $pendudukList = Penduduk::orderBy('nama')->get();
        $jabatanList = JabatanRtRw::orderBy('nama')->get();
        $kelurahanList = Kelurahan::orderBy('nama')->get();

        $assignedUserIds = RtRwPengurus::whereNotNull('user_id')->pluck('user_id');
        $rtRwUserList = User::whereHas('role', fn($q) => $q->where('name', Role::RT_RW))
            ->whereNotIn('id', $assignedUserIds)
            ->orderBy('name')->get();

        return view('master.rt.show', compact('rt', 'pendudukList', 'jabatanList', 'kelurahanList', 'rtRwUserList'));
    }

    public function edit(Rt $rt)
    {
        $rt->load('rw.kelurahan');
        $rwList = Rw::with('kelurahan')->orderBy('nomor')->get();

        return view('master.rt.edit', compact('rt', 'rwList'));
    }

    public function update(Request $request, Rt $rt)
    {
        $validated = $request->validate([
            'rw_id' => ['required', 'exists:rws,id'],
            'nomor' => ['required', 'integer', 'min:1'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'luas_area' => ['nullable', 'numeric', 'min:0'],
            'alamat_pos' => ['nullable', 'string', 'max:255'],
            'no_telp' => ['nullable', 'string', 'max:20'],
            'deskripsi' => ['nullable', 'string'],
            'fasilitas' => ['nullable', 'string'],
        ]);

        // Unique check: nomor per RW (exclude self)
        $exists = Rt::where('rw_id', $validated['rw_id'])
            ->where('nomor', $validated['nomor'])
            ->where('id', '!=', $rt->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors(['nomor' => 'Nomor RT sudah ada di RW ini.']);
        }

        if ($request->hasFile('foto')) {
            if ($rt->foto) {
                Storage::disk('public')->delete($rt->foto);
            }
            $validated['foto'] = $request->file('foto')->store('wilayah/rt', 'public');
        }

        $rt->update($validated);

        return redirect()->route('master.rt.show', $rt)
            ->with('success', 'Data RT ' . str_pad($rt->nomor, 3, '0', STR_PAD_LEFT) . ' berhasil diperbarui.');
    }

    public function destroy(Rt $rt)
    {
        // Check for related data
        if ($rt->penduduks()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus RT yang masih memiliki data penduduk.');
        }

        if ($rt->foto) {
            Storage::disk('public')->delete($rt->foto);
        }

        $nomor = str_pad($rt->nomor, 3, '0', STR_PAD_LEFT);
        $rt->delete();

        return redirect()->route('master.rt.index')
            ->with('success', 'RT ' . $nomor . ' berhasil dihapus.');
    }

    public function deleteFoto(Rt $rt)
    {
        if ($rt->foto) {
            Storage::disk('public')->delete($rt->foto);
            $rt->update(['foto' => null]);
        }

        return back()->with('success', 'Foto RT berhasil dihapus.');
    }

    // ─── PENGURUS CRUD (nested in RT) ─────────────────────────────

    public function storePengurus(Request $request, Rt $rt)
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

        $validated['rt_id'] = $rt->id;
        $validated['rw_id'] = $rt->rw_id;
        $validated['kelurahan_id'] = $rt->rw->kelurahan_id;

        DB::transaction(function () use ($request, $validated) {
            $userId = null;
            $mode = $request->input('assign_user_mode', 'none');

            if ($mode === 'existing' && $request->filled('user_id')) {
                $userId = $validated['user_id'];
                $this->syncUserWilayah($userId, $validated['rw_id'], $validated['rt_id']);
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
                $this->syncUserWilayah($user->id, $validated['rw_id'], $validated['rt_id']);
                $userId = $user->id;
                session()->flash('new_user_credential', "Akun berhasil dibuat. Email: {$user->email} | Password sementara: {$rawPassword}");
            }

            $validated['user_id'] = $userId;
            unset($validated['assign_user_mode'], $validated['new_user_email']);
            RtRwPengurus::create($validated);
        });

        return redirect()->route('master.rt.show', $rt)
            ->with('success', 'Pengurus berhasil ditambahkan.');
    }

    public function updatePengurus(Request $request, Rt $rt, RtRwPengurus $penguru)
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

        DB::transaction(function () use ($request, $validated, $penguru) {
            $mode = $request->input('assign_user_mode', 'none');

            if ($mode === 'none') {
                $validated['user_id'] = null;
            } elseif ($mode === 'existing' && $request->filled('user_id')) {
                $validated['user_id'] = $validated['user_id'];
                $this->syncUserWilayah($validated['user_id'], $penguru->rw_id, $penguru->rt_id);
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
                $this->syncUserWilayah($user->id, $penguru->rw_id, $penguru->rt_id);
                $validated['user_id'] = $user->id;
                session()->flash('new_user_credential', "Akun berhasil dibuat. Email: {$user->email} | Password sementara: {$rawPassword}");
            }

            unset($validated['assign_user_mode'], $validated['new_user_email']);
            $penguru->update($validated);
        });

        return redirect()->route('master.rt.show', $rt)
            ->with('success', 'Data pengurus berhasil diperbarui.');
    }

    public function destroyPengurus(Rt $rt, RtRwPengurus $penguru)
    {
        $penguru->delete();

        return redirect()->route('master.rt.show', $rt)
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
