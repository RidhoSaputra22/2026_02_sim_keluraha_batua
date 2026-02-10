<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DataPenduduk;

class DataPendudukFactory extends Factory
{
    protected $model = DataPenduduk::class;

    public function definition(): array
    {
        return [
            'nik' => $this->faker->unique()->numerify('################'),
            'nama' => $this->faker->name(),
            'no_kk' => null,
            'tempat_lahir' => $this->faker->city(),
            'tanggal_lahir' => $this->faker->date(),
            'kecamatan' => 'Manggala',
            'kelurahan' => 'Batua',
            'rt' => str_pad($this->faker->numberBetween(1, 15), 3, '0', STR_PAD_LEFT),
            'rw' => str_pad($this->faker->numberBetween(1, 10), 3, '0', STR_PAD_LEFT),
            'alamat' => $this->faker->address(),
            'jenis_kelamin' => $this->faker->randomElement(['L', 'P']),
            'gol_darah' => $this->faker->randomElement(['A', 'B', 'AB', 'O']),
            'agama' => $this->faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha']),
            'status_kawin' => $this->faker->randomElement(['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']),
            'pekerjaan' => $this->faker->jobTitle(),
            'pendidikan' => $this->faker->randomElement(['SD', 'SMP', 'SMA', 'D3', 'S1', 'S2']),
            'kewarganegaraan' => 'WNI',
            'status_data' => 'aktif',
            'tgl_input' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'petugas_input' => null,
        ];
    }
}
