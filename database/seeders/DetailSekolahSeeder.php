<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DetailSekolah;

class DetailSekolahSeeder extends Seeder
{
    public function run(): void
    {
        DetailSekolah::factory()->count(20)->create();
    }
}
