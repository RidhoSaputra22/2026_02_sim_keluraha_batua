<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Rt;

class RtFactory extends Factory
{
    protected $model = Rt::class;

    public function definition(): array
    {
        return [
            'rw_id' => \App\Models\Rw::factory(),
            'nomor' => $this->faker->numberBetween(1, 20)
        ];
    }
}
