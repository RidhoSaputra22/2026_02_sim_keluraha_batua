<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataKeluarga;

class DataKeluargaSeeder extends Seeder
{
    public function run(): void
    {
        DataKeluarga::factory()->count(20)->create();
    }
}
