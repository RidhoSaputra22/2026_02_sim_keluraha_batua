<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PegawaiStaff;

class PegawaiStaffSeeder extends Seeder
{
    public function run(): void
    {
        PegawaiStaff::factory()->count(20)->create();
    }
}
