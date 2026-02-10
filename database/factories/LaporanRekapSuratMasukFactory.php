<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\LaporanRekapSuratMasuk;

class LaporanRekapSuratMasukFactory extends Factory
{
    protected $model = LaporanRekapSuratMasuk::class;

    public function definition(): array
    {
        return [
            'kelurahan_desa' => $this->faker->words(3, true),
            'no_surat' => $this->faker->numerify('###/KEL-BTU/##/####'),
            'jenis_surat' => $this->faker->randomElement(['SKTM', 'Domisili', 'Usaha', 'Kelahiran']),
            'sifat_surat' => $this->faker->randomElement(['Biasa', 'Segera', 'Sangat Segera']),
            'asal_surat' => $this->faker->randomElement(['Kecamatan', 'Dinas', 'Internal', 'Warga']),
            'tanggal_diterima' => $this->faker->date(),
        ];
    }
}
