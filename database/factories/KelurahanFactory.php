<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Kelurahan;

class KelurahanFactory extends Factory
{
    protected $model = Kelurahan::class;

    public function definition(): array
    {
        return [
            'kecamatan_id' => \App\Models\Kecamatan::factory(),
            'nama' => $this->faker->unique()->citySuffix()
        ];
    }
}
