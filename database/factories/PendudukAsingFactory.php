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
            'nama' => $this->faker->name(),
            'kecamatan' => $this->faker->randomElement(['Manggala', 'Panakkukang', 'Tamalate', 'Makassar']),
            'kelurahan' => $this->faker->randomElement(['Batua', 'Bangkala', 'Tamangapa', 'Antang']),
            'rt' => str_pad($this->faker->numberBetween(1, 20), 3, '0', STR_PAD_LEFT),
            'rw' => str_pad($this->faker->numberBetween(1, 10), 3, '0', STR_PAD_LEFT),
            'alamat' => $this->faker->address(),
            'jenis_kelamin' => $this->faker->randomElement(['L', 'P']),
            'tgl_input' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'petugas_input' => $this->faker->numberBetween(1, 10),
            'arsip' => $this->faker->optional()->filePath(),
        ];
    }
}
