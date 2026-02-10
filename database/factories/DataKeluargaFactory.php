<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DataKeluarga;

class DataKeluargaFactory extends Factory
{
    protected $model = DataKeluarga::class;

    public function definition(): array
    {
        return [
            'no_kk' => $this->faker->unique()->numerify('################'),
            'nama_kepala_keluarga' => $this->faker->words(3, true),
            'jumlah_anggota_keluarga' => $this->faker->numberBetween(0, 5000),
            'rw' => $this->faker->words(3, true),
            'rt' => $this->faker->words(3, true),
            'tgl_input' => $this->faker->date(),
            'petugas_input' => $this->faker->words(3, true),
            'arsip' => $this->faker->optional()->filePath(),
        ];;
    }
}
