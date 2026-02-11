<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PendudukAsing;

class PendudukAsingFactory extends Factory
{
    protected $model = PendudukAsing::class;

    public function definition(): array
    {
        return [
            'no_paspor' => $this->faker->unique()->bothify('??######'),
            'nama' => $this->faker->name(),
            'alamat' => $this->faker->address(),
            'rt_id' => \App\Models\Rt::factory(),
            'jenis_kelamin' => $this->faker->randomElement(['L','P']),
            'tgl_input' => $this->faker->dateTimeThisYear(),
            'petugas_input_id' => null,
            'arsip_path' => null
        ];
    }
}
