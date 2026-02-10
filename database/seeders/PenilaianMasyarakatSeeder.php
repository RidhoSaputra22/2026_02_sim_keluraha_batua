<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PenilaianMasyarakat;

class PenilaianMasyarakatSeeder extends Seeder
{
    public function run(): void
    {
        PenilaianMasyarakat::factory()->count(20)->create();
    }
}
