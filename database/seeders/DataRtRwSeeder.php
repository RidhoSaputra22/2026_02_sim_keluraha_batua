<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataRtRw;

class DataRtRwSeeder extends Seeder
{
    public function run(): void
    {
        DataRtRw::factory()->count(20)->create();
    }
}
