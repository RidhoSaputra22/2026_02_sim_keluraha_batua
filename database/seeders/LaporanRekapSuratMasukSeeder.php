<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LaporanRekapSuratMasuk;

class LaporanRekapSuratMasukSeeder extends Seeder
{
    public function run(): void
    {
        LaporanRekapSuratMasuk::factory()->count(20)->create();
    }
}
