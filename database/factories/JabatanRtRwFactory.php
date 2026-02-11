<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\JabatanRtRw;

class JabatanRtRwFactory extends Factory
{
    protected $model = JabatanRtRw::class;

    public function definition(): array
    {
        return [
            'nama' => $this->faker->unique()->randomElement(['Ketua RT','Ketua RW','Sekretaris','Bendahara'])
        ];
    }
}
