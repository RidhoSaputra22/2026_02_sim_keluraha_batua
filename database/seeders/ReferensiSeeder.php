<?php

namespace Database\Seeders;

use App\Models\JabatanRtRw;
use App\Models\LayananPublik;
use App\Models\SuratJenis;
use App\Models\SuratSifat;
use App\Models\SurveyLayanan;
use Illuminate\Database\Seeder;

class ReferensiSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Jenis Surat ──────────────────────────────────────
        $jenisSurat = [
            'Surat Keterangan Tidak Mampu (SKTM)',
            'Surat Keterangan Domisili',
            'Surat Keterangan Usaha',
            'Surat Pengantar Nikah',
            'Surat Keterangan Kelahiran',
            'Surat Keterangan Kematian',
            'Surat Keterangan Pindah',
            'Surat Keterangan Datang',
            'Surat Pengantar KTP',
            'Surat Pengantar KK',
            'Surat Keterangan Belum Menikah',
            'Surat Keterangan Berkelakuan Baik',
            'Surat Pengantar SKCK',
            'Surat Keterangan Ahli Waris',
            'Surat Izin Keramaian',
            'Surat Keterangan Janda/Duda',
            'Surat Keterangan Penghasilan',
            'Surat Pengantar IMB',
            'Surat Keterangan Kehilangan',
            'Surat Keterangan Beda Nama',
        ];

        foreach ($jenisSurat as $nama) {
            SuratJenis::create(['nama' => $nama]);
        }

        // ─── Sifat Surat ─────────────────────────────────────
        $sifatSurat = [
            'Biasa',
            'Segera',
            'Sangat Segera',
            'Rahasia',
        ];

        foreach ($sifatSurat as $nama) {
            SuratSifat::create(['nama' => $nama]);
        }

        // ─── Jabatan RT/RW ───────────────────────────────────
        $jabatan = [
            'Ketua RT',
            'Wakil Ketua RT',
            'Sekretaris RT',
            'Bendahara RT',
            'Ketua RW',
            'Wakil Ketua RW',
            'Sekretaris RW',
            'Bendahara RW',
        ];

        foreach ($jabatan as $nama) {
            JabatanRtRw::create(['nama' => $nama]);
        }

        // ─── Layanan Publik ──────────────────────────────────
        $layanan = [
            'Pelayanan Administrasi Kependudukan',
            'Pelayanan Surat Menyurat',
            'Pelayanan Perizinan',
            'Pelayanan Sosial & Kemasyarakatan',
            'Pelayanan Kesehatan',
            'Pelayanan Kebersihan & Lingkungan',
            'Pelayanan Keamanan & Ketertiban',
        ];

        foreach ($layanan as $nama) {
            LayananPublik::create(['nama' => $nama]);
        }

        // ─── Jenis Survey Layanan ────────────────────────────
        $surveyLayanan = [
            'Pelayanan Administrasi',
            'Pelayanan Kependudukan',
            'Pelayanan Persuratan',
            'Pelayanan Perizinan',
            'Pelayanan Sosial',
            'Pelayanan Umum',
        ];

        foreach ($surveyLayanan as $nama) {
            SurveyLayanan::create(['nama' => $nama]);
        }
    }
}
