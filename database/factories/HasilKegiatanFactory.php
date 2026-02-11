<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\HasilKegiatan;

class HasilKegiatanFactory extends Factory
{
    protected $model = HasilKegiatan::class;

    public function definition(): array
    {
        return [
            'agenda_id' => \App\Models\AgendaKegiatan::factory(),
            'hari_tanggal' => $this->faker->dateTimeThisYear(),
            'notulen' => $this->faker->paragraphs(2, true),
            'keterangan' => $this->faker->sentence(8),
            'arsip_path' => null
        ];
    }
}
