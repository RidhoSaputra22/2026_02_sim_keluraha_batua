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
            'kelurahan' => $this->faker->randomElement(['Batua', 'Bangkala', 'Tamangapa', 'Antang']),
            'tempat_ibadah' => $this->faker->randomElement(['Masjid', 'Musholla', 'Gereja', 'Pura']),
            'nama' => $this->faker->name(),
            'alamat' => $this->faker->sentence(12),
            'rt' => str_pad($this->faker->numberBetween(1, 20), 3, '0', STR_PAD_LEFT),
            'rw' => str_pad($this->faker->numberBetween(1, 10), 3, '0', STR_PAD_LEFT),
            'pengurus' => $this->faker->name(),
            'arsip' => $this->faker->optional()->filePath(),
        ];
    }
}
