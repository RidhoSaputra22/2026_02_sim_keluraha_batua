<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\RtRwPengurus;

class RtRwPengurusFactory extends Factory
{
    protected $model = RtRwPengurus::class;

    public function definition(): array
    {
        return [
            'kelurahan_id' => \App\Models\Kelurahan::factory(),
            'penduduk_id' => \App\Models\Penduduk::factory(),
            'jabatan_id' => \App\Models\JabatanRtRw::factory(),
            'rw_id' => \App\Models\Rw::factory(),
            'rt_id' => \App\Models\Rt::factory(),
            'tgl_mulai' => $this->faker->date(),
            'status' => $this->faker->randomElement(['Aktif','Nonaktif']),
            'alamat' => $this->faker->address(),
            'no_telp' => $this->faker->e164PhoneNumber(),
            'no_rekening' => $this->faker->numerify('################'),
            'no_npwp' => $this->faker->numerify('###############')
        ];
    }
}
