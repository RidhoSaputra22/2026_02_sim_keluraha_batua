<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Rw;

class RwFactory extends Factory
{
    protected $model = Rw::class;

    public function definition(): array
    {
        return [
            'kelurahan_id' => \App\Models\Kelurahan::factory(),
            'nomor' => $this->faker->numberBetween(1, 20),
            'warna' => $this->faker->hexColor(),
        ];
    }
}
