<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RekapBulananPenduduk;

class RekapBulananPendudukSeeder extends Seeder
{
    public function run(): void
    {
        RekapBulananPenduduk::factory()->count(20)->create();
    }
}
