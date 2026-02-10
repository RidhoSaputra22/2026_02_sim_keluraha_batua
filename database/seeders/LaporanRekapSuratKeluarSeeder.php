<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LaporanRekapSuratKeluar;

class LaporanRekapSuratKeluarSeeder extends Seeder
{
    public function run(): void
    {
        LaporanRekapSuratKeluar::factory()->count(20)->create();
    }
}
