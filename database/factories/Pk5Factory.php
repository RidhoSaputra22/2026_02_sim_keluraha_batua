<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Pk5;

class Pk5Factory extends Factory
{
    protected $model = Pk5::class;

    public function definition(): array
    {
        return [
            'nip' => $this->faker->optional()->numerify('##################'),
            'nama' => $this->faker->name(),
            'jabatan' => $this->faker->jobTitle(),
            'pangkat' => $this->faker->randomElement(['Pengatur','Penata','Pembina']),
            'status' => $this->faker->randomElement(['Aktif','Nonaktif']),
            'no_telp' => $this->faker->e164PhoneNumber(),
            'tgl_input' => $this->faker->dateTimeThisYear(),
            'petugas_input_id' => null
        ];
    }
}
