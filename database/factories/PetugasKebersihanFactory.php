<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PetugasKebersihan;

class PetugasKebersihanFactory extends Factory
{
    protected $model = PetugasKebersihan::class;

    public function definition(): array
    {
        return [
            'nama' => $this->faker->words(3, true),
            'nik' => $this->faker->unique()->numerify('################'),
            'unit_kerja' => $this->faker->words(3, true),
            'jenis_kelamin' => $this->faker->randomElement(['L','P']),
            'pekerjaan' => $this->faker->words(3, true),
            'lokasi' => $this->faker->words(3, true),
            'status' => $this->faker->words(3, true),
        ];;
    }
}
