<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataUmkm;

class DataUmkmSeeder extends Seeder
{
    public function run(): void
    {
        DataUmkm::factory()->count(20)->create();
    }
}
