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
                'name'        => Role::ADMIN,
                'label'       => 'Admin Sistem',
                'description' => 'Akses penuh ke seluruh fitur sistem termasuk kelola user, role, master data, template, dan pengaturan.',
                'permissions' => ['*'],
                'is_active'   => true,
            ],
            [
                'name'        => Role::OPERATOR,
                'label'       => 'Operator Kelurahan',
                'description' => 'Input data penduduk, proses permohonan surat, cetak dokumen, dan arsip.',
                'permissions' => ['penduduk.*', 'keluarga.*', 'surat.*', 'usaha.*', 'laporan.view', 'arsip.*'],
                'is_active'   => true,
            ],
            [
                'name'        => Role::VERIFIKATOR,
                'label'       => 'Verifikator (Kasi/Seklur)',
                'description' => 'Validasi data dan berkas, approve/reject permohonan surat, koreksi data.',
                'permissions' => ['surat.verify', 'surat.view', 'surat.reject', 'laporan.view'],
                'is_active'   => true,
            ],
            [
                'name'        => Role::PENANDATANGAN,
                'label'       => 'Penandatangan (Lurah)',
                'description' => 'Tanda tangan dan finalisasi surat resmi kelurahan.',
                'permissions' => ['surat.sign', 'surat.view', 'surat.finalize'],
                'is_active'   => true,
            ],
            [
                'name'        => Role::RT_RW,
                'label'       => 'Ketua RT/RW',
                'description' => 'Pengelolaan data warga RT/RW, surat pengantar, dan monitoring wilayah.',
                'permissions' => ['penduduk.view', 'keluarga.view', 'surat.pengantar', 'laporan.view'],
                'is_active'   => true,
            ],
            [
                'name'        => Role::WARGA,
                'label'       => 'Warga',
                'description' => 'Portal warga untuk mengajukan permohonan layanan, upload berkas, dan cek status.',
                'permissions' => ['surat.request', 'surat.track', 'dokumen.download'],
                'is_active'   => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
