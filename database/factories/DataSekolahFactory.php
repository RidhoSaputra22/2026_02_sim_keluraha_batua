<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DataSekolah;

class DataSekolahFactory extends Factory
{
    protected $model = DataSekolah::class;

    public function definition(): array
    {
        return [
            'kelurahan' => $this->faker->words(3, true),
            'nama_sekolah' => $this->faker->words(3, true),
            'jenjang' => $this->faker->words(3, true),
            'status' => $this->faker->words(3, true),
            'alamat' => $this->faker->sentence(12),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'arsip' => $this->faker->optional()->filePath(),
        ];;
    }
}
