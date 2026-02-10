<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HasilKegiatan;

class HasilKegiatanSeeder extends Seeder
{
    public function run(): void
    {
        HasilKegiatan::factory()->count(20)->create();
    }
}
