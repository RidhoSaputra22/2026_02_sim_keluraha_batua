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

        // ─── Kelurahan Batua (dengan biodata) ─────────────────
        $kelurahan = Kelurahan::create([
            'kecamatan_id' => $kecamatan->id,
            'nama' => 'Batua',
            'kode_pos' => '90233',
            'luas_area' => 3.12,
            'alamat_kantor' => 'Jl. Batua Raya No. 1, Kec. Manggala, Kota Makassar',
            'no_telp' => '0411-1234567',
            'email' => 'kelurahan.batua@makassarkota.go.id',
            'website' => 'https://batua.makassarkota.go.id',
            'nama_lurah' => 'H. Muhammad Arif, S.Sos',
            'nip_lurah' => '197506152003121005',
            'visi' => 'Terwujudnya Kelurahan Batua yang aman, nyaman, tertib, dan sejahtera.',
            'misi' => "1. Meningkatkan pelayanan publik yang prima\n2. Meningkatkan partisipasi masyarakat dalam pembangunan\n3. Menjaga keamanan dan ketertiban lingkungan\n4. Mendorong pertumbuhan ekonomi masyarakat",
            'deskripsi' => 'Kelurahan Batua adalah salah satu kelurahan di Kecamatan Manggala, Kota Makassar, Sulawesi Selatan. Kelurahan ini memiliki 13 RW dengan jumlah penduduk yang terus berkembang.',
            'batas_utara' => 'Kelurahan Borong',
            'batas_selatan' => 'Kelurahan Tamangapa',
            'batas_timur' => 'Kelurahan Manggala',
            'batas_barat' => 'Kelurahan Bangkala',
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
        // Batua memiliki 13 RW, masing-masing dengan 1 RT
        $rtPerRw = [
            1 => 1,  2 => 1,  3 => 1,  4 => 1,  5 => 1,
            6 => 1,  7 => 1,  8 => 1,  9 => 1,  10 => 1,
            11 => 1, 12 => 1, 13 => 1,
        ];

        $rwDescriptions = [
            1 => 'RW 001 berada di bagian utara Kelurahan Batua',
            2 => 'RW 002 berada di sekitar Jl. Batua Raya',
            3 => 'RW 003 mencakup area perumahan penduduk',
        ];

        foreach ($rtPerRw as $nomorRw => $jumlahRt) {
            $rw = Rw::create([
                'kelurahan_id' => $kelurahan->id,
                'nomor' => $nomorRw,
                'luas_area' => round(3.12 / 13, 2),
                'deskripsi' => $rwDescriptions[$nomorRw] ?? null,
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
