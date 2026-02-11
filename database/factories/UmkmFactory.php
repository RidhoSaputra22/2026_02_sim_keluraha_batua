<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Umkm;

class UmkmFactory extends Factory
{
    protected $model = Umkm::class;

    public function definition(): array
    {
        return [
            'kelurahan_id' => \App\Models\Kelurahan::factory(),
            'rt_id' => \App\Models\Rt::factory(),
            'penduduk_id' => null,
            'nama_pemilik' => $this->faker->name(),
            'nik_pemilik' => $this->faker->numerify('################'),
            'no_hp' => $this->faker->e164PhoneNumber(),
            'nama_ukm' => $this->faker->company(),
            'alamat' => $this->faker->address(),
            'sektor_umkm' => $this->faker->randomElement(['Kuliner','Jasa','Kerajinan','Perdagangan'])
        ];
    }
}
