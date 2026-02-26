<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => Role::ADMIN,
                'label' => 'Admin Sistem',
                'description' => 'Akses penuh ke seluruh fitur sistem termasuk kelola user, role, master data, template, dan pengaturan.',
                'permissions' => ['*'],
                'is_active' => true,
            ],
            [
                'name' => Role::RT_RW,
                'label' => 'Ketua RT/RW',
                'description' => 'Pengelolaan data warga RT/RW, surat pengantar, dan monitoring wilayah.',
                'permissions' => ['penduduk.view', 'keluarga.view', 'surat.pengantar', 'laporan.view'],
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
