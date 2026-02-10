<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SuratLainLain;

class SuratLainLainSeeder extends Seeder
{
    public function run(): void
    {
        SuratLainLain::factory()->count(20)->create();
    }
}
