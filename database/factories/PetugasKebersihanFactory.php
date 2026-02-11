<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PetugasKebersihan;

class PetugasKebersihanFactory extends Factory
{
    protected $model = PetugasKebersihan::class;

    public function definition(): array
    {
        return [
            'kelurahan_id' => \App\Models\Kelurahan::factory(),
            'penduduk_id' => null,
            'nama' => $this->faker->name(),
            'nik' => $this->faker->numerify('################'),
            'unit_kerja' => $this->faker->randomElement(['DLH','Kelurahan','RW']),
            'jenis_kelamin' => $this->faker->randomElement(['L','P']),
            'pekerjaan' => 'Petugas Kebersihan',
            'lokasi' => $this->faker->streetName(),
            'status' => $this->faker->randomElement(['Aktif','Nonaktif'])
        ];
    }
}
