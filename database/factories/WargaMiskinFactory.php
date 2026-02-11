<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\WargaMiskin;

class WargaMiskinFactory extends Factory
{
    protected $model = WargaMiskin::class;

    public function definition(): array
    {
        return [
            'kelurahan_id' => \App\Models\Kelurahan::factory(),
            'penduduk_id' => \App\Models\Penduduk::factory(),
            'rt_id' => \App\Models\Rt::factory(),
            'rw_id' => \App\Models\Rw::factory(),
            'no_peserta' => $this->faker->optional()->numerify('##########'),
            'keterangan' => $this->faker->sentence(6)
        ];
    }
}
