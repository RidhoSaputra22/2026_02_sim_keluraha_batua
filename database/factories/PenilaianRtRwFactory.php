<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PenilaianRtRw;

class PenilaianRtRwFactory extends Factory
{
    protected $model = PenilaianRtRw::class;

    public function definition(): array
    {
        return [
            'nik' => null,
            'nama' => $this->faker->name(),
            'jabatan' => $this->faker->randomElement(['Ketua RT', 'Ketua RW', 'Sekretaris RT', 'Sekretaris RW', 'Bendahara RT', 'Bendahara RW']),
            'nilai' => $this->faker->randomFloat(2, 0, 100),
            'standar_nilai' => $this->faker->randomFloat(2, 0, 100),
            'usulan_nilai_insentif' => $this->faker->randomFloat(2, 0, 500000),
            'periode_penilaian' => $this->faker->date(),
            'lpj' => $this->faker->optional()->filePath(),
            'arsip' => $this->faker->optional()->filePath(),
        ];
    }
}
