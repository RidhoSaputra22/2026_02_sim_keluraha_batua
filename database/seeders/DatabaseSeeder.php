<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ── Seed Roles ──────────────────────────────────────────
        $roles = [
            [
                'name' => Role::ADMIN,
                'label' => 'Admin Sistem',
                'description' => 'Akses penuh ke seluruh modul sistem',
                'permissions' => json_encode(['*']),
                'is_active' => true,
            ],
            [
                'name' => Role::OPERATOR,
                'label' => 'Operator Kelurahan',
                'description' => 'Input data penduduk, KK, permohonan surat, cetak & arsip',
                'permissions' => json_encode(['kependudukan.*', 'persuratan.permohonan', 'persuratan.arsip', 'usaha.*', 'laporan.*']),
                'is_active' => true,
            ],
            [
                'name' => Role::VERIFIKATOR,
                'label' => 'Verifikator (Kasi/Seklur)',
                'description' => 'Validasi data & berkas, approve/reject permohonan surat',
                'permissions' => json_encode(['persuratan.verifikasi', 'laporan.*']),
                'is_active' => true,
            ],
            [
                'name' => Role::PENANDATANGAN,
                'label' => 'Penandatangan (Lurah)',
                'description' => 'Tanda tangan & finalisasi surat resmi',
                'permissions' => json_encode(['persuratan.tanda-tangan']),
                'is_active' => true,
            ],
            [
                'name' => Role::RT_RW,
                'label' => 'RT/RW',
                'description' => 'Pendataan warga wilayah, surat pengantar, monitoring',
                'permissions' => json_encode(['kependudukan.penduduk', 'kependudukan.kk', 'rtrw.*']),
                'is_active' => true,
            ],
            [
                'name' => Role::WARGA,
                'label' => 'Warga',
                'description' => 'Ajukan permohonan surat, tracking status, unduh dokumen',
                'permissions' => json_encode(['warga.*', 'persuratan.tracking']),
                'is_active' => true,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
        }

        // ── Seed Sample Users (1 per role) ──────────────────────
        $sampleUsers = [
            [
                'name' => 'Admin Batua',
                'email' => 'admin@batua.com',
                'nip' => '199001012020011001',
                'jabatan' => 'Administrator Sistem',
                'role' => Role::ADMIN,
            ],
            [
                'name' => 'Rina Operator',
                'email' => 'operator@batua.com',
                'nip' => '199203152020012002',
                'jabatan' => 'Staf Pelayanan',
                'role' => Role::OPERATOR,
            ],
            [
                'name' => 'Andi Verifikator',
                'email' => 'verifikator@batua.com',
                'nip' => '198507202019031003',
                'jabatan' => 'Kasi Pemerintahan',
                'role' => Role::VERIFIKATOR,
            ],
            [
                'name' => 'H. Muh. Amir, S.Sos',
                'email' => 'lurah@batua.com',
                'nip' => '197805102005011004',
                'jabatan' => 'Lurah Batua',
                'role' => Role::PENANDATANGAN,
            ],
            [
                'name' => 'Pak Dg. Nai (RT 003)',
                'email' => 'rt003@batua.com',
                'nik' => '7371012503780001',
                'jabatan' => 'Ketua RT 003 / RW 002',
                'role' => Role::RT_RW,
            ],
            [
                'name' => 'Ahmad Yani',
                'email' => 'warga@batua.com',
                'nik' => '7371011506900002',
                'role' => Role::WARGA,
            ],
        ];

        foreach ($sampleUsers as $userData) {
            $roleName = $userData['role'];
            unset($userData['role']);

            $role = Role::where('name', $roleName)->first();

            User::updateOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, [
                    'password' => bcrypt('password'),
                    'role_id' => $role->id,
                    'is_active' => true,
                ])
            );
        }

        // ── Seed All Data (in FK-safe order) ───────────────────
        $this->call([
            // Master Data (no dependencies)
            PegawaiStaffSeeder::class,
            PenandatangananSeeder::class,
            DataRtRwSeeder::class,

            // Kependudukan (depends on RT/RW)
            DataKeluargaSeeder::class,
            DataPendudukSeeder::class,
            PendudukAsingSeeder::class,

            // Usaha & Ekonomi
            DataUmkmSeeder::class,
            Pk5Seeder::class,
            DataWargaMiskinSeeder::class,

            // Fasilitas Umum
            DataFaskesSeeder::class,
            DataSekolahSeeder::class,
            DetailSekolahSeeder::class,
            TempatIbadahSeeder::class,
            DesaWismaPkkSeeder::class,

            // Transportasi & Infrastruktur
            DataKendaraanSeeder::class,
            PetugasKebersihanSeeder::class,

            // Administrasi Surat
            SuratMasukSeeder::class,
            DaftarSuratKeluarSeeder::class,
            SuratHimbauanSeeder::class,
            SuratLainLainSeeder::class,
            DataEkspedisiSeeder::class,

            // Agenda & Kegiatan
            AgendaKegiatanSeeder::class,
            HasilKegiatanSeeder::class,

            // Penilaian & Evaluasi
            PenilaianMasyarakatSeeder::class,
            PenilaianRtRwSeeder::class,
            RekapPenilaianRtRwSeeder::class,

            // Laporan & Rekap
            // KtpTercetakSeeder::class, // Requires existing NIK from data_penduduk
            RekapBulananPendudukSeeder::class,
            RekapImbSeeder::class,
            LaporanRekapSuratMasukSeeder::class,
            LaporanRekapSuratKeluarSeeder::class,
            LaporanRekapSuratKeteranganDomisiliSeeder::class,
        ]);
    }
}
