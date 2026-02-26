<?php

namespace Database\Seeders;

use App\Models\JabatanRtRw;
use Illuminate\Database\Seeder;

class ReferensiSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Jabatan RT/RW ───────────────────────────────────
        $jabatan = [
            'Ketua RT',
            'Wakil Ketua RT',
            'Sekretaris RT',
            'Bendahara RT',
            'Ketua RW',
            'Wakil Ketua RW',
            'Sekretaris RW',
            'Bendahara RW',
        ];

        foreach ($jabatan as $nama) {
            JabatanRtRw::create(['nama' => $nama]);
        }
    }
}
