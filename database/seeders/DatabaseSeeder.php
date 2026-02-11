<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Urutan seeder berdasarkan dependency FK:
     * 1. Master data (tanpa dependensi)
     * 2. User (depends: roles)
     * 3. Pegawai & Penandatangan (depends: users)
     * 4. Penduduk & Keluarga (depends: wilayah, users)
     * 5. Data transaksional (depends: semua di atas)
     */
    public function run(): void
    {
        $this->call([
            // ╔════════════════════════════════════════════════╗
            // ║  MASTER DATA (referensi / lookup tables)      ║
            // ╚════════════════════════════════════════════════╝
            RoleSeeder::class,          // 6 roles
            WilayahSeeder::class,       // Kecamatan → Kelurahan → RW → RT
            ReferensiSeeder::class,     // Jenis surat, sifat, jabatan RT/RW, layanan, survey

            // ╔════════════════════════════════════════════════╗
            // ║  DEMO DATA (sample / transactional)           ║
            // ╚════════════════════════════════════════════════╝
            UserSeeder::class,                  // 9 users (1+ per role)
            PegawaiPenandatanganSeeder::class,  // 10 pegawai, 3 penandatangan
            PendudukKeluargaSeeder::class,      // 15 keluarga, ~50 penduduk, KTP tercetak
            InstansiPemohonSuratSeeder::class,  // 10 instansi, 10 pemohon, 18 surat
            RtRwPengurusSeeder::class,          // Pengurus RT/RW + penilaian 2 periode
            DataUmumSeeder::class,              // UMKM, sekolah, faskes, tempat ibadah, dll.
            AgendaSurveySeeder::class,          // 6 agenda, hasil kegiatan, 20 survey
        ]);
    }
}
