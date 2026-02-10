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
            'nama' => $this->faker->name(),
            'nik' => null,
            'unit_kerja' => $this->faker->randomElement(['Kebersihan Umum', 'Taman', 'Drainase']),
            'jenis_kelamin' => $this->faker->randomElement(['L','P']),
            'pekerjaan' => $this->faker->randomElement(['Petugas Kebersihan', 'Koordinator', 'Supervisor']),
            'lokasi' => $this->faker->randomElement(['RW 001', 'RW 002', 'RW 003', 'Pasar']),
            'status' => $this->faker->randomElement(['Aktif', 'Nonaktif', 'Cuti']),
        ];
    }
}
