<?php

namespace App\Http\Controllers\Admin\Access;

use App\Http\Controllers\Controller;
use App\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->get();

        return view('admin.roles.index', compact('roles'));
    }
}
