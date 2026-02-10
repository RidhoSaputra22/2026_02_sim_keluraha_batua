<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PenilaianRtRw;

class PenilaianRtRwFactory extends Factory
{
    protected $model = PenilaianRtRw::class;

    public function definition(): array
    {
        return [
            'nik' => $this->faker->unique()->numerify('################'),
            'nama' => $this->faker->words(3, true),
            'jabatan' => $this->faker->words(3, true),
            'nilai' => $this->faker->randomFloat(2, 0, 100),
            'standar_nilai' => $this->faker->randomFloat(2, 0, 100),
            'usulan_nilai_insentif' => $this->faker->randomFloat(2, 0, 500000),
            'periode_penilaian' => $this->faker->date(),
            'lpj' => $this->faker->optional()->filePath(),
            'arsip' => $this->faker->optional()->filePath(),
        ];;
    }
}
