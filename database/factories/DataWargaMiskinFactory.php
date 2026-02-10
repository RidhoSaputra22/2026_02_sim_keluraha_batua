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
            'kelurahan' => $this->faker->words(3, true),
            'nama' => $this->faker->words(3, true),
            'nik' => $this->faker->unique()->numerify('################'),
            'alamat' => $this->faker->sentence(12),
            'rw' => $this->faker->words(3, true),
            'rt' => $this->faker->words(3, true),
            'no_peserta' => $this->faker->words(3, true),
            'keterangan' => $this->faker->sentence(12),
        ];;
    }
}
