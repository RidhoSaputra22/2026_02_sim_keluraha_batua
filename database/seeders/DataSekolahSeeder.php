<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataSekolah;

class DataSekolahSeeder extends Seeder
{
    public function run(): void
    {
        DataSekolah::factory()->count(20)->create();
    }
}
