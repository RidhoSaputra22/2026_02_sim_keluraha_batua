<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DataRtRw;

class DataRtRwFactory extends Factory
{
    protected $model = DataRtRw::class;

    public function definition(): array
    {
        return [
            'kelurahan' => $this->faker->words(3, true),
            'nik' => $this->faker->unique()->numerify('################'),
            'nama' => $this->faker->words(3, true),
            'jabatan' => $this->faker->words(3, true),
            'rw' => $this->faker->words(3, true),
            'rt' => $this->faker->words(3, true),
            'tgl_mulai' => $this->faker->date(),
            'status' => $this->faker->words(3, true),
            'alamat' => $this->faker->sentence(12),
            'no_telp' => $this->faker->phoneNumber(),
            'no_rekening' => $this->faker->words(3, true),
            'no_npwp' => $this->faker->words(3, true),
        ];;
    }
}
