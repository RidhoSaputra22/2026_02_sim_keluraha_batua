<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KtpTercetak;

class KtpTercetakSeeder extends Seeder
{
    public function run(): void
    {
        KtpTercetak::factory()->count(20)->create();
    }
}
