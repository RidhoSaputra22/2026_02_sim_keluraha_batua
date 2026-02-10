<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DataKeluarga;

class DataKeluargaFactory extends Factory
{
    protected $model = DataKeluarga::class;

    public function definition(): array
    {
        return [
            'no_kk' => $this->faker->unique()->numerify('################'),
            'nama_kepala_keluarga' => $this->faker->name(),
            'nik_kepala_keluarga' => $this->faker->unique()->numerify('################'),
            'jumlah_anggota_keluarga' => $this->faker->numberBetween(1, 10),
            'alamat' => $this->faker->address(),
            'rw' => str_pad($this->faker->numberBetween(1, 10), 3, '0', STR_PAD_LEFT),
            'rt' => str_pad($this->faker->numberBetween(1, 15), 3, '0', STR_PAD_LEFT),
            'kecamatan' => 'Manggala',
            'kelurahan' => 'Batua',
            'status' => 'aktif',
            'tgl_input' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'petugas_input' => null,
        ];
    }
}
