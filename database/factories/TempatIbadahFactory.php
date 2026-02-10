<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TempatIbadah;

class TempatIbadahFactory extends Factory
{
    protected $model = TempatIbadah::class;

    public function definition(): array
    {
        return [
            'kelurahan' => $this->faker->words(3, true),
            'tempat_ibadah' => $this->faker->words(3, true),
            'nama' => $this->faker->words(3, true),
            'alamat' => $this->faker->sentence(12),
            'rt' => $this->faker->words(3, true),
            'rw' => $this->faker->words(3, true),
            'pengurus' => $this->faker->words(3, true),
            'arsip' => $this->faker->optional()->filePath(),
        ];;
    }
}
