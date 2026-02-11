<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Keluarga;

class KeluargaFactory extends Factory
{
    protected $model = Keluarga::class;

    public function definition(): array
    {
        return [
            'no_kk' => $this->faker->unique()->numerify('################'),
            'kepala_keluarga_id' => null,
            'jumlah_anggota_keluarga' => $this->faker->numberBetween(1, 6),
            'rt_id' => \App\Models\Rt::factory(),
            'tgl_input' => $this->faker->dateTimeThisYear(),
            'petugas_input_id' => null,
            'arsip_path' => null
        ];
    }
}
