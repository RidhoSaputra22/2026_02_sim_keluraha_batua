<?php

namespace Database\Seeders;

use App\Models\Keluarga;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\User;
use Illuminate\Database\Seeder;

class PendudukKeluargaSeeder extends Seeder
{
    public function run(): void
    {
        $petugasId = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first()?->id;
        $now = now();

        // Ambil semua RT yang ada (Kel. Batua)
        $allRts = Rt::all();

        // ─── Data Keluarga & Penduduk ─────────────────────────
        // Format: [no_kk, rt_index, kepala_keluarga, anggota[]]
        // rt_index mengacu pada urutan RT dari database
        $keluargaData = [
            // ── Keluarga 1 (RW 001 / RT 001) ──
            [
                'no_kk' => '7371112301080001',
                'rt_index' => 0,
                'anggota' => [
                    ['nik' => '7371111505700001', 'nama' => 'H. Daeng Mattola',     'jk' => 'L', 'agama' => 'Islam', 'status_kawin' => 'Kawin', 'pendidikan' => 'SMA/Sederajat', 'gol_darah' => 'O', 'kepala' => true],
                    ['nik' => '7371114708720002', 'nama' => 'Hj. Sitti Aminah',     'jk' => 'P', 'agama' => 'Islam', 'status_kawin' => 'Kawin', 'pendidikan' => 'SMA/Sederajat', 'gol_darah' => 'A'],
                    ['nik' => '7371111208950003', 'nama' => 'Muh. Ridwan',          'jk' => 'L', 'agama' => 'Islam', 'status_kawin' => 'Belum Kawin', 'pendidikan' => 'S1', 'gol_darah' => 'O'],
                    ['nik' => '7371115503000004', 'nama' => 'Nurul Hikmah',         'jk' => 'P', 'agama' => 'Islam', 'status_kawin' => 'Belum Kawin', 'pendidikan' => 'SMA/Sederajat', 'gol_darah' => 'A'],
                ],
            ],
            // ── Keluarga 2 (RW 001 / RT 001) ──
            [
                'no_kk' => '7371112301080002',
                'rt_index' => 0,
                'anggota' => [
                    ['nik' => '7371112305800005', 'nama' => 'Andi Mappangara',      'jk' => 'L', 'agama' => 'Islam', 'status_kawin' => 'Kawin', 'pendidikan' => 'S1', 'gol_darah' => 'B', 'kepala' => true],
                    ['nik' => '7371116707820006', 'nama' => 'Andi Tenri Olle',      'jk' => 'P', 'agama' => 'Islam', 'status_kawin' => 'Kawin', 'pendidikan' => 'S1', 'gol_darah' => 'AB'],
                    ['nik' => '7371111504050007', 'nama' => 'Andi Tenri Ajeng',     'jk' => 'P', 'agama' => 'Islam', 'status_kawin' => 'Belum Kawin', 'pendidikan' => 'SMP/Sederajat', 'gol_darah' => 'B'],
                ],
            ],
            // ── Keluarga 3 (RW 001 / RT 002) ──
            [
                'no_kk' => '7371112301080003',
                'rt_index' => 1,
                'anggota' => [
                    ['nik' => '7371111010750008', 'nama' => 'Dg. Ngemba',           'jk' => 'L', 'agama' => 'Islam', 'status_kawin' => 'Kawin', 'pendidikan' => 'SD/Sederajat', 'gol_darah' => 'O', 'kepala' => true],
                    ['nik' => '7371114502780009', 'nama' => 'Dg. Kanang',           'jk' => 'P', 'agama' => 'Islam', 'status_kawin' => 'Kawin', 'pendidikan' => 'SD/Sederajat', 'gol_darah' => 'O'],
                    ['nik' => '7371112003000010', 'nama' => 'Muh. Asrul',           'jk' => 'L', 'agama' => 'Islam', 'status_kawin' => 'Belum Kawin', 'pendidikan' => 'SMA/Sederajat', 'gol_darah' => 'O'],
                ],
            ],
            // ── Keluarga 4 (RW 001 / RT 003) ──
            [
                'no_kk' => '7371112301080004',
                'rt_index' => 2,
                'anggota' => [
                    ['nik' => '7371112305850011', 'nama' => 'Syamsuddin',           'jk' => 'L', 'agama' => 'Islam', 'status_kawin' => 'Kawin', 'pendidikan' => 'D3', 'gol_darah' => 'A', 'kepala' => true],
                    ['nik' => '7371115607870012', 'nama' => 'Hasna',                'jk' => 'P', 'agama' => 'Islam', 'status_kawin' => 'Kawin', 'pendidikan' => 'SMA/Sederajat', 'gol_darah' => 'B'],
                    ['nik' => '7371111201100013', 'nama' => 'Muh. Fachri',          'jk' => 'L', 'agama' => 'Islam', 'status_kawin' => 'Belum Kawin', 'pendidikan' => 'SD/Sederajat', 'gol_darah' => 'A'],
                    ['nik' => '7371115505120014', 'nama' => 'Aisyah Putri',         'jk' => 'P', 'agama' => 'Islam', 'status_kawin' => 'Belum Kawin', 'pendidikan' => 'SD/Sederajat', 'gol_darah' => 'B'],
                ],
            ],
            // ── Keluarga 5 (RW 002 / RT 001) ──
            [
                'no_kk' => '7371112301080005',
                'rt_index' => 5,
                'anggota' => [
                    ['nik' => '7371112305950015', 'nama' => 'Andi Baso',            'jk' => 'L', 'agama' => 'Islam', 'status_kawin' => 'Kawin', 'pendidikan' => 'S1', 'gol_darah' => 'AB', 'kepala' => true],
                    ['nik' => '7371115607980016', 'nama' => 'Fatimah Azzahra',      'jk' => 'P', 'agama' => 'Islam', 'status_kawin' => 'Kawin', 'pendidikan' => 'S1', 'gol_darah' => 'A'],
                    ['nik' => '7371112010200017', 'nama' => 'Muh. Aqil Baso',       'jk' => 'L', 'agama' => 'Islam', 'status_kawin' => 'Belum Kawin', 'pendidikan' => 'Belum Sekolah', 'gol_darah' => 'AB'],
                ],
            ],
            // ── Keluarga 6 (RW 002 / RT 002) ──
            [
                'no_kk' => '7371112301080006',
                'rt_index' => 6,
                'anggota' => [
                    ['nik' => '7371110101780018', 'nama' => 'Abdul Rahman',         'jk' => 'L', 'agama' => 'Islam', 'status_kawin' => 'Kawin', 'pendidikan' => 'SMA/Sederajat', 'gol_darah' => 'O', 'kepala' => true],
                    ['nik' => '7371114503800019', 'nama' => 'Ramlah',               'jk' => 'P', 'agama' => 'Islam', 'status_kawin' => 'Kawin', 'pendidikan' => 'SMP/Sederajat', 'gol_darah' => 'A'],
                    ['nik' => '7371111505030020', 'nama' => 'Muh. Alif',            'jk' => 'L', 'agama' => 'Islam', 'status_kawin' => 'Belum Kawin', 'pendidikan' => 'SMA/Sederajat', 'gol_darah' => 'O'],
                    ['nik' => '7371117012060021', 'nama' => 'Nurul Auliya',         'jk' => 'P', 'agama' => 'Islam', 'status_kawin' => 'Belum Kawin', 'pendidikan' => 'SMP/Sederajat', 'gol_darah' => 'A'],
                    ['nik' => '7371112505100022', 'nama' => 'Muh. Farel',           'jk' => 'L', 'agama' => 'Islam', 'status_kawin' => 'Belum Kawin', 'pendidikan' => 'SD/Sederajat', 'gol_darah' => 'O'],
                ],
            ],
            // ── Keluarga 7 (RW 003 / RT 001) ──
            [
                'no_kk' => '7371112301080007',
                'rt_index' => 9,
                'anggota' => [
                    ['nik' => '7371112008680023', 'nama' => 'Kamaruddin',           'jk' => 'L', 'agama' => 'Islam', 'status_kawin' => 'Kawin', 'pendidikan' => 'S2', 'gol_darah' => 'B', 'kepala' => true],
                    ['nik' => '7371115102700024', 'nama' => 'Hj. Suriani, S.Pd',    'jk' => 'P', 'agama' => 'Islam', 'status_kawin' => 'Kawin', 'pendidikan' => 'S1', 'gol_darah' => 'A'],
                    ['nik' => '7371111706950025', 'nama' => 'Akbar Kamaruddin',     'jk' => 'L', 'agama' => 'Islam', 'status_kawin' => 'Belum Kawin', 'pendidikan' => 'S1', 'gol_darah' => 'B'],
                ],
            ],
            // ── Keluarga 8 (RW 003 / RT 002) ──
            [
                'no_kk' => '7371112301080008',
                'rt_index' => 10,
                'anggota' => [
                    ['nik' => '7371110506650026', 'nama' => 'Basri Dg. Situju',     'jk' => 'L', 'agama' => 'Islam', 'status_kawin' => 'Cerai Mati', 'pendidikan' => 'SMP/Sederajat', 'gol_darah' => 'O', 'kepala' => true],
                    ['nik' => '7371113003900027', 'nama' => 'Suardi',               'jk' => 'L', 'agama' => 'Islam', 'status_kawin' => 'Kawin', 'pendidikan' => 'SMA/Sederajat', 'gol_darah' => 'O'],
                ],
            ],
            // ── Keluarga 9 (RW 004 / RT 001) ──
            [
                'no_kk' => '7371112301080009',
                'rt_index' => 14,
                'anggota' => [
                    ['nik' => '7371111212850028', 'nama' => 'Rusdi',                'jk' => 'L', 'agama' => 'Islam', 'status_kawin' => 'Kawin', 'pendidikan' => 'D3', 'gol_darah' => 'A', 'kepala' => true],
                    ['nik' => '7371115606880029', 'nama' => 'Marlina',              'jk' => 'P', 'agama' => 'Islam', 'status_kawin' => 'Kawin', 'pendidikan' => 'SMA/Sederajat', 'gol_darah' => 'B'],
                    ['nik' => '7371112803130030', 'nama' => 'Muh. Zaki',            'jk' => 'L', 'agama' => 'Islam', 'status_kawin' => 'Belum Kawin', 'pendidikan' => 'SMP/Sederajat', 'gol_darah' => 'A'],
                ],
            ],
            // ── Keluarga 10 (RW 004 / RT 002) ──
            [
                'no_kk' => '7371112301080010',
                'rt_index' => 15,
                'anggota' => [
                    ['nik' => '7371110808700031', 'nama' => 'Mustafa Dg. Nai',      'jk' => 'L', 'agama' => 'Islam', 'status_kawin' => 'Kawin', 'pendidikan' => 'SMA/Sederajat', 'gol_darah' => 'AB', 'kepala' => true],
                    ['nik' => '7371114404730032', 'nama' => 'Jumriati',             'jk' => 'P', 'agama' => 'Islam', 'status_kawin' => 'Kawin', 'pendidikan' => 'SMA/Sederajat', 'gol_darah' => 'O'],
                    ['nik' => '7371111507970033', 'nama' => 'Muh. Ikhsan',          'jk' => 'L', 'agama' => 'Islam', 'status_kawin' => 'Belum Kawin', 'pendidikan' => 'S1', 'gol_darah' => 'AB'],
                    ['nik' => '7371117001010034', 'nama' => 'Nadia Mustafa',        'jk' => 'P', 'agama' => 'Islam', 'status_kawin' => 'Belum Kawin', 'pendidikan' => 'SMA/Sederajat', 'gol_darah' => 'O'],
                ],
            ],
            // ── Keluarga 11 (RW 005 / RT 001) ──
            [
                'no_kk' => '7371112301080011',
                'rt_index' => 18,
                'anggota' => [
                    ['nik' => '7371112509750035', 'nama' => 'Jamaluddin',           'jk' => 'L', 'agama' => 'Islam', 'status_kawin' => 'Kawin', 'pendidikan' => 'SMA/Sederajat', 'gol_darah' => 'A', 'kepala' => true],
                    ['nik' => '7371116003780036', 'nama' => 'Hasnawati',            'jk' => 'P', 'agama' => 'Islam', 'status_kawin' => 'Kawin', 'pendidikan' => 'SMA/Sederajat', 'gol_darah' => 'B'],
                ],
            ],
            // ── Keluarga 12 (RW 006 / RT 001) ──
            [
                'no_kk' => '7371112301080012',
                'rt_index' => 21,
                'anggota' => [
                    ['nik' => '7371110307600037', 'nama' => 'Dg. Bella',            'jk' => 'L', 'agama' => 'Islam', 'status_kawin' => 'Kawin', 'pendidikan' => 'Tidak Sekolah', 'gol_darah' => 'O', 'kepala' => true],
                    ['nik' => '7371114210630038', 'nama' => 'Dg. Baji',             'jk' => 'P', 'agama' => 'Islam', 'status_kawin' => 'Kawin', 'pendidikan' => 'Tidak Sekolah', 'gol_darah' => 'O'],
                    ['nik' => '7371111105850039', 'nama' => 'Hasanuddin',           'jk' => 'L', 'agama' => 'Islam', 'status_kawin' => 'Kawin', 'pendidikan' => 'SMP/Sederajat', 'gol_darah' => 'A'],
                ],
            ],
            // ── Keluarga 13 (RW 007 / RT 001) ──
            [
                'no_kk' => '7371112301080013',
                'rt_index' => 25,
                'anggota' => [
                    ['nik' => '7371112807830040', 'nama' => 'Agus Salim',           'jk' => 'L', 'agama' => 'Islam', 'status_kawin' => 'Kawin', 'pendidikan' => 'S1', 'gol_darah' => 'B', 'kepala' => true],
                    ['nik' => '7371115109860041', 'nama' => 'Wahyuni',              'jk' => 'P', 'agama' => 'Islam', 'status_kawin' => 'Kawin', 'pendidikan' => 'D3', 'gol_darah' => 'A'],
                    ['nik' => '7371111002130042', 'nama' => 'Muh. Farhan',          'jk' => 'L', 'agama' => 'Islam', 'status_kawin' => 'Belum Kawin', 'pendidikan' => 'SMP/Sederajat', 'gol_darah' => 'B'],
                ],
            ],
            // ── Keluarga 14 (RW 008 / RT 001) ──
            [
                'no_kk' => '7371112301080014',
                'rt_index' => 28,
                'anggota' => [
                    ['nik' => '7371110303550043', 'nama' => 'H. Muh. Said',         'jk' => 'L', 'agama' => 'Islam', 'status_kawin' => 'Kawin', 'pendidikan' => 'S1', 'gol_darah' => 'O', 'kepala' => true],
                    ['nik' => '7371114506580044', 'nama' => 'Hj. Nursia',           'jk' => 'P', 'agama' => 'Islam', 'status_kawin' => 'Kawin', 'pendidikan' => 'SMA/Sederajat', 'gol_darah' => 'A'],
                    ['nik' => '7371111207800045', 'nama' => 'Saiful Said',          'jk' => 'L', 'agama' => 'Islam', 'status_kawin' => 'Kawin', 'pendidikan' => 'S1', 'gol_darah' => 'O'],
                    ['nik' => '7371115503830046', 'nama' => 'Fitriani',             'jk' => 'P', 'agama' => 'Islam', 'status_kawin' => 'Kawin', 'pendidikan' => 'D3', 'gol_darah' => 'B'],
                    ['nik' => '7371112201100047', 'nama' => 'Muh. Aufar Said',      'jk' => 'L', 'agama' => 'Islam', 'status_kawin' => 'Belum Kawin', 'pendidikan' => 'SD/Sederajat', 'gol_darah' => 'O'],
                ],
            ],
            // ── Keluarga 15 (RW 008 / RT 002) ──
            [
                'no_kk' => '7371112301080015',
                'rt_index' => 29,
                'anggota' => [
                    ['nik' => '7371112507900048', 'nama' => 'Supriadi',             'jk' => 'L', 'agama' => 'Kristen', 'status_kawin' => 'Kawin', 'pendidikan' => 'S1', 'gol_darah' => 'A', 'kepala' => true],
                    ['nik' => '7371115201920049', 'nama' => 'Maria Goretti',        'jk' => 'P', 'agama' => 'Kristen', 'status_kawin' => 'Kawin', 'pendidikan' => 'S1', 'gol_darah' => 'B'],
                    ['nik' => '7371111510180050', 'nama' => 'Daniel Supriadi',      'jk' => 'L', 'agama' => 'Kristen', 'status_kawin' => 'Belum Kawin', 'pendidikan' => 'SD/Sederajat', 'gol_darah' => 'A'],
                ],
            ],
        ];

        // ─── Insert Keluarga & Penduduk ───────────────────────
        foreach ($keluargaData as $kData) {
            $rtId = $allRts[$kData['rt_index']]->id ?? null;

            // Buat keluarga dulu (tanpa kepala)
            $keluarga = Keluarga::create([
                'no_kk'                  => $kData['no_kk'],
                'kepala_keluarga_id'     => null,
                'jumlah_anggota_keluarga' => count($kData['anggota']),
                'rt_id'                  => $rtId,
                'tgl_input'              => $now,
                'petugas_input_id'       => $petugasId,
            ]);

            $kepalaId = null;

            foreach ($kData['anggota'] as $anggota) {
                $penduduk = Penduduk::create([
                    'nik'              => $anggota['nik'],
                    'nama'             => $anggota['nama'],
                    'alamat'           => 'Kel. Batua, Kec. Manggala, Kota Makassar',
                    'keluarga_id'      => $keluarga->id,
                    'rt_id'            => $rtId,
                    'jenis_kelamin'    => $anggota['jk'],
                    'gol_darah'        => $anggota['gol_darah'],
                    'agama'            => $anggota['agama'],
                    'status_kawin'     => $anggota['status_kawin'],
                    'pendidikan'       => $anggota['pendidikan'],
                    'status_data'      => 'Aktif',
                    'tgl_input'        => $now,
                    'petugas_input_id' => $petugasId,
                ]);

                if (!empty($anggota['kepala'])) {
                    $kepalaId = $penduduk->id;
                }
            }

            // Update kepala keluarga
            if ($kepalaId) {
                $keluarga->update(['kepala_keluarga_id' => $kepalaId]);
            }
        }
    }
}
