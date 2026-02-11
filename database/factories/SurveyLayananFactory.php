<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SurveyLayanan;

class SurveyLayananFactory extends Factory
{
    protected $model = SurveyLayanan::class;

    public function definition(): array
    {
        return [
            'nama' => $this->faker->unique()->randomElement(['Administrasi','Kependudukan','Persuratan','Pengaduan','Layanan Umum'])
        ];
    }
}
