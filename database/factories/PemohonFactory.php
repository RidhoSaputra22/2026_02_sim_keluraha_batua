<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Pemohon;

class PemohonFactory extends Factory
{
    protected $model = Pemohon::class;

    public function definition(): array
    {
        return [
            'penduduk_id' => null,
            'nama' => $this->faker->name(),
            'no_hp_wa' => $this->faker->e164PhoneNumber(),
            'email' => $this->faker->safeEmail()
        ];
    }
}
