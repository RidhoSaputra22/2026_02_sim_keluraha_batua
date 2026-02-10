<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PendudukAsing;

class PendudukAsingFactory extends Factory
{
    protected $model = PendudukAsing::class;

    public function definition(): array
    {
        return [
            'no_passport' => $this->faker->unique()->bothify('P#########'),
            'nama' => $this->faker->words(3, true),
            'kecamatan' => $this->faker->words(3, true),
            'kelurahan' => $this->faker->words(3, true),
            'rt' => $this->faker->words(3, true),
            'rw' => $this->faker->words(3, true),
            'alamat' => $this->faker->sentence(12),
            'jenis_kelamin' => $this->faker->randomElement(['L','P']),
            'tgl_input' => $this->faker->date(),
            'petugas_input' => $this->faker->words(3, true),
            'arsip' => $this->faker->optional()->filePath(),
        ];;
    }
}
