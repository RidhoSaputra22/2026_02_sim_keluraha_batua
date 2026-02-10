<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DataFaskes;

class DataFaskesFactory extends Factory
{
    protected $model = DataFaskes::class;

    public function definition(): array
    {
        return [
            'nama_rs' => $this->faker->words(3, true),
            'alamat' => $this->faker->sentence(12),
            'rw' => $this->faker->words(3, true),
            'jenis' => $this->faker->words(3, true),
            'kelas' => $this->faker->words(3, true),
            'jenis_pelayanan' => $this->faker->words(3, true),
            'akreditasi' => $this->faker->words(3, true),
            'telp' => $this->faker->phoneNumber(),
        ];;
    }
}
