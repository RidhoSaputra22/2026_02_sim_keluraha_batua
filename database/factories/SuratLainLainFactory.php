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
            'no_surat' => $this->faker->words(3, true),
            'tanggal_surat' => $this->faker->date(),
            'perihal' => $this->faker->words(3, true),
            'tujuan_surat' => $this->faker->words(3, true),
            'arsip' => $this->faker->optional()->filePath(),
        ];;
    }
}
