<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PegawaiStaff;

class PegawaiStaffFactory extends Factory
{
    protected $model = PegawaiStaff::class;

    public function definition(): array
    {
        return [
            'nip' => $this->faker->unique()->numerify('##################'),
            'nama' => $this->faker->name(),
            'jabatan' => $this->faker->jobTitle(),
            'gol' => $this->faker->randomElement(['II/a','II/b','III/a','III/b','IV/a']),
            'pangkat' => $this->faker->randomElement(['Pengatur','Penata','Pembina']),
            'status_pegawai' => $this->faker->randomElement(['PNS','PPPK','Honorer']),
            'tgl_input' => $this->faker->dateTimeThisYear(),
            'petugas_input_id' => null,
            'no_urut' => $this->faker->numberBetween(1, 50)
        ];
    }
}
