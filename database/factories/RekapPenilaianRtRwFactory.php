<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\RekapPenilaianRtRw;

class RekapPenilaianRtRwFactory extends Factory
{
    protected $model = RekapPenilaianRtRw::class;

    public function definition(): array
    {
        return [
            'nik' => null,
            'nama' => $this->faker->name(),
            'jabatan' => $this->faker->randomElement(['Ketua', 'Sekretaris', 'Anggota']),
            'rw' => str_pad($this->faker->numberBetween(1, 10), 3, '0', STR_PAD_LEFT),
            'tanggal' => $this->faker->date(),
            'periode_penilaian' => $this->faker->date(),
            'nilai' => $this->faker->randomFloat(2, 0, 100),
            'kelurahan' => $this->faker->randomElement(['Batua', 'Bangkala', 'Tamangapa', 'Antang']),
            'arsip' => $this->faker->optional()->filePath(),
        ];
    }
}
