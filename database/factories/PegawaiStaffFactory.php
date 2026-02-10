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
            'nip' => $this->faker->unique()->numerify('################'),
            'nama' => $this->faker->words(3, true),
            'jabatan' => $this->faker->words(3, true),
            'gol' => $this->faker->words(3, true),
            'pangkat' => $this->faker->words(3, true),
            'status_pegawai' => $this->faker->words(3, true),
            'tgl_input' => $this->faker->date(),
            'petugas_input' => $this->faker->words(3, true),
            'no_urut' => $this->faker->words(3, true),
        ];;
    }
}
