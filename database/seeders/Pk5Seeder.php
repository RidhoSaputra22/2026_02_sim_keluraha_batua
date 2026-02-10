<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pk5;

class Pk5Seeder extends Seeder
{
    public function run(): void
    {
        Pk5::factory()->count(20)->create();
    }
}
