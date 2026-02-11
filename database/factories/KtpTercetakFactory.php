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
            'penduduk_id' => \App\Models\Penduduk::factory(),
            'tgl_buat' => $this->faker->date(),
            'petugas_input_id' => null
        ];
    }
}
