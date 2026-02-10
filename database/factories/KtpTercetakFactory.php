<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\KtpTercetak;

class KtpTercetakFactory extends Factory
{
    protected $model = KtpTercetak::class;

    public function definition(): array
    {
        return [
            'nik' => null,
            'nama' => $this->faker->name(),
            'tempat_lahir' => $this->faker->city(),
            'tanggal_lahir' => $this->faker->date(),
            'jenis_kelamin' => $this->faker->randomElement(['L','P']),
            'agama' => $this->faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu']),
            'status_kawin' => $this->faker->randomElement(['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']),
            'pendidikan' => $this->faker->randomElement(['Tidak/Belum Sekolah', 'SD', 'SMP', 'SMA', 'D3', 'S1', 'S2']),
            'alamat' => $this->faker->sentence(12),
            'rt' => str_pad($this->faker->numberBetween(1, 20), 3, '0', STR_PAD_LEFT),
            'rw' => str_pad($this->faker->numberBetween(1, 10), 3, '0', STR_PAD_LEFT),
            'kelurahan' => $this->faker->randomElement(['Batua', 'Bangkala', 'Tamangapa', 'Antang']),
            'kecamatan' => $this->faker->randomElement(['Manggala', 'Panakkukang', 'Tamalate']),
            'tgl_buat' => $this->faker->date(),
            'petugas_input' => $this->faker->numberBetween(1, 10),
        ];
    }
}
