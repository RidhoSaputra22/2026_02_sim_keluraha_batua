<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DesaWismaPkk;

class DesaWismaPkkSeeder extends Seeder
{
    public function run(): void
    {
        DesaWismaPkk::factory()->count(20)->create();
    }
}
