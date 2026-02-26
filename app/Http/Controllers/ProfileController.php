<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the profile page.
     */
    public function show(Request $request)
    {
        $user = $request->user()->load('role');

        return view('profile.show', compact('user'));
    }

    /**
     * Update profile information.
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $role = $user->getRoleName();

        // Base validation rules for all roles
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
        ];

        // Role-specific validation
        if ($role === Role::ADMIN) {
            $rules['nip'] = ['nullable', 'string', 'max:30'];
            $rules['jabatan'] = ['nullable', 'string', 'max:100'];
        }

        if ($role === Role::RT_RW) {
            $rules['nik'] = ['nullable', 'string', 'max:16'];
        }

        $validated = $request->validate($rules);

        // Only update allowed fields based on role
        $fillable = ['name', 'email', 'phone'];

        if ($role === Role::ADMIN) {
            $fillable = array_merge($fillable, ['nip', 'jabatan']);
        }

        if ($role === Role::RT_RW) {
            $fillable = array_merge($fillable, ['nik']);
        }

        $data = collect($validated)->only($fillable)->toArray();
        $user->update($data);

        return redirect()->route('profile.show')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.current_password' => 'Password lama tidak sesuai.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.min' => 'Password baru minimal 8 karakter.',
        ]);

        $request->user()->update([
            'password' => $validated['password'],
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'Password berhasil diperbarui.');
    }
}
