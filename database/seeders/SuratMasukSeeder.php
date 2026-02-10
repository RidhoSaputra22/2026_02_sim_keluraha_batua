<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SuratMasuk;

class SuratMasukSeeder extends Seeder
{
    public function run(): void
    {
        SuratMasuk::factory()->count(20)->create();
    }
}
