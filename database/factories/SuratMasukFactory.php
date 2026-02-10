<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SuratMasuk;

class SuratMasukFactory extends Factory
{
    protected $model = SuratMasuk::class;

    public function definition(): array
    {
        return [
            'no_surat' => $this->faker->words(3, true),
            'jenis_surat' => $this->faker->words(3, true),
            'sifat_surat' => $this->faker->words(3, true),
            'asal_surat' => $this->faker->words(3, true),
            'tanggal_diterima' => $this->faker->date(),
            'arsip' => $this->faker->optional()->filePath(),
        ];;
    }
}
