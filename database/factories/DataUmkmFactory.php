<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DataUmkm;

class DataUmkmFactory extends Factory
{
    protected $model = DataUmkm::class;

    public function definition(): array
    {
        return [
            'nama_pemilik' => $this->faker->words(3, true),
            'nik' => $this->faker->unique()->numerify('################'),
            'no_hp' => $this->faker->phoneNumber(),
            'nama_ukm' => $this->faker->words(3, true),
            'alamat' => $this->faker->sentence(12),
            'rt' => $this->faker->words(3, true),
            'rw' => $this->faker->words(3, true),
            'sektor_umkm' => $this->faker->words(3, true),
        ];;
    }
}
