<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Pk5;

class Pk5Factory extends Factory
{
    protected $model = Pk5::class;

    public function definition(): array
    {
        return [
            'nip' => null, // Will be set manually in seeder if needed, or left null to avoid FK constraint
            'nama' => $this->faker->name(),
            'jabatan' => $this->faker->randomElement(['Ketua', 'Wakil Ketua', 'Sekretaris', 'Bendahara', 'Anggota']),
            'pangkat' => $this->faker->randomElement(['Pembina', 'Pengarah', 'Pelaksana', 'Anggota Biasa']),
            'status' => $this->faker->randomElement(['Aktif', 'Nonaktif', 'Cuti']),
            'no_telp' => $this->faker->numerify('08##########'),
            'tgl_input' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'petugas_input' => $this->faker->numberBetween(1, 10),
        ];
    }
}
