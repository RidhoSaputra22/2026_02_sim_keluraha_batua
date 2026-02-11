<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\AgendaKegiatan;

class AgendaKegiatanFactory extends Factory
{
    protected $model = AgendaKegiatan::class;

    public function definition(): array
    {
        return [
            'kelurahan_id' => \App\Models\Kelurahan::factory(),
            'hari_kegiatan' => $this->faker->date(),
            'jam' => $this->faker->time(),
            'lokasi' => $this->faker->address(),
            'instansi_id' => \App\Models\Instansi::factory(),
            'perihal' => $this->faker->sentence(4),
            'penanggung_jawab' => $this->faker->name(),
            'keterangan' => $this->faker->sentence(8),
            'arsip_path' => null
        ];
    }
}
