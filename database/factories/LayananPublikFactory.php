<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\LayananPublik;

class LayananPublikFactory extends Factory
{
    protected $model = LayananPublik::class;

    public function definition(): array
    {
        return [
            'nama' => $this->faker->unique()->randomElement(['Domisili','Keterangan Usaha','Keterangan Tidak Mampu','Pengantar Nikah','SKCK'])
        ];
    }
}
