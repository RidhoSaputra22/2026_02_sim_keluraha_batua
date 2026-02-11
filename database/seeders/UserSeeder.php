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
                'name'       => 'Admin Batua',
                'email'      => 'admin@batua.com',
                'password'   => 'password',
                'role_id'    => $roles[Role::ADMIN]->id,
                'phone'      => '08114321001',
                'nip'        => '199001012020011001',
                'jabatan'    => 'Administrator Sistem',
                'is_active'  => true,
            ],
            // ─── Operator 1 ──────────────────────────────────
            [
                'name'       => 'Siti Rahmawati',
                'email'      => 'operator@batua.com',
                'password'   => 'password',
                'role_id'    => $roles[Role::OPERATOR]->id,
                'phone'      => '08114321002',
                'nip'        => '199205152021012001',
                'jabatan'    => 'Operator Kelurahan',
                'is_active'  => true,
            ],
            // ─── Operator 2 ──────────────────────────────────
            [
                'name'       => 'Ahmad Fauzi',
                'email'      => 'operator2@batua.com',
                'password'   => 'password',
                'role_id'    => $roles[Role::OPERATOR]->id,
                'phone'      => '08114321003',
                'nip'        => '199308202021011002',
                'jabatan'    => 'Operator Kelurahan',
                'is_active'  => true,
            ],
            // ─── Verifikator ─────────────────────────────────
            [
                'name'       => 'Hj. Nurjannah, S.Sos',
                'email'      => 'verifikator@batua.com',
                'password'   => 'password',
                'role_id'    => $roles[Role::VERIFIKATOR]->id,
                'phone'      => '08114321004',
                'nip'        => '198506102010012003',
                'jabatan'    => 'Kasi Pemerintahan',
                'is_active'  => true,
            ],
            // ─── Penandatangan (Lurah) ───────────────────────
            [
                'name'       => 'Drs. H. Muhammad Arif, M.Si',
                'email'      => 'penandatangan@batua.com',
                'password'   => 'password',
                'role_id'    => $roles[Role::PENANDATANGAN]->id,
                'phone'      => '08114321005',
                'nip'        => '197803252005011001',
                'jabatan'    => 'Lurah Batua',
                'is_active'  => true,
            ],
            // ─── RT/RW ───────────────────────────────────────
            [
                'name'       => 'H. Daeng Mattola',
                'email'      => 'rtrw@batua.com',
                'password'   => 'password',
                'role_id'    => $roles[Role::RT_RW]->id,
                'phone'      => '08114321006',
                'nik'        => '7371111505700001',
                'jabatan'    => 'Ketua RW 001',
                'wilayah_rw' => '001',
                'is_active'  => true,
            ],
            // ─── RT/RW 2 ────────────────────────────────────
            [
                'name'       => 'Muh. Ridwan',
                'email'      => 'rt001@batua.com',
                'password'   => 'password',
                'role_id'    => $roles[Role::RT_RW]->id,
                'phone'      => '08114321007',
                'nik'        => '7371111208800002',
                'jabatan'    => 'Ketua RT 001/001',
                'wilayah_rt' => '001',
                'wilayah_rw' => '001',
                'is_active'  => true,
            ],
            // ─── Warga 1 ────────────────────────────────────
            [
                'name'       => 'Andi Baso',
                'email'      => 'warga@batua.com',
                'password'   => 'password',
                'role_id'    => $roles[Role::WARGA]->id,
                'phone'      => '08114321008',
                'nik'        => '7371112305950001',
                'is_active'  => true,
            ],
            // ─── Warga 2 ────────────────────────────────────
            [
                'name'       => 'Fatimah Azzahra',
                'email'      => 'warga2@batua.com',
                'password'   => 'password',
                'role_id'    => $roles[Role::WARGA]->id,
                'phone'      => '08114321009',
                'nik'        => '7371115607980002',
                'is_active'  => true,
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}
