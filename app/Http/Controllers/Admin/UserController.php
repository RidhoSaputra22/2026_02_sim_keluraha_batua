<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role');

        // Search
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($roleId = $request->get('role_id')) {
            $query->where('role_id', $roleId);
        }

        // Filter by status
        if ($request->has('is_active') && $request->get('is_active') !== '') {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $roles = Role::where('is_active', true)->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::where('is_active', true)->get();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'unique:users,email'],
            'password'   => ['required', Password::min(8)],
            'role_id'    => ['required', 'exists:roles,id'],
            'phone'      => ['nullable', 'string', 'max:20'],
            'nip'        => ['nullable', 'string', 'max:30'],
            'nik'        => ['nullable', 'string', 'max:20'],
            'jabatan'    => ['nullable', 'string', 'max:100'],
            'wilayah_rt' => ['nullable', 'string', 'max:5'],
            'wilayah_rw' => ['nullable', 'string', 'max:5'],
            'is_active'  => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $roles = Role::where('is_active', true)->get();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password'   => ['nullable', Password::min(8)],
            'role_id'    => ['required', 'exists:roles,id'],
            'phone'      => ['nullable', 'string', 'max:20'],
            'nip'        => ['nullable', 'string', 'max:30'],
            'nik'        => ['nullable', 'string', 'max:20'],
            'jabatan'    => ['nullable', 'string', 'max:100'],
            'wilayah_rt' => ['nullable', 'string', 'max:5'],
            'wilayah_rw' => ['nullable', 'string', 'max:5'],
            'is_active'  => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        // Remove password if empty
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }

    /**
     * Toggle user active status.
     */
    public function toggleActive(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Pengguna berhasil {$status}.");
    }
}
