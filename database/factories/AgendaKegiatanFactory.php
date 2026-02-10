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
            'hari_kegiatan' => $this->faker->date(),
            'jam' => $this->faker->time('H:i'),
            'lokasi' => $this->faker->randomElement(['Kantor Kelurahan', 'Balai RW', 'Aula', 'Lapangan']),
            'instansi_pengirim' => $this->faker->randomElement(['Kecamatan Manggala', 'Dinas Kependudukan', 'Dinas Kesehatan', 'Internal']),
            'perihal' => $this->faker->sentence(5),
            'penanggung_jawab' => $this->faker->name(),
            'keterangan' => $this->faker->sentence(12),
            'arsip' => $this->faker->optional()->filePath(),
        ];
    }
}
