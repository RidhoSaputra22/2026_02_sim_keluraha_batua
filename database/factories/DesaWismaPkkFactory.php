<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DesaWismaPkk;

class DesaWismaPkkFactory extends Factory
{
    protected $model = DesaWismaPkk::class;

    public function definition(): array
    {
        return [
            'nik' => null,
            'nama' => $this->faker->name(),
            'no_kk' => null, // Will be set manually in seeder if needed, or left null to avoid FK constraint
            'kecamatan' => $this->faker->randomElement(['Manggala', 'Panakkukang', 'Tamalate']),
            'kelurahan' => $this->faker->randomElement(['Batua', 'Bangkala', 'Tamangapa', 'Antang']),
            'rt' => str_pad($this->faker->numberBetween(1, 20), 3, '0', STR_PAD_LEFT),
            'rw' => str_pad($this->faker->numberBetween(1, 10), 3, '0', STR_PAD_LEFT),
            'alamat' => $this->faker->sentence(12),
            'jenis_kelamin' => $this->faker->randomElement(['L','P']),
            'agama' => $this->faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu']),
            'status_kawin' => $this->faker->randomElement(['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']),
            'pendidikan' => $this->faker->randomElement(['Tidak/Belum Sekolah', 'SD', 'SMP', 'SMA', 'D3', 'S1', 'S2']),
            'status_data' => $this->faker->randomElement(['aktif', 'pindah', 'meninggal']),
            'tgl_input' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'petugas_input' => $this->faker->numberBetween(1, 10),
            'arsip' => $this->faker->optional()->filePath(),
        ];
    }
}
