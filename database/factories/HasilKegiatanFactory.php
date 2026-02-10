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
            'hari_tanggal' => $this->faker->dayOfWeek() . ', ' . $this->faker->date(),
            'agenda' => $this->faker->sentence(5),
            'notulen' => $this->faker->sentence(12),
            'keterangan' => $this->faker->sentence(12),
            'arsip' => $this->faker->optional()->filePath(),
        ];
    }
}
