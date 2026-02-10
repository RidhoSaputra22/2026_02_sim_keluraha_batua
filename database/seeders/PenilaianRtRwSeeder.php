<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PenilaianRtRw;

class PenilaianRtRwSeeder extends Seeder
{
    public function run(): void
    {
        PenilaianRtRw::factory()->count(20)->create();
    }
}
