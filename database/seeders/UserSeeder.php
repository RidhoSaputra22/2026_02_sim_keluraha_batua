<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Role::all()->keyBy('name');

        $users = [
            // ─── Admin ───────────────────────────────────────
            [
                'name' => 'Admin Batua',
                'email' => 'admin@batua.com',
                'password' => 'password',
                'role_id' => $roles[Role::ADMIN]->id,
                'phone' => '08114321001',
                'nip' => '199001012020011001',
                'jabatan' => 'Administrator Sistem',
                'is_active' => true,
            ],
            // ─── RT/RW ───────────────────────────────────────
            [
                'name' => 'H. Daeng Mattola',
                'email' => 'rtrw@batua.com',
                'password' => 'password',
                'role_id' => $roles[Role::RT_RW]->id,
                'phone' => '08114321006',
                'nik' => '7371111505700001',
                'jabatan' => 'Ketua RW 001',
                'wilayah_rw' => '001',
                'is_active' => true,
            ],
            // ─── RT/RW 2 ────────────────────────────────────
            [
                'name' => 'Muh. Ridwan',
                'email' => 'rt001@batua.com',
                'password' => 'password',
                'role_id' => $roles[Role::RT_RW]->id,
                'phone' => '08114321007',
                'nik' => '7371111208800002',
                'jabatan' => 'Ketua RT 001/001',
                'wilayah_rt' => '001',
                'wilayah_rw' => '001',
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}
