<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PegawaiStaff;

class PegawaiStaffFactory extends Factory
{
    protected $model = PegawaiStaff::class;

    public function definition(): array
    {
        return [
            'nik' => $this->faker->unique()->numerify('################'),
            'nip' => $this->faker->unique()->numerify('##################'),
            'nama' => $this->faker->name(),
            'jabatan' => $this->faker->randomElement(['Lurah', 'Sekretaris Lurah', 'Kasi Pemerintahan', 'Kasi Pelayanan', 'Staff']),
            'golongan' => $this->faker->randomElement(['III/a', 'III/b', 'III/c', 'III/d', 'IV/a']),
            'pangkat' => $this->faker->randomElement(['Penata Muda', 'Penata', 'Penata Tk. I', 'Pembina']),
            'status_pegawai' => $this->faker->randomElement(['aktif', 'nonaktif']),
            'tgl_input' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'petugas_input' => null,
            'no_urut' => $this->faker->numberBetween(1, 50),
        ];
    }
}
