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
            'nama' => 'Batua',
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
                'nama' => $nama,
            ]);
        }

        // ─── RW & RT untuk Kelurahan Batua ────────────────────
        // Batua memiliki 8 RW, masing-masing dengan 3-5 RT
        $rtPerRw = [
            1 => 1,  // RW 001 → RT 001
            2 => 1,  // RW 002 → RT 001
            3 => 1,  // RW 003 → RT 001
            4 => 1,  // RW 004 → RT 001
            5 => 1,  // RW 005 → RT 001
            6 => 1,  // RW 006 → RT 001
            7 => 1,  // RW 007 → RT 001
            8 => 1,  // RW 008 → RT 001
            9 => 1,  // RW 009 → RT 001
            10 => 1, // RW 010 → RT 001
            11 => 1, // RW 011 → RT 001
            12 => 1, // RW 012 → RT 001
            13 => 1, // RW 012 → RT 001
        ];

        foreach ($rtPerRw as $nomorRw => $jumlahRt) {
            $rw = Rw::create([
                'kelurahan_id' => $kelurahan->id,
                'nomor' => $nomorRw,
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
