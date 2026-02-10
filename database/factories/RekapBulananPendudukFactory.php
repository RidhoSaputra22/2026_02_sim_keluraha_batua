<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\RekapBulananPenduduk;

class RekapBulananPendudukFactory extends Factory
{
    protected $model = RekapBulananPenduduk::class;

    public function definition(): array
    {
        return [
            'kelurahan' => $this->faker->words(3, true),
            'periode' => $this->faker->date(),
            'data_laki_laki' => $this->faker->numberBetween(0, 5000),
            'data_perempuan' => $this->faker->numberBetween(0, 5000),
            'data_laki_laki_wna' => $this->faker->numberBetween(0, 5000),
            'data_perempuan_wna' => $this->faker->numberBetween(0, 5000),
            'lahir_laki_laki' => $this->faker->numberBetween(0, 5000),
            'lahir_perempuan' => $this->faker->numberBetween(0, 5000),
            'lahir_laki_laki_wna' => $this->faker->numberBetween(0, 5000),
            'lahir_perempuan_wna' => $this->faker->numberBetween(0, 5000),
            'kematian_laki_laki' => $this->faker->numberBetween(0, 5000),
            'kematian_perempuan' => $this->faker->numberBetween(0, 5000),
            'kematian_laki_laki_wna' => $this->faker->numberBetween(0, 5000),
            'kematian_perempuan_wna' => $this->faker->numberBetween(0, 5000),
            'datang_laki_laki' => $this->faker->numberBetween(0, 5000),
            'datang_perempuan' => $this->faker->numberBetween(0, 5000),
            'datang_laki_laki_wna' => $this->faker->numberBetween(0, 5000),
            'datang_perempuan_wna' => $this->faker->numberBetween(0, 5000),
            'pindah_laki_laki' => $this->faker->numberBetween(0, 5000),
            'pindah_perempuan' => $this->faker->numberBetween(0, 5000),
            'pindah_laki_laki_wna' => $this->faker->numberBetween(0, 5000),
            'pindah_perempuan_wna' => $this->faker->numberBetween(0, 5000),
            'pend_laki_laki' => $this->faker->numberBetween(0, 5000),
            'pend_perempuan' => $this->faker->numberBetween(0, 5000),
            'pend_laki_laki_wna' => $this->faker->numberBetween(0, 5000),
            'pend_perempuan_wna' => $this->faker->numberBetween(0, 5000),
        ];;
    }
}
