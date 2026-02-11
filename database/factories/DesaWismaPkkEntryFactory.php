<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DesaWismaPkkEntry;

class DesaWismaPkkEntryFactory extends Factory
{
    protected $model = DesaWismaPkkEntry::class;

    public function definition(): array
    {
        return [
            'penduduk_id' => \App\Models\Penduduk::factory(),
            'keluarga_id' => \App\Models\Keluarga::factory(),
            'rt_id' => \App\Models\Rt::factory(),
            'kelurahan_id' => \App\Models\Kelurahan::factory(),
            'keterangan' => $this->faker->sentence(6)
        ];
    }
}
