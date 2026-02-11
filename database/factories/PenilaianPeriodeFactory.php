<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PenilaianPeriode;

class PenilaianPeriodeFactory extends Factory
{
    protected $model = PenilaianPeriode::class;

    public function definition(): array
    {
        return [
            'kelurahan_id' => \App\Models\Kelurahan::factory(),
            'nama_periode' => $this->faker->unique()->numerify('2026-##'),
            'tgl_mulai' => $this->faker->date(),
            'tgl_selesai' => $this->faker->date()
        ];
    }
}
