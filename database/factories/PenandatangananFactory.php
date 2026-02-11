<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Penandatanganan;

class PenandatangananFactory extends Factory
{
    protected $model = Penandatanganan::class;

    public function definition(): array
    {
        return [
            'pegawai_id' => \App\Models\PegawaiStaff::factory(),
            'status' => $this->faker->randomElement(['Aktif','Nonaktif']),
            'no_telp' => $this->faker->e164PhoneNumber(),
            'tgl_input' => $this->faker->dateTimeThisYear(),
            'petugas_input_id' => null
        ];
    }
}
