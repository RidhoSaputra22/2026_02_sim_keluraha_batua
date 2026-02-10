<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\LaporanRekapSuratKeluar;

class LaporanRekapSuratKeluarFactory extends Factory
{
    protected $model = LaporanRekapSuratKeluar::class;

    public function definition(): array
    {
        return [
            'kelurahan_desa' => $this->faker->words(3, true),
            'nama_layanan_publik' => $this->faker->words(3, true),
            'nama_pengguna_layanan' => $this->faker->words(3, true),
            'tgl_mengurus_layanan' => $this->faker->date(),
            'no_hp_wa_aktif' => $this->faker->phoneNumber(),
            'email_aktif' => $this->faker->safeEmail(),
        ];;
    }
}
