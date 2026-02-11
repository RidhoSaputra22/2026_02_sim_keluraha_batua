<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Imb;

class ImbFactory extends Factory
{
    protected $model = Imb::class;

    public function definition(): array
    {
        return [
            'kelurahan_id' => \App\Models\Kelurahan::factory(),
            'nama_pemohon' => $this->faker->name(),
            'alamat_pemohon' => $this->faker->address(),
            'alamat_bangunan' => $this->faker->address(),
            'status_luas_tanah' => $this->faker->randomElement(['Milik','Sewa']),
            'nama_pada_surat' => $this->faker->name(),
            'penggunaan_fungsi_gedung' => $this->faker->randomElement(['Rumah Tinggal','Ruko','Gudang','Kantor'])
        ];
    }
}
