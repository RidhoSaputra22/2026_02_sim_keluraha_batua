<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Penandatanganan;

class PenandatangananSeeder extends Seeder
{
    public function run(): void
    {
        Penandatanganan::factory()->count(20)->create();
    }
}
