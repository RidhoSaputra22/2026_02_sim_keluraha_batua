<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PendudukAsing;

class PendudukAsingSeeder extends Seeder
{
    public function run(): void
    {
        PendudukAsing::factory()->count(20)->create();
    }
}
