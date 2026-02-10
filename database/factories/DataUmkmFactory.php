<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DataUmkm;

class DataUmkmFactory extends Factory
{
    protected $model = DataUmkm::class;

    public function definition(): array
    {
        return [
            'nama_pemilik' => $this->faker->name(),
            'nik' => null, // Will be set manually in seeder if needed, or left null to avoid FK constraint
            'no_hp' => $this->faker->numerify('08##########'),
            'nama_ukm' => $this->faker->company(),
            'alamat' => $this->faker->address(),
            'rt' => str_pad($this->faker->numberBetween(1, 20), 3, '0', STR_PAD_LEFT),
            'rw' => str_pad($this->faker->numberBetween(1, 10), 3, '0', STR_PAD_LEFT),
            'sektor_umkm' => $this->faker->randomElement(['Kuliner', 'Perdagangan', 'Jasa', 'Kerajinan', 'Pertanian', 'Peternakan']),
        ];
    }
}
