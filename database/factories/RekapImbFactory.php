<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\RekapImb;

class RekapImbFactory extends Factory
{
    protected $model = RekapImb::class;

    public function definition(): array
    {
        return [
            'nama_pemohon' => $this->faker->name(),
            'alamat_pemohon' => $this->faker->sentence(12),
            'alamat_bangunan' => $this->faker->sentence(12),
            'status_luas_tanah' => $this->faker->randomElement(['Milik Sendiri', 'Sewa', 'Warisan']),
            'nama_pada_surat' => $this->faker->name(),
            'penggunaan_fungsi_gedung' => $this->faker->randomElement(['Rumah Tinggal', 'Ruko', 'Kantor', 'Gudang']),
        ];
    }
}
