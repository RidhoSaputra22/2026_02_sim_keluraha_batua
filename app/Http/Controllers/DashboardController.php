<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Redirect user to their role-specific dashboard.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        return match ($user->getRoleName()) {
            Role::ADMIN         => redirect()->route('admin.dashboard'),
            Role::OPERATOR      => redirect()->route('operator.dashboard'),
            Role::VERIFIKATOR   => redirect()->route('verifikator.dashboard'),
            Role::PENANDATANGAN => redirect()->route('penandatangan.dashboard'),
            Role::RT_RW         => redirect()->route('rtrw.dashboard'),
            Role::WARGA         => redirect()->route('warga.dashboard'),
            default             => abort(403, 'Role tidak dikenali.'),
        };
    }
}
