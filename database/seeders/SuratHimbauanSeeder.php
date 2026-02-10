<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SuratHimbauan;

class SuratHimbauanSeeder extends Seeder
{
    public function run(): void
    {
        SuratHimbauan::factory()->count(20)->create();
    }
}
