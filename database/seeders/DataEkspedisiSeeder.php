<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataEkspedisi;

class DataEkspedisiSeeder extends Seeder
{
    public function run(): void
    {
        DataEkspedisi::factory()->count(20)->create();
    }
}
