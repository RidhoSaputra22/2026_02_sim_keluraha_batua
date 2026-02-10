<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DataEkspedisi;

class DataEkspedisiFactory extends Factory
{
    protected $model = DataEkspedisi::class;

    public function definition(): array
    {
        return [
            'pemilik_usaha' => $this->faker->words(3, true),
            'ekspedisi' => $this->faker->words(3, true),
            'alamat' => $this->faker->sentence(12),
            'penanggung_jawab' => $this->faker->words(3, true),
            'telp_hp' => $this->faker->numerify('08##########'),
            'kegiatan_ekspedisi' => $this->faker->words(3, true),
        ];
    }
}
