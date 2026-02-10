<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DaftarSuratKeluar;

class DaftarSuratKeluarSeeder extends Seeder
{
    public function run(): void
    {
        DaftarSuratKeluar::factory()->count(20)->create();
    }
}
