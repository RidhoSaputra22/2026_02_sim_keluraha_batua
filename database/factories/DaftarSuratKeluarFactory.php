<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DaftarSuratKeluar;

class DaftarSuratKeluarFactory extends Factory
{
    protected $model = DaftarSuratKeluar::class;

    public function definition(): array
    {
        return [
            'status_esign' => $this->faker->words(3, true),
            'jenis_surat' => $this->faker->randomElement(['SKTM', 'Domisili', 'Usaha', 'Kelahiran']),
            'no_surat' => $this->faker->numerify('###/KEL-BTU/##/####'),
            'tanggal_surat' => $this->faker->date(),
            'nama_dalam_surat' => $this->faker->words(3, true),
            'nama_pemohon' => $this->faker->words(3, true),
            'no_telepon' => $this->faker->numerify('08##########'),
            'tgl_input' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'lainnya' => $this->faker->sentence(12),
            'petugas_input' => $this->faker->numberBetween(1, 10),
            'arsip' => $this->faker->optional()->filePath(),
        ];
    }
}
