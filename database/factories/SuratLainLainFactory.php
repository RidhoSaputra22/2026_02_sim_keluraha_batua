<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SuratLainLain;

class SuratLainLainFactory extends Factory
{
    protected $model = SuratLainLain::class;

    public function definition(): array
    {
        return [
            'no_surat' => $this->faker->numerify('###/KEL-BTU/##/####'),
            'tanggal_surat' => $this->faker->date(),
            'perihal' => $this->faker->sentence(5),
            'tujuan_surat' => $this->faker->company(),
            'arsip' => $this->faker->optional()->filePath(),
        ];
    }
}
