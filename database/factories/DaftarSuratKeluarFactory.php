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
            'jenis_surat' => $this->faker->words(3, true),
            'no_surat' => $this->faker->words(3, true),
            'tanggal_surat' => $this->faker->date(),
            'nama_dalam_surat' => $this->faker->words(3, true),
            'nama_pemohon' => $this->faker->words(3, true),
            'no_telepon' => $this->faker->phoneNumber(),
            'tgl_input' => $this->faker->date(),
            'lainnya' => $this->faker->sentence(12),
            'petugas_input' => $this->faker->words(3, true),
            'arsip' => $this->faker->optional()->filePath(),
        ];;
    }
}
