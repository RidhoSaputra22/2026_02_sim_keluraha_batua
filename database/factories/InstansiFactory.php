<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Instansi;

class InstansiFactory extends Factory
{
    protected $model = Instansi::class;

    public function definition(): array
    {
        return [
            'nama' => $this->faker->company(),
            'alamat' => $this->faker->address(),
            'telp' => $this->faker->phoneNumber()
        ];
    }
}
