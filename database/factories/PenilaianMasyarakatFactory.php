<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PenilaianMasyarakat;

class PenilaianMasyarakatFactory extends Factory
{
    protected $model = PenilaianMasyarakat::class;

    public function definition(): array
    {
        return [
            'jenis_kelamin' => $this->faker->randomElement(['L','P']),
            'umur' => $this->faker->numberBetween(0, 5000),
            'pendidikan' => $this->faker->words(3, true),
            'pekerjaan' => $this->faker->words(3, true),
            'jenis_layanan' => $this->faker->words(3, true),
            'jumlah_nilai' => $this->faker->numberBetween(0, 500),
            'nilai_rata_rata' => $this->faker->randomFloat(2, 0, 100),
            'wilayah_penugasan' => $this->faker->words(3, true),
        ];;
    }
}
