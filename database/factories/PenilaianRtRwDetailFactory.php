<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PenilaianRtRwDetail;

class PenilaianRtRwDetailFactory extends Factory
{
    protected $model = PenilaianRtRwDetail::class;

    public function definition(): array
    {
        return [
            'periode_id' => \App\Models\PenilaianPeriode::factory(),
            'pengurus_id' => \App\Models\RtRwPengurus::factory(),
            'nilai' => $this->faker->randomFloat(2, 50, 100),
            'standar_nilai' => 75,
            'usulan_nilai_insentif' => $this->faker->numberBetween(0, 500000),
            'lpj_path' => null,
            'arsip_path' => null,
            'created_by' => null
        ];
    }
}
