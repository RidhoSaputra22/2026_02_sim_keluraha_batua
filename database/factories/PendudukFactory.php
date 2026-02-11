<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Penduduk;

class PendudukFactory extends Factory
{
    protected $model = Penduduk::class;

    public function definition(): array
    {
        return [
            'nik' => $this->faker->unique()->numerify('################'),
            'nama' => $this->faker->name(),
            'alamat' => $this->faker->address(),
            'keluarga_id' => \App\Models\Keluarga::factory(),
            'rt_id' => \App\Models\Rt::factory(),
            'jenis_kelamin' => $this->faker->randomElement(['L','P']),
            'gol_darah' => $this->faker->randomElement(['A','B','AB','O']),
            'agama' => $this->faker->randomElement(['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu']),
            'status_kawin' => $this->faker->randomElement(['Belum Kawin','Kawin','Cerai Hidup','Cerai Mati']),
            'pendidikan' => $this->faker->randomElement(['SD','SMP','SMA','D3','S1','S2']),
            'status_data' => $this->faker->randomElement(['Aktif','Nonaktif']),
            'tgl_input' => $this->faker->dateTimeThisYear(),
            'petugas_input_id' => null
        ];
    }
}
