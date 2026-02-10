<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RekapPenilaianRtRw;

class RekapPenilaianRtRwSeeder extends Seeder
{
    public function run(): void
    {
        RekapPenilaianRtRw::factory()->count(20)->create();
    }
}
