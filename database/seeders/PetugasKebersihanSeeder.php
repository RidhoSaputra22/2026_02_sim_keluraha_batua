<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PetugasKebersihan;

class PetugasKebersihanSeeder extends Seeder
{
    public function run(): void
    {
        PetugasKebersihan::factory()->count(20)->create();
    }
}
