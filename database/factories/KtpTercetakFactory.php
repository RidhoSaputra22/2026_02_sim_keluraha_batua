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
            'nik' => $this->faker->unique()->numerify('################'),
            'nama' => $this->faker->words(3, true),
            'tempat_lahir' => $this->faker->words(3, true),
            'tanggal_lahir' => $this->faker->date(),
            'jenis_kelamin' => $this->faker->randomElement(['L','P']),
            'agama' => $this->faker->words(3, true),
            'status_kawin' => $this->faker->words(3, true),
            'pendidikan' => $this->faker->words(3, true),
            'alamat' => $this->faker->sentence(12),
            'rt' => $this->faker->words(3, true),
            'rw' => $this->faker->words(3, true),
            'kelurahan' => $this->faker->words(3, true),
            'kecamatan' => $this->faker->words(3, true),
            'tgl_buat' => $this->faker->date(),
            'petugas_input' => $this->faker->words(3, true),
        ];;
    }
}
