<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DataRtRw;

class DataRtRwFactory extends Factory
{
    protected $model = DataRtRw::class;

    public function definition(): array
    {
        return [
            'kelurahan' => 'Batua',
            'kecamatan' => 'Manggala',
            'nik' => null,
            'nama' => $this->faker->name(),
            'jabatan' => $this->faker->randomElement(['RT', 'RW']),
            'rw' => str_pad($this->faker->numberBetween(1, 10), 3, '0', STR_PAD_LEFT),
            'rt' => str_pad($this->faker->numberBetween(1, 15), 3, '0', STR_PAD_LEFT),
            'tgl_mulai' => $this->faker->date(),
            'status' => $this->faker->randomElement(['aktif', 'nonaktif']),
            'alamat' => $this->faker->address(),
            'no_telp' => $this->faker->numerify('08##########'),
            'no_rekening' => $this->faker->numerify('##########'),
            'no_npwp' => $this->faker->numerify('##.###.###.#-###.###'),
        ];
    }
}
