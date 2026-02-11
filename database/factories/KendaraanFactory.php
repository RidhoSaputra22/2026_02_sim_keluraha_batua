<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Kendaraan;

class KendaraanFactory extends Factory
{
    protected $model = Kendaraan::class;

    public function definition(): array
    {
        return [
            'jenis_barang' => $this->faker->randomElement(['Motor','Mobil','Truk','Pickup']),
            'nama_pengemudi' => $this->faker->name(),
            'no_polisi' => strtoupper($this->faker->bothify('B #### ??')),
            'no_rangka' => strtoupper($this->faker->bothify('###############')),
            'no_mesin' => strtoupper($this->faker->bothify('###########')),
            'tahun_perolehan' => (string)$this->faker->numberBetween(2000, 2026),
            'merek_type' => $this->faker->randomElement(['Toyota','Honda','Suzuki','Daihatsu']) . ' ' . $this->faker->word(),
            'kelurahan_id' => \App\Models\Kelurahan::factory()
        ];
    }
}
