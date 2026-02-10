<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LaporanRekapSuratKeteranganDomisili;

class LaporanRekapSuratKeteranganDomisiliSeeder extends Seeder
{
    public function run(): void
    {
        LaporanRekapSuratKeteranganDomisili::factory()->count(20)->create();
    }
}
