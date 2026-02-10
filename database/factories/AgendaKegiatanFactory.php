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
            'hari_kegiatan' => $this->faker->words(3, true),
            'jam' => $this->faker->words(3, true),
            'lokasi' => $this->faker->words(3, true),
            'instansi_pengirim' => $this->faker->words(3, true),
            'perihal' => $this->faker->words(3, true),
            'penanggung_jawab' => $this->faker->words(3, true),
            'keterangan' => $this->faker->sentence(12),
            'arsip' => $this->faker->optional()->filePath(),
        ];;
    }
}
