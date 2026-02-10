<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DesaWismaPkk;

class DesaWismaPkkFactory extends Factory
{
    protected $model = DesaWismaPkk::class;

    public function definition(): array
    {
        return [
            'nik' => $this->faker->unique()->numerify('################'),
            'nama' => $this->faker->words(3, true),
            'no_kk' => $this->faker->unique()->numerify('################'),
            'kecamatan' => $this->faker->words(3, true),
            'kelurahan' => $this->faker->words(3, true),
            'rt' => $this->faker->words(3, true),
            'rw' => $this->faker->words(3, true),
            'alamat' => $this->faker->sentence(12),
            'jenis_kelamin' => $this->faker->randomElement(['L','P']),
            'agama' => $this->faker->words(3, true),
            'status_kawin' => $this->faker->words(3, true),
            'pendidikan' => $this->faker->words(3, true),
            'status_data' => $this->faker->words(3, true),
            'tgl_input' => $this->faker->date(),
            'petugas_input' => $this->faker->words(3, true),
            'arsip' => $this->faker->optional()->filePath(),
        ];;
    }
}
