<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TempatIbadah;

class TempatIbadahSeeder extends Seeder
{
    public function run(): void
    {
        TempatIbadah::factory()->count(20)->create();
    }
}
