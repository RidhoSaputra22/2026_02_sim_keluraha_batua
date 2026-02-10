<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AgendaKegiatan;

class AgendaKegiatanSeeder extends Seeder
{
    public function run(): void
    {
        AgendaKegiatan::factory()->count(20)->create();
    }
}
