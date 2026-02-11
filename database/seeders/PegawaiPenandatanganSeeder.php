<?php

namespace Database\Seeders;

use App\Models\PegawaiStaff;
use App\Models\Penandatanganan;
use App\Models\User;
use Illuminate\Database\Seeder;

class PegawaiPenandatanganSeeder extends Seeder
{
    public function run(): void
    {
        $petugasId = User::whereHas('role', fn ($q) => $q->where('name', 'admin'))->first()?->id;
        $now = now();

        // ─── Pegawai Staff Kelurahan Batua ────────────────────
        $pegawaiData = [
            [
                'nip'            => '197803252005011001',
                'nama'           => 'Drs. H. Muhammad Arif, M.Si',
                'jabatan'        => 'Lurah',
                'gol'            => 'IV/a',
                'pangkat'        => 'Pembina',
                'status_pegawai' => 'PNS',
                'no_urut'        => 1,
            ],
            [
                'nip'            => '198201102008012001',
                'nama'           => 'Hj. Andi Tenri Abeng, S.STP',
                'jabatan'        => 'Sekretaris Lurah',
                'gol'            => 'III/d',
                'pangkat'        => 'Penata Tk. I',
                'status_pegawai' => 'PNS',
                'no_urut'        => 2,
            ],
            [
                'nip'            => '198506102010012003',
                'nama'           => 'Hj. Nurjannah, S.Sos',
                'jabatan'        => 'Kasi Pemerintahan',
                'gol'            => 'III/c',
                'pangkat'        => 'Penata',
                'status_pegawai' => 'PNS',
                'no_urut'        => 3,
            ],
            [
                'nip'            => '198709152012011001',
                'nama'           => 'Irfan Syahputra, S.AP',
                'jabatan'        => 'Kasi Kesejahteraan Sosial',
                'gol'            => 'III/b',
                'pangkat'        => 'Penata Muda Tk. I',
                'status_pegawai' => 'PNS',
                'no_urut'        => 4,
            ],
            [
                'nip'            => '199001012020011001',
                'nama'           => 'Muh. Ilham Akbar, S.Kom',
                'jabatan'        => 'Kasi Pelayanan Umum',
                'gol'            => 'III/a',
                'pangkat'        => 'Penata Muda',
                'status_pegawai' => 'PNS',
                'no_urut'        => 5,
            ],
            [
                'nip'            => '199205152021012001',
                'nama'           => 'Siti Rahmawati, A.Md',
                'jabatan'        => 'Staf Pelayanan',
                'gol'            => 'II/c',
                'pangkat'        => 'Pengatur',
                'status_pegawai' => 'PNS',
                'no_urut'        => 6,
            ],
            [
                'nip'            => '199308202021011002',
                'nama'           => 'Ahmad Fauzi, S.Sos',
                'jabatan'        => 'Staf Kependudukan',
                'gol'            => 'III/a',
                'pangkat'        => 'Penata Muda',
                'status_pegawai' => 'PNS',
                'no_urut'        => 7,
            ],
            [
                'nip'            => 'PTT-2023-001',
                'nama'           => 'Rina Marlina',
                'jabatan'        => 'Staf Administrasi',
                'gol'            => '-',
                'pangkat'        => '-',
                'status_pegawai' => 'PTT',
                'no_urut'        => 8,
            ],
            [
                'nip'            => 'PTT-2023-002',
                'nama'           => 'Muh. Fadli',
                'jabatan'        => 'Staf Kebersihan',
                'gol'            => '-',
                'pangkat'        => '-',
                'status_pegawai' => 'PTT',
                'no_urut'        => 9,
            ],
            [
                'nip'            => 'PTT-2024-001',
                'nama'           => 'Dewi Sartika',
                'jabatan'        => 'Staf Arsip',
                'gol'            => '-',
                'pangkat'        => '-',
                'status_pegawai' => 'PTT',
                'no_urut'        => 10,
            ],
        ];

        $pegawaiMap = [];
        foreach ($pegawaiData as $data) {
            $data['tgl_input'] = $now;
            $data['petugas_input_id'] = $petugasId;
            $pegawai = PegawaiStaff::create($data);
            $pegawaiMap[$data['nip']] = $pegawai->id;
        }

        // ─── Penandatangan (pejabat yang berhak tanda tangan) ─
        $penandatangan = [
            [
                'pegawai_id' => $pegawaiMap['197803252005011001'],  // Lurah
                'status'     => 'Aktif',
                'no_telp'    => '08114321005',
            ],
            [
                'pegawai_id' => $pegawaiMap['198201102008012001'],  // Sekretaris Lurah
                'status'     => 'Aktif',
                'no_telp'    => '08114321010',
            ],
            [
                'pegawai_id' => $pegawaiMap['198506102010012003'],  // Kasi Pemerintahan
                'status'     => 'Aktif',
                'no_telp'    => '08114321004',
            ],
        ];

        foreach ($penandatangan as $data) {
            $data['tgl_input'] = $now;
            $data['petugas_input_id'] = $petugasId;
            Penandatanganan::create($data);
        }
    }
}
