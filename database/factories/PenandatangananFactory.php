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
            'nip' => $this->faker->unique()->numerify('################'),
            'nama' => $this->faker->words(3, true),
            'jabatan' => $this->faker->words(3, true),
            'pangkat' => $this->faker->words(3, true),
            'status' => $this->faker->words(3, true),
            'no_telp' => $this->faker->phoneNumber(),
            'tgl_input' => $this->faker->date(),
            'petugas_input' => $this->faker->words(3, true),
        ];;
    }
}
