<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DataWargaMiskin;

class DataWargaMiskinFactory extends Factory
{
    protected $model = DataWargaMiskin::class;

    public function definition(): array
    {
        return [
            'kelurahan' => $this->faker->randomElement(['Batua', 'Bangkala', 'Tamangapa', 'Antang']),
            'nama' => $this->faker->name(),
            'nik' => null,
            'alamat' => $this->faker->sentence(12),
            'rw' => str_pad($this->faker->numberBetween(1, 10), 3, '0', STR_PAD_LEFT),
            'rt' => str_pad($this->faker->numberBetween(1, 20), 3, '0', STR_PAD_LEFT),
            'no_peserta' => $this->faker->numerify('##########'),
            'keterangan' => $this->faker->sentence(12),
        ];
    }
}
