<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SuratHimbauan;

class SuratHimbauanFactory extends Factory
{
    protected $model = SuratHimbauan::class;

    public function definition(): array
    {
        return [
            'no_surat' => $this->faker->words(3, true),
            'sifat_surat' => $this->faker->words(3, true),
            'asal_surat' => $this->faker->words(3, true),
            'tanggal_surat' => $this->faker->date(),
            'tujuan_surat' => $this->faker->words(3, true),
            'uraian' => $this->faker->sentence(12),
            'arsip' => $this->faker->optional()->filePath(),
        ];;
    }
}
