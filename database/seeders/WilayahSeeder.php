<?php

namespace Database\Seeders;

use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Rt;
use App\Models\Rw;
use Illuminate\Database\Seeder;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Kecamatan Manggala, Kota Makassar ────────────────
        $kecamatan = Kecamatan::create(['nama' => 'Manggala']);

        // ─── Kelurahan Batua ──────────────────────────────────
        $kelurahan = Kelurahan::create([
            'kecamatan_id' => $kecamatan->id,
            'nama'         => 'Batua',
        ]);

        // ─── Kelurahan lain di Kecamatan Manggala (referensi) ─
        $kelurahanLain = [
            'Antang',
            'Bangkala',
            'Borong',
            'Manggala',
            'Tamangapa',
        ];

        foreach ($kelurahanLain as $nama) {
            Kelurahan::create([
                'kecamatan_id' => $kecamatan->id,
                'nama'         => $nama,
            ]);
        }

        // ─── RW & RT untuk Kelurahan Batua ────────────────────
        // Batua memiliki 8 RW, masing-masing dengan 3-5 RT
        $rtPerRw = [
            1 => 5,  // RW 001 → RT 001-005
            2 => 4,  // RW 002 → RT 001-004
            3 => 5,  // RW 003 → RT 001-005
            4 => 4,  // RW 004 → RT 001-004
            5 => 3,  // RW 005 → RT 001-003
            6 => 4,  // RW 006 → RT 001-004
            7 => 3,  // RW 007 → RT 001-003
            8 => 4,  // RW 008 → RT 001-004
        ];

        foreach ($rtPerRw as $nomorRw => $jumlahRt) {
            $rw = Rw::create([
                'kelurahan_id' => $kelurahan->id,
                'nomor'        => $nomorRw,
            ]);

            for ($i = 1; $i <= $jumlahRt; $i++) {
                Rt::create([
                    'rw_id' => $rw->id,
                    'nomor' => $i,
                ]);
            }
        }
    }
}
