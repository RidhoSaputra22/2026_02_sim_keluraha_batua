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
            'no_surat' => $this->faker->words(3, true),
            'jenis_surat' => $this->faker->words(3, true),
            'sifat_surat' => $this->faker->words(3, true),
            'asal_surat' => $this->faker->words(3, true),
            'tanggal_diterima' => $this->faker->date(),
        ];;
    }
}
