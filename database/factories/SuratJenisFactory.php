<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SuratJenis;

class SuratJenisFactory extends Factory
{
    protected $model = SuratJenis::class;

    public function definition(): array
    {
        return [
            'nama' => $this->faker->unique()->randomElement(['Surat Keterangan','Surat Domisili','Surat Pengantar','Surat Himbauan','Surat Lain-lain'])
        ];
    }
}
