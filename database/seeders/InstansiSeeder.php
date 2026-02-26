<?php

namespace Database\Seeders;

use App\Models\Instansi;
use Illuminate\Database\Seeder;

class InstansiSeeder extends Seeder
{
    public function run(): void
    {
        $instansiData = [
            ['nama' => 'Kantor Kelurahan Batua',              'alamat' => 'Jl. Batua Raya No. 1, Makassar',       'telp' => '0411-441234'],
            ['nama' => 'Kantor Kecamatan Manggala',           'alamat' => 'Jl. Manggala Raya, Makassar',          'telp' => '0411-442345'],
            ['nama' => 'Dinas Kependudukan & Catatan Sipil',  'alamat' => 'Jl. Ahmad Yani No. 2, Makassar',       'telp' => '0411-443456'],
            ['nama' => 'Kantor Urusan Agama Kec. Manggala',   'alamat' => 'Jl. Manggala Raya No. 10, Makassar',   'telp' => '0411-444567'],
            ['nama' => 'Puskesmas Batua',                     'alamat' => 'Jl. Batua Raya No. 15, Makassar',      'telp' => '0411-445678'],
            ['nama' => 'Polsek Manggala',                     'alamat' => 'Jl. Manggala No. 5, Makassar',         'telp' => '0411-446789'],
            ['nama' => 'BPN Kota Makassar',                   'alamat' => 'Jl. AP Pettarani, Makassar',           'telp' => '0411-447890'],
            ['nama' => 'Dinas Sosial Kota Makassar',          'alamat' => 'Jl. Balaikota No. 8, Makassar',        'telp' => '0411-448901'],
            ['nama' => 'PT. PLN (Persero) Area Makassar',     'alamat' => 'Jl. Hertasning, Makassar',             'telp' => '0411-449012'],
            ['nama' => 'PDAM Kota Makassar',                  'alamat' => 'Jl. Dr. Ratulangi, Makassar',          'telp' => '0411-450123'],
        ];

        foreach ($instansiData as $data) {
            Instansi::create($data);
        }
    }
}
