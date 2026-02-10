<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataKendaraan;

class DataKendaraanSeeder extends Seeder
{
    public function run(): void
    {
        DataKendaraan::factory()->count(20)->create();
    }
}
