<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Surat;

class SuratFactory extends Factory
{
    protected $model = Surat::class;

    public function definition(): array
    {
        return [
            'kelurahan_id' => \App\Models\Kelurahan::factory(),
            'arah' => $this->faker->randomElement(['masuk','keluar']),
            'nomor_surat' => $this->faker->bothify('##/SK/????/####'),
            'tanggal_surat' => $this->faker->date(),
            'tanggal_diterima' => $this->faker->optional()->date(),
            'jenis_id' => \App\Models\SuratJenis::factory(),
            'sifat_id' => \App\Models\SuratSifat::factory(),
            'instansi_id' => \App\Models\Instansi::factory(),
            'layanan_publik_id' => \App\Models\LayananPublik::factory(),
            'tujuan_surat' => $this->faker->company(),
            'perihal' => $this->faker->sentence(3),
            'uraian' => $this->faker->paragraph(),
            'nama_dalam_surat' => $this->faker->name(),
            'pemohon_id' => \App\Models\Pemohon::factory(),
            'status_esign' => $this->faker->randomElement(['draft','proses','signed','reject']),
            'tgl_input' => $this->faker->dateTimeThisYear(),
            'petugas_input_id' => null,
            'arsip_path' => null
        ];
    }
}
