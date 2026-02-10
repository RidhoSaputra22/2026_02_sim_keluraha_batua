<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataFaskes;

class DataFaskesSeeder extends Seeder
{
    public function run(): void
    {
        DataFaskes::factory()->count(20)->create();
    }
}
