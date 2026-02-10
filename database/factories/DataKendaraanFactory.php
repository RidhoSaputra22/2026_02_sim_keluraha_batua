<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DataKendaraan;

class DataKendaraanFactory extends Factory
{
    protected $model = DataKendaraan::class;

    public function definition(): array
    {
        return [
            'jenis_barang' => $this->faker->randomElement(['Motor', 'Mobil', 'Truk', 'Pick Up']),
            'nama_pengemudi' => $this->faker->name(),
            'no_polisi' => $this->faker->bothify('DD #### ??'),
            'no_rangka' => $this->faker->bothify('???###?????????###'),
            'no_mesin' => $this->faker->bothify('??#########'),
            'tahun_perolehan' => $this->faker->numberBetween(2000, (int)date('Y')),
            'merek_type' => $this->faker->randomElement(['Toyota Avanza', 'Honda Beat', 'Yamaha Mio', 'Suzuki Ertiga', 'Daihatsu Xenia']),
            'wilayah_penugasan' => $this->faker->randomElement(['RW 001', 'RW 002', 'RW 003', 'Seluruh Kelurahan']),
            'arsip' => $this->faker->optional()->filePath(),
        ];
    }
}
