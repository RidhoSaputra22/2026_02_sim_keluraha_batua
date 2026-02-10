<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(DataKeluargaSeeder::class);
        $this->call(DataPendudukSeeder::class);
        $this->call(PendudukAsingSeeder::class);
        $this->call(KtpTercetakSeeder::class);
        $this->call(RekapBulananPendudukSeeder::class);
        $this->call(PegawaiStaffSeeder::class);
        $this->call(PenandatangananSeeder::class);
        $this->call(DataRtRwSeeder::class);
        $this->call(PenilaianRtRwSeeder::class);
        $this->call(RekapPenilaianRtRwSeeder::class);
        $this->call(PenilaianMasyarakatSeeder::class);
        $this->call(AgendaKegiatanSeeder::class);
        $this->call(HasilKegiatanSeeder::class);
        $this->call(SuratMasukSeeder::class);
        $this->call(SuratHimbauanSeeder::class);
        $this->call(SuratLainLainSeeder::class);
        $this->call(DaftarSuratKeluarSeeder::class);
        $this->call(LaporanRekapSuratMasukSeeder::class);
        $this->call(LaporanRekapSuratKeluarSeeder::class);
        $this->call(LaporanRekapSuratKeteranganDomisiliSeeder::class);
        $this->call(DataUmkmSeeder::class);
        $this->call(DetailSekolahSeeder::class);
        $this->call(DataFaskesSeeder::class);
        $this->call(PetugasKebersihanSeeder::class);
        $this->call(DataEkspedisiSeeder::class);
        $this->call(RekapImbSeeder::class);
        $this->call(DataSekolahSeeder::class);
        $this->call(TempatIbadahSeeder::class);
        $this->call(Pk5Seeder::class);
        $this->call(DataWargaMiskinSeeder::class);
        $this->call(DesaWismaPkkSeeder::class);
        $this->call(DataKendaraanSeeder::class);
    }
}
