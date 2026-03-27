<?php

namespace App\Http\Controllers\Admin\Master;

use App\Enums\StatusAktifEnum;
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
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PengurusController extends Controller
{
    public function index(Request $request)
    {
        $query = RtRwPengurus::with(['penduduk', 'jabatan', 'rw', 'rt', 'user']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('penduduk', function ($qp) use ($search) {
                    $qp->where('nama', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%");
                })
                    ->orWhere('no_telp', 'like', "%{$search}%");
            });
        }

        if ($jabatanId = $request->get('jabatan')) {
            $query->where('jabatan_id', $jabatanId);
        }

        if ($rwId = $request->get('rw')) {
            $query->where('rw_id', $rwId);
        }

        if ($status = $request->get('status')) {
            $query->whereRaw('LOWER(status) = ?', [strtolower($status)]);
        }

        $pengurus = $query->orderBy('rw_id')->orderBy('rt_id')->paginate(15)->withQueryString();

        $rwList = Rw::orderBy('nomor')->get();
        $jabatanList = JabatanRtRw::orderBy('nama')->get();

        $totalRT = Rt::count();
        $totalRW = Rw::count();
        $totalAktif = RtRwPengurus::whereRaw('LOWER(status) = ?', [StatusAktifEnum::AKTIF->value])->count();
        $totalNonaktif = RtRwPengurus::whereRaw('LOWER(status) = ?', [StatusAktifEnum::NONAKTIF->value])->count();

        return view('master.pengurus.index', compact('pengurus', 'rwList', 'jabatanList', 'totalRT', 'totalRW', 'totalAktif', 'totalNonaktif'));
    }

    public function create()
    {
        $pendudukList = Penduduk::orderBy('nama')->get();
        $jabatanList = JabatanRtRw::orderBy('nama')->get();
        $rwList = Rw::orderBy('nomor')->get();
        $rtList = Rt::with('rw')->orderBy('rw_id')->orderBy('nomor')->get();
        $kelurahanList = Kelurahan::orderBy('nama')->get();

        $assignedUserIds = RtRwPengurus::whereNotNull('user_id')->pluck('user_id');
        $rtRwUserList = User::whereHas('role', fn($q) => $q->where('name', Role::RT_RW))
            ->whereNotIn('id', $assignedUserIds)
            ->orderBy('name')->get();

        return view('master.pengurus.create', compact('pendudukList', 'jabatanList', 'rwList', 'rtList', 'kelurahanList', 'rtRwUserList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelurahan_id' => ['required', 'exists:kelurahans,id'],
            'penduduk_id' => ['required', 'exists:penduduks,id'],
            'jabatan_id' => ['required', 'exists:jabatan_rt_rw,id'],
            'rw_id' => ['nullable', 'exists:rws,id'],
            'rt_id' => ['nullable', 'exists:rts,id'],
            'tgl_mulai' => ['nullable', 'date'],
            'status' => ['required', Rule::in(StatusAktifEnum::values())],
            'alamat' => ['nullable', 'string'],
            'no_telp' => ['nullable', 'string', 'max:20'],
            'no_rekening' => ['nullable', 'string', 'max:30'],
            'no_npwp' => ['nullable', 'string', 'max:30'],
            'assign_user_mode' => ['nullable', 'in:none,existing,create_new'],
            'user_id' => ['nullable', 'exists:users,id', 'unique:rt_rw_pengurus,user_id'],
            'new_user_email' => ['nullable', 'required_if:assign_user_mode,create_new', 'email', 'unique:users,email', 'max:255'],
        ]);

        DB::transaction(function () use ($request, $validated) {
            $userId = null;
            $mode = $request->input('assign_user_mode', 'none');

            if ($mode === 'existing' && $request->filled('user_id')) {
                $userId = $validated['user_id'];
                $this->syncUserWilayah($userId, $validated['rw_id'] ?? null, $validated['rt_id'] ?? null);
            } elseif ($mode === 'create_new' && $request->filled('new_user_email')) {
                $penduduk = Penduduk::findOrFail($validated['penduduk_id']);
                $rtRwRole = Role::where('name', Role::RT_RW)->first();
                $rawPassword = Str::random(10);
                $user = User::create([
                    'name' => $penduduk->nama,
                    'email' => $request->input('new_user_email'),
                    'password' => $rawPassword,
                    'role_id' => $rtRwRole?->id,
                    'nik' => $penduduk->nik,
                    'phone' => $request->input('no_telp'),
                    'is_active' => true,
                ]);
                $this->syncUserWilayah($user->id, $validated['rw_id'] ?? null, $validated['rt_id'] ?? null);
                $userId = $user->id;
                session()->flash('new_user_credential', "Akun berhasil dibuat. Email: {$user->email} | Password sementara: {$rawPassword}");
            }

            $validated['user_id'] = $userId;
            unset($validated['assign_user_mode'], $validated['new_user_email']);
            RtRwPengurus::create($validated);
        });

        return redirect()->route('master.pengurus.index')
            ->with('success', 'Data pengurus RT/RW berhasil ditambahkan.');
    }

    public function edit(RtRwPengurus $penguru)
    {
        $penguru->load(['penduduk', 'jabatan', 'rw', 'rt', 'user']);
        $pendudukList = Penduduk::orderBy('nama')->get();
        $jabatanList = JabatanRtRw::orderBy('nama')->get();
        $rwList = Rw::orderBy('nomor')->get();
        $rtList = Rt::with('rw')->orderBy('rw_id')->orderBy('nomor')->get();
        $kelurahanList = Kelurahan::orderBy('nama')->get();

        $assignedUserIds = RtRwPengurus::whereNotNull('user_id')
            ->where('id', '!=', $penguru->id)
            ->pluck('user_id');
        $rtRwUserList = User::whereHas('role', fn($q) => $q->where('name', Role::RT_RW))
            ->whereNotIn('id', $assignedUserIds)
            ->orderBy('name')->get();

        return view('master.pengurus.edit', compact('penguru', 'pendudukList', 'jabatanList', 'rwList', 'rtList', 'kelurahanList', 'rtRwUserList'));
    }

    public function update(Request $request, RtRwPengurus $penguru)
    {
        $validated = $request->validate([
            'kelurahan_id' => ['required', 'exists:kelurahans,id'],
            'penduduk_id' => ['required', 'exists:penduduks,id'],
            'jabatan_id' => ['required', 'exists:jabatan_rt_rw,id'],
            'rw_id' => ['nullable', 'exists:rws,id'],
            'rt_id' => ['nullable', 'exists:rts,id'],
            'tgl_mulai' => ['nullable', 'date'],
            'status' => ['required', Rule::in(StatusAktifEnum::values())],
            'alamat' => ['nullable', 'string'],
            'no_telp' => ['nullable', 'string', 'max:20'],
            'no_rekening' => ['nullable', 'string', 'max:30'],
            'no_npwp' => ['nullable', 'string', 'max:30'],
            'assign_user_mode' => ['nullable', 'in:none,existing,create_new'],
            'user_id' => ['nullable', 'exists:users,id', 'unique:rt_rw_pengurus,user_id,' . $penguru->id],
            'new_user_email' => ['nullable', 'required_if:assign_user_mode,create_new', 'email', 'max:255',
                'unique:users,email,' . ($penguru->user_id ?? 'NULL') . ',id'],
        ]);

        $previousUserId = $penguru->user_id;

        DB::transaction(function () use ($request, $validated, $penguru, $previousUserId) {
            $mode = $request->input('assign_user_mode', 'none');

            if ($mode === 'none') {
                $validated['user_id'] = null;
            } elseif ($mode === 'existing' && $request->filled('user_id')) {
                $validated['user_id'] = $validated['user_id'];
                $this->syncUserWilayah($validated['user_id'], $validated['rw_id'] ?? null, $validated['rt_id'] ?? null);
            } elseif ($mode === 'create_new' && $request->filled('new_user_email')) {
                $penduduk = Penduduk::findOrFail($validated['penduduk_id']);
                $rtRwRole = Role::where('name', Role::RT_RW)->first();
                $rawPassword = Str::random(10);
                $user = User::create([
                    'name' => $penduduk->nama,
                    'email' => $request->input('new_user_email'),
                    'password' => $rawPassword,
                    'role_id' => $rtRwRole?->id,
                    'nik' => $penduduk->nik,
                    'phone' => $request->input('no_telp'),
                    'is_active' => true,
                ]);
                $this->syncUserWilayah($user->id, $validated['rw_id'] ?? null, $validated['rt_id'] ?? null);
                $validated['user_id'] = $user->id;
                session()->flash('new_user_credential', "Akun berhasil dibuat. Email: {$user->email} | Password sementara: {$rawPassword}");
            }

            unset($validated['assign_user_mode'], $validated['new_user_email']);
            $penguru->update($validated);

            if ($previousUserId && $previousUserId !== $penguru->user_id) {
                $this->clearUserWilayah($previousUserId);
            }
        });

        return redirect()->route('master.pengurus.index')
            ->with('success', 'Data pengurus RT/RW berhasil diperbarui.');
    }

    public function destroy(RtRwPengurus $penguru)
    {
        $userId = $penguru->user_id;
        $penguru->delete();

        if ($userId) {
            $this->clearUserWilayah($userId);
        }

        return redirect()->route('master.pengurus.index')
            ->with('success', 'Data pengurus RT/RW berhasil dihapus.');
    }

    // ─── Private Helpers ─────────────────────────────────────────────────────

    private function syncUserWilayah(int $userId, ?int $rwId, ?int $rtId): void
    {
        $rw = $rwId ? Rw::find($rwId) : null;
        $rt = $rtId ? Rt::find($rtId) : null;

        User::where('id', $userId)->update([
            'wilayah_rw' => $rw?->nomor,
            'wilayah_rt' => $rt?->nomor,
        ]);
    }

    private function clearUserWilayah(int $userId): void
    {
        User::where('id', $userId)->update([
            'wilayah_rw' => null,
            'wilayah_rt' => null,
        ]);
    }
}
