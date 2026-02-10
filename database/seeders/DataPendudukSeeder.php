<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataPenduduk;

class DataPendudukSeeder extends Seeder
{
    public function run(): void
    {
        DataPenduduk::factory()->count(20)->create();
    }
}
