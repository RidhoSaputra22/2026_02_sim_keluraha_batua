<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TempatIbadah;

class TempatIbadahFactory extends Factory
{
    protected $model = TempatIbadah::class;

    public function definition(): array
    {
        return [
            'kelurahan_id' => \App\Models\Kelurahan::factory(),
            'tempat_ibadah' => $this->faker->randomElement(['Masjid','Gereja','Pura','Vihara']),
            'nama' => $this->faker->company(),
            'alamat' => $this->faker->address(),
            'rt_id' => \App\Models\Rt::factory(),
            'rw_id' => \App\Models\Rw::factory(),
            'pengurus' => $this->faker->name(),
            'arsip_path' => null
        ];
    }
}
