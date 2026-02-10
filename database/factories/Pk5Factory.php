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
