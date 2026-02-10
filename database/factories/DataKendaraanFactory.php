<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DataKendaraan;

class DataKendaraanFactory extends Factory
{
    protected $model = DataKendaraan::class;

    public function definition(): array
    {
        return [
            'jenis_barang' => $this->faker->words(3, true),
            'nama_pengemudi' => $this->faker->words(3, true),
            'no_polisi' => $this->faker->words(3, true),
            'no_rangka' => $this->faker->words(3, true),
            'no_mesin' => $this->faker->words(3, true),
            'tahun_perolehan' => $this->faker->numberBetween(2000, (int)date('Y')),
            'merek_type' => $this->faker->words(3, true),
            'wilayah_penugasan' => $this->faker->words(3, true),
            'arsip' => $this->faker->optional()->filePath(),
        ];;
    }
}
