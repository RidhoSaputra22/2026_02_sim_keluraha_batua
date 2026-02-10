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
            'no_surat' => $this->faker->numerify('###/KEL-BTU/##/####'),
            'sifat_surat' => $this->faker->randomElement(['Biasa', 'Segera', 'Sangat Segera']),
            'asal_surat' => $this->faker->randomElement(['Kecamatan', 'Dinas', 'Internal', 'Warga']),
            'tanggal_surat' => $this->faker->date(),
            'tujuan_surat' => $this->faker->company(),
            'uraian' => $this->faker->sentence(12),
            'arsip' => $this->faker->optional()->filePath(),
        ];
    }
}
