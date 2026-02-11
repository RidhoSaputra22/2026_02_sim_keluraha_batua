<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Sekolah;

class SekolahFactory extends Factory
{
    protected $model = Sekolah::class;

    public function definition(): array
    {
        return [
            'kelurahan_id' => \App\Models\Kelurahan::factory(),
            'npsn' => $this->faker->unique()->numerify('########'),
            'nama_sekolah' => $this->faker->company() . ' School',
            'jenjang' => $this->faker->randomElement(['SD','SMP','SMA','SMK']),
            'status' => $this->faker->randomElement(['Negeri','Swasta']),
            'alamat' => $this->faker->address(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'tahun_ajar' => '2025/2026',
            'jumlah_siswa' => $this->faker->numberBetween(50, 800),
            'rombel' => $this->faker->numberBetween(3, 30),
            'jumlah_guru' => $this->faker->numberBetween(5, 80),
            'jumlah_pegawai' => $this->faker->numberBetween(2, 30),
            'ruang_kelas' => $this->faker->numberBetween(3, 40),
            'jumlah_r_lab' => $this->faker->numberBetween(0, 10),
            'jumlah_r_perpus' => $this->faker->numberBetween(0, 2)
        ];
    }
}
