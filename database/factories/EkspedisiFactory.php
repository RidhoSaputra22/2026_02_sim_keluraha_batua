<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Ekspedisi;

class EkspedisiFactory extends Factory
{
    protected $model = Ekspedisi::class;

    public function definition(): array
    {
        return [
            'kelurahan_id' => \App\Models\Kelurahan::factory(),
            'pemilik_usaha' => $this->faker->name(),
            'ekspedisi' => $this->faker->company(),
            'alamat' => $this->faker->address(),
            'penanggung_jawab' => $this->faker->name(),
            'telp_hp' => $this->faker->e164PhoneNumber(),
            'kegiatan_ekspedisi' => $this->faker->sentence(4)
        ];
    }
}
