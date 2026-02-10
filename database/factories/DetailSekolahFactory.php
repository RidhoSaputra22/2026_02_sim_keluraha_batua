<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DetailSekolah;

class DetailSekolahFactory extends Factory
{
    protected $model = DetailSekolah::class;

    public function definition(): array
    {
        return [
            'npsn' => $this->faker->words(3, true),
            'nama_sekolah' => $this->faker->words(3, true),
            'tahun_ajar' => $this->faker->words(3, true),
            'jumlah_siswa' => $this->faker->numberBetween(0, 5000),
            'rombel' => $this->faker->numberBetween(0, 5000),
            'jumlah_guru' => $this->faker->numberBetween(0, 5000),
            'jumlah_pegawai' => $this->faker->numberBetween(0, 5000),
            'ruang_kelas' => $this->faker->numberBetween(0, 5000),
            'jumlah_r_lab' => $this->faker->numberBetween(0, 5000),
            'jumlah_r_perpus' => $this->faker->numberBetween(0, 5000),
        ];;
    }
}
