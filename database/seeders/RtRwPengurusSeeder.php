<?php

namespace Database\Seeders;

use App\Models\JabatanRtRw;
use App\Models\Kelurahan;
use App\Models\PenilaianPeriode;
use App\Models\PenilaianRtRwDetail;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\RtRwPengurus;
use App\Models\Rw;
use App\Models\User;
use Illuminate\Database\Seeder;

class RtRwPengurusSeeder extends Seeder
{
    public function run(): void
    {
        $kelurahan = Kelurahan::where('nama', 'Batua')->first();
        $adminId   = User::whereHas('role', fn ($q) => $q->where('name', 'admin'))->first()?->id;

        $jabatanKetuaRw  = JabatanRtRw::where('nama', 'Ketua RW')->first();
        $jabatanKetuaRt  = JabatanRtRw::where('nama', 'Ketua RT')->first();
        $jabatanSekRw    = JabatanRtRw::where('nama', 'Sekretaris RW')->first();
        $jabatanSekRt    = JabatanRtRw::where('nama', 'Sekretaris RT')->first();

        // Ambil penduduk laki-laki dewasa untuk dijadikan pengurus
        $pendudukLk = Penduduk::where('jenis_kelamin', 'L')
            ->where('status_kawin', 'Kawin')
            ->get();

        $idx = 0;
        $pengurusIds = [];

        // ─── Ketua RW untuk setiap RW ────────────────────────
        $rwList = Rw::where('kelurahan_id', $kelurahan->id)->orderBy('nomor')->get();

        foreach ($rwList as $rw) {
            if (!isset($pendudukLk[$idx])) break;

            $pengurus = RtRwPengurus::create([
                'kelurahan_id' => $kelurahan->id,
                'penduduk_id'  => $pendudukLk[$idx]->id,
                'jabatan_id'   => $jabatanKetuaRw->id,
                'rw_id'        => $rw->id,
                'rt_id'        => null,
                'tgl_mulai'    => '2024-01-01',
                'status'       => 'Aktif',
                'alamat'       => $pendudukLk[$idx]->alamat,
                'no_telp'      => '0811' . rand(1000000, 9999999),
            ]);

            $pengurusIds[] = $pengurus->id;
            $idx++;
        }

        // ─── Ketua RT untuk beberapa RT (sample) ─────────────
        $rtSample = Rt::take(8)->get();

        foreach ($rtSample as $rt) {
            if (!isset($pendudukLk[$idx])) break;

            $pengurus = RtRwPengurus::create([
                'kelurahan_id' => $kelurahan->id,
                'penduduk_id'  => $pendudukLk[$idx]->id,
                'jabatan_id'   => $jabatanKetuaRt->id,
                'rw_id'        => $rt->rw_id,
                'rt_id'        => $rt->id,
                'tgl_mulai'    => '2024-01-01',
                'status'       => 'Aktif',
                'alamat'       => $pendudukLk[$idx]->alamat,
                'no_telp'      => '0811' . rand(1000000, 9999999),
            ]);

            $pengurusIds[] = $pengurus->id;
            $idx++;
        }

        // ─── Penilaian Periode ────────────────────────────────
        $periodes = [
            [
                'kelurahan_id' => $kelurahan->id,
                'nama_periode' => 'Semester I - 2025',
                'tgl_mulai'    => '2025-01-01',
                'tgl_selesai'  => '2025-06-30',
            ],
            [
                'kelurahan_id' => $kelurahan->id,
                'nama_periode' => 'Semester II - 2025',
                'tgl_mulai'    => '2025-07-01',
                'tgl_selesai'  => '2025-12-31',
            ],
        ];

        foreach ($periodes as $pData) {
            $periode = PenilaianPeriode::create($pData);

            // Buat penilaian untuk setiap pengurus
            foreach ($pengurusIds as $pgId) {
                PenilaianRtRwDetail::create([
                    'periode_id'              => $periode->id,
                    'pengurus_id'             => $pgId,
                    'nilai'                   => rand(60, 95) + (rand(0, 99) / 100),
                    'standar_nilai'           => 75.00,
                    'usulan_nilai_insentif'   => rand(500, 1500) * 1000,
                    'created_by'              => $adminId,
                ]);
            }
        }
    }
}
