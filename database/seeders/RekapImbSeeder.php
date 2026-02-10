<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RekapImb;

class RekapImbSeeder extends Seeder
{
    public function run(): void
    {
        RekapImb::factory()->count(20)->create();
    }
}
