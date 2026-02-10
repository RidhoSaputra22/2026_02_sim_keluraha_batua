<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Penandatanganan;

class PenandatangananFactory extends Factory
{
    protected $model = Penandatanganan::class;

    public function definition(): array
    {
        return [
            'nik' => $this->faker->unique()->numerify('################'),
            'nip' => null,
            'nama' => $this->faker->name(),
            'jabatan' => $this->faker->randomElement(['Lurah', 'Sekretaris Lurah', 'Kasi Pemerintahan']),
            'pangkat' => $this->faker->randomElement(['Penata Muda', 'Penata', 'Penata Tk. I', 'Pembina']),
            'golongan' => $this->faker->randomElement(['III/a', 'III/b', 'III/c', 'III/d', 'IV/a']),
            'status' => $this->faker->randomElement(['aktif', 'nonaktif']),
            'no_telp' => $this->faker->numerify('08##########'),
            'email' => $this->faker->unique()->safeEmail(),
            'alamat' => $this->faker->address(),
            'tgl_mulai' => $this->faker->date(),
            'tgl_input' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'petugas_input' => null,
        ];
    }
}
