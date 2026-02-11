<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Faskes;

class FaskesFactory extends Factory
{
    protected $model = Faskes::class;

    public function definition(): array
    {
        return [
            'kelurahan_id' => \App\Models\Kelurahan::factory(),
            'nama_rs' => $this->faker->company(),
            'alamat' => $this->faker->address(),
            'rw_id' => \App\Models\Rw::factory(),
            'jenis' => $this->faker->randomElement(['RS','Puskesmas','Klinik']),
            'kelas' => $this->faker->randomElement(['A','B','C','D']),
            'jenis_pelayanan' => $this->faker->randomElement(['Rawat Jalan','Rawat Inap','UGD']),
            'akreditasi' => $this->faker->randomElement(['Paripurna','Utama','Madya','Dasar']),
            'telp' => $this->faker->phoneNumber()
        ];
    }
}
