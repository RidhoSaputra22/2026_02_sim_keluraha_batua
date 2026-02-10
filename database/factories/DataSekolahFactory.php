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
            'kelurahan' => $this->faker->randomElement(['Batua', 'Bangkala', 'Tamangapa', 'Antang']),
            'nama_sekolah' => 'SD Negeri ' . $this->faker->numberBetween(1, 100),
            'jenjang' => $this->faker->randomElement(['SD', 'SMP', 'SMA', 'SMK']),
            'status' => $this->faker->randomElement(['Negeri', 'Swasta']),
            'alamat' => $this->faker->sentence(12),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'arsip' => $this->faker->optional()->filePath(),
        ];
    }
}
