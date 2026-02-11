<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SuratSifat;

class SuratSifatFactory extends Factory
{
    protected $model = SuratSifat::class;

    public function definition(): array
    {
        return [
            'nama' => $this->faker->unique()->randomElement(['Biasa','Penting','Rahasia','Segera'])
        ];
    }
}
