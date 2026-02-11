<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SurveyKepuasan;

class SurveyKepuasanFactory extends Factory
{
    protected $model = SurveyKepuasan::class;

    public function definition(): array
    {
        return [
            'kelurahan_id' => \App\Models\Kelurahan::factory(),
            'jenis_kelamin' => $this->faker->randomElement(['L','P']),
            'umur' => $this->faker->numberBetween(17, 70),
            'pendidikan' => $this->faker->randomElement(['SD','SMP','SMA','D3','S1','S2']),
            'pekerjaan' => $this->faker->jobTitle(),
            'jenis_layanan_id' => \App\Models\SurveyLayanan::factory(),
            'jumlah_nilai' => $this->faker->numberBetween(50, 100),
            'nilai_rata_rata' => $this->faker->randomFloat(2, 50, 100)
        ];
    }
}
