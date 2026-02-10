<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataWargaMiskin;

class DataWargaMiskinSeeder extends Seeder
{
    public function run(): void
    {
        DataWargaMiskin::factory()->count(20)->create();
    }
}
