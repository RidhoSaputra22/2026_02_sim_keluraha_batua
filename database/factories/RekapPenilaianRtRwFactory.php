<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\RekapPenilaianRtRw;

class RekapPenilaianRtRwFactory extends Factory
{
    protected $model = RekapPenilaianRtRw::class;

    public function definition(): array
    {
        return [
            'nik' => $this->faker->unique()->numerify('################'),
            'nama' => $this->faker->words(3, true),
            'jabatan' => $this->faker->words(3, true),
            'rw' => $this->faker->words(3, true),
            'tanggal' => $this->faker->date(),
            'periode_penilaian' => $this->faker->date(),
            'nilai' => $this->faker->randomFloat(2, 0, 100),
            'kelurahan' => $this->faker->words(3, true),
            'arsip' => $this->faker->optional()->filePath(),
        ];;
    }
}
