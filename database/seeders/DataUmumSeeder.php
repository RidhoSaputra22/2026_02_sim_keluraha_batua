<?php

namespace Database\Seeders;

use App\Models\DesaWismaPkkEntry;
use App\Models\Ekspedisi;
use App\Models\Faskes;
use App\Models\Imb;
use App\Models\Keluarga;
use App\Models\Kelurahan;
use App\Models\Kendaraan;
use App\Models\Penduduk;
use App\Models\PendudukAsing;
use App\Models\PetugasKebersihan;
use App\Models\Pk5;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\Sekolah;
use App\Models\TempatIbadah;
use App\Models\Umkm;
use App\Models\User;
use App\Models\WargaMiskin;
use Illuminate\Database\Seeder;

class DataUmumSeeder extends Seeder
{
    public function run(): void
    {
        $kelurahan = Kelurahan::where('nama', 'Batua')->first();
        $petugasId = User::whereHas('role', fn ($q) => $q->where('name', 'operator'))->first()?->id;
        $now = now();

        $rwList = Rw::where('kelurahan_id', $kelurahan->id)->orderBy('nomor')->get();
        $rtList = Rt::whereIn('rw_id', $rwList->pluck('id'))->get();
        $pendudukAll = Penduduk::all();

        // ═══════════════════════════════════════════════════════
        // UMKM / Usaha
        // ═══════════════════════════════════════════════════════
        $umkmData = [
            ['nama_ukm' => 'Warung Makan Dg. Mattola',       'sektor' => 'Kuliner',         'idx' => 0],
            ['nama_ukm' => 'Bengkel Las Hasanuddin',          'sektor' => 'Jasa',            'idx' => 11],
            ['nama_ukm' => 'Toko Kelontong Aminah',          'sektor' => 'Perdagangan',     'idx' => 1],
            ['nama_ukm' => 'Konveksi Batua Jaya',            'sektor' => 'Produksi',        'idx' => 4],
            ['nama_ukm' => 'Salon Kecantikan Wahyuni',       'sektor' => 'Jasa',            'idx' => 13],
            ['nama_ukm' => 'Warung Kopi Dg. Nai',            'sektor' => 'Kuliner',         'idx' => 9],
            ['nama_ukm' => 'Fotocopy & ATK Ridwan',          'sektor' => 'Jasa',            'idx' => 2],
            ['nama_ukm' => 'Laundry Bersih Jaya',            'sektor' => 'Jasa',            'idx' => 7],
            ['nama_ukm' => 'Toko Bangunan H. Said',          'sektor' => 'Perdagangan',     'idx' => 14],
            ['nama_ukm' => 'Catering Bu Ramlah',             'sektor' => 'Kuliner',         'idx' => 5],
        ];

        foreach ($umkmData as $u) {
            $p = $pendudukAll[$u['idx']] ?? $pendudukAll->first();
            Umkm::create([
                'kelurahan_id'  => $kelurahan->id,
                'rt_id'         => $p->rt_id,
                'penduduk_id'   => $p->id,
                'nama_pemilik'  => $p->nama,
                'nik_pemilik'   => $p->nik,
                'no_hp'         => '0811' . rand(1000000, 9999999),
                'nama_ukm'      => $u['nama_ukm'],
                'alamat'        => 'Kel. Batua, Kec. Manggala',
                'sektor_umkm'   => $u['sektor'],
            ]);
        }

        // ═══════════════════════════════════════════════════════
        // Sekolah
        // ═══════════════════════════════════════════════════════
        $sekolahData = [
            ['npsn' => '40312001', 'nama' => 'SD Inpres Batua',              'jenjang' => 'SD',  'status' => 'Negeri', 'siswa' => 285, 'guru' => 18, 'pegawai' => 5, 'rombel' => 12, 'kelas' => 12],
            ['npsn' => '40312002', 'nama' => 'SD Negeri Batua II',           'jenjang' => 'SD',  'status' => 'Negeri', 'siswa' => 320, 'guru' => 20, 'pegawai' => 6, 'rombel' => 12, 'kelas' => 12],
            ['npsn' => '40312003', 'nama' => 'SMP Negeri 33 Makassar',       'jenjang' => 'SMP', 'status' => 'Negeri', 'siswa' => 480, 'guru' => 32, 'pegawai' => 10, 'rombel' => 18, 'kelas' => 18],
            ['npsn' => '40312004', 'nama' => 'SMA Islam Batua',              'jenjang' => 'SMA', 'status' => 'Swasta', 'siswa' => 210, 'guru' => 22, 'pegawai' => 7, 'rombel' => 9, 'kelas' => 9],
            ['npsn' => '40312005', 'nama' => 'TK Aisyiyah Batua',            'jenjang' => 'TK',  'status' => 'Swasta', 'siswa' => 60,  'guru' => 6,  'pegawai' => 2, 'rombel' => 3, 'kelas' => 3],
            ['npsn' => '40312006', 'nama' => 'MTs Muhammadiyah Batua',       'jenjang' => 'MTs', 'status' => 'Swasta', 'siswa' => 180, 'guru' => 15, 'pegawai' => 4, 'rombel' => 6, 'kelas' => 6],
        ];

        foreach ($sekolahData as $s) {
            Sekolah::create([
                'kelurahan_id'   => $kelurahan->id,
                'npsn'           => $s['npsn'],
                'nama_sekolah'   => $s['nama'],
                'jenjang'        => $s['jenjang'],
                'status'         => $s['status'],
                'alamat'         => 'Kel. Batua, Kec. Manggala, Kota Makassar',
                'latitude'       => -5.1476 + (rand(-100, 100) / 10000),
                'longitude'      => 119.4934 + (rand(-100, 100) / 10000),
                'tahun_ajar'     => '2025/2026',
                'jumlah_siswa'   => $s['siswa'],
                'rombel'         => $s['rombel'],
                'jumlah_guru'    => $s['guru'],
                'jumlah_pegawai' => $s['pegawai'],
                'ruang_kelas'    => $s['kelas'],
                'jumlah_r_lab'   => rand(1, 3),
                'jumlah_r_perpus' => 1,
            ]);
        }

        // ═══════════════════════════════════════════════════════
        // Fasilitas Kesehatan
        // ═══════════════════════════════════════════════════════
        $faskesData = [
            ['nama' => 'Puskesmas Batua',          'jenis' => 'Puskesmas',     'kelas' => '-',     'pelayanan' => 'Rawat Jalan', 'akreditasi' => 'Madya',   'telp' => '0411-445678'],
            ['nama' => 'Pustu Batua Timur',         'jenis' => 'Pustu',        'kelas' => '-',     'pelayanan' => 'Rawat Jalan', 'akreditasi' => '-',       'telp' => '0411-445679'],
            ['nama' => 'Klinik Pratama Sehat Batua', 'jenis' => 'Klinik',      'kelas' => 'D',     'pelayanan' => 'Rawat Jalan', 'akreditasi' => 'Dasar',   'telp' => '0411-445680'],
            ['nama' => 'Posyandu Melati RW 001',    'jenis' => 'Posyandu',     'kelas' => '-',     'pelayanan' => 'Imunisasi & Gizi', 'akreditasi' => '-',  'telp' => '-'],
            ['nama' => 'Posyandu Mawar RW 003',     'jenis' => 'Posyandu',     'kelas' => '-',     'pelayanan' => 'Imunisasi & Gizi', 'akreditasi' => '-',  'telp' => '-'],
        ];

        foreach ($faskesData as $i => $f) {
            Faskes::create([
                'kelurahan_id'     => $kelurahan->id,
                'nama_rs'          => $f['nama'],
                'alamat'           => 'Kel. Batua, Kec. Manggala',
                'rw_id'            => $rwList[$i % $rwList->count()]->id ?? null,
                'jenis'            => $f['jenis'],
                'kelas'            => $f['kelas'],
                'jenis_pelayanan'  => $f['pelayanan'],
                'akreditasi'       => $f['akreditasi'],
                'telp'             => $f['telp'],
            ]);
        }

        // ═══════════════════════════════════════════════════════
        // Tempat Ibadah
        // ═══════════════════════════════════════════════════════
        $ibadahData = [
            ['jenis' => 'Masjid',    'nama' => 'Masjid Al-Ikhlas',                 'rw' => 0, 'rt' => 0],
            ['jenis' => 'Masjid',    'nama' => 'Masjid Nurul Iman',                'rw' => 1, 'rt' => 5],
            ['jenis' => 'Masjid',    'nama' => 'Masjid Al-Muhajirin',              'rw' => 2, 'rt' => 9],
            ['jenis' => 'Masjid',    'nama' => 'Masjid Babul Jannah',              'rw' => 3, 'rt' => 14],
            ['jenis' => 'Masjid',    'nama' => 'Masjid Raodhatul Jannah',          'rw' => 5, 'rt' => 21],
            ['jenis' => 'Musholla',  'nama' => 'Musholla At-Taqwa',                'rw' => 4, 'rt' => 18],
            ['jenis' => 'Musholla',  'nama' => 'Musholla Al-Falah',                'rw' => 6, 'rt' => 25],
            ['jenis' => 'Gereja',    'nama' => 'Gereja GPIB Batua',                'rw' => 7, 'rt' => 29],
        ];

        foreach ($ibadahData as $ib) {
            TempatIbadah::create([
                'kelurahan_id'  => $kelurahan->id,
                'tempat_ibadah' => $ib['jenis'],
                'nama'          => $ib['nama'],
                'alamat'        => 'Kel. Batua, Kec. Manggala',
                'rt_id'         => $rtList[$ib['rt']]->id ?? null,
                'rw_id'         => $rwList[$ib['rw']]->id ?? null,
                'pengurus'      => 'Pengurus ' . $ib['nama'],
            ]);
        }

        // ═══════════════════════════════════════════════════════
        // Ekspedisi
        // ═══════════════════════════════════════════════════════
        $ekspedisiData = [
            ['ekspedisi' => 'JNE Batua',       'pemilik' => 'Agus Salim',     'pj' => 'Agus Salim',     'kegiatan' => 'Jasa pengiriman barang'],
            ['ekspedisi' => 'J&T Express Batua', 'pemilik' => 'Supriadi',     'pj' => 'Supriadi',       'kegiatan' => 'Jasa pengiriman barang'],
            ['ekspedisi' => 'SiCepat Batua',   'pemilik' => 'Saiful Said',    'pj' => 'Saiful Said',    'kegiatan' => 'Jasa pengiriman barang & logistik'],
        ];

        foreach ($ekspedisiData as $e) {
            Ekspedisi::create([
                'kelurahan_id'       => $kelurahan->id,
                'pemilik_usaha'      => $e['pemilik'],
                'ekspedisi'          => $e['ekspedisi'],
                'alamat'             => 'Kel. Batua, Kec. Manggala',
                'penanggung_jawab'   => $e['pj'],
                'telp_hp'            => '0811' . rand(1000000, 9999999),
                'kegiatan_ekspedisi' => $e['kegiatan'],
            ]);
        }

        // ═══════════════════════════════════════════════════════
        // IMB
        // ═══════════════════════════════════════════════════════
        $imbData = [
            ['pemohon' => 'Andi Mappangara', 'alamat_b' => 'Jl. Batua Raya No. 45',  'fungsi' => 'Rumah Tinggal',   'luas' => '120 m²'],
            ['pemohon' => 'Kamaruddin',      'alamat_b' => 'Jl. Batua Dalam No. 12',  'fungsi' => 'Rumah Tinggal',   'luas' => '150 m²'],
            ['pemohon' => 'Agus Salim',      'alamat_b' => 'Jl. Batua Raya No. 78',   'fungsi' => 'Ruko',            'luas' => '200 m²'],
            ['pemohon' => 'H. Muh. Said',    'alamat_b' => 'Jl. Antang Raya No. 33',  'fungsi' => 'Rumah Tinggal',   'luas' => '250 m²'],
            ['pemohon' => 'Supriadi',        'alamat_b' => 'Jl. Batua Raya No. 99',   'fungsi' => 'Gedung Usaha',    'luas' => '180 m²'],
        ];

        foreach ($imbData as $imb) {
            Imb::create([
                'kelurahan_id'              => $kelurahan->id,
                'nama_pemohon'              => $imb['pemohon'],
                'alamat_pemohon'            => 'Kel. Batua, Kec. Manggala',
                'alamat_bangunan'           => $imb['alamat_b'],
                'status_luas_tanah'         => 'SHM - ' . $imb['luas'],
                'nama_pada_surat'           => $imb['pemohon'],
                'penggunaan_fungsi_gedung'  => $imb['fungsi'],
            ]);
        }

        // ═══════════════════════════════════════════════════════
        // Kendaraan Dinas
        // ═══════════════════════════════════════════════════════
        $kendaraanData = [
            ['jenis' => 'Sepeda Motor',   'pengemudi' => 'Muh. Fadli',      'nopol' => 'DD 1234 AB', 'merek' => 'Honda Beat 2022',      'tahun' => '2022'],
            ['jenis' => 'Sepeda Motor',   'pengemudi' => 'Ahmad Fauzi',     'nopol' => 'DD 5678 CD', 'merek' => 'Yamaha NMAX 2023',     'tahun' => '2023'],
            ['jenis' => 'Mobil',          'pengemudi' => 'Muh. Fadli',      'nopol' => 'DD 9012 EF', 'merek' => 'Toyota Avanza 2021',   'tahun' => '2021'],
            ['jenis' => 'Mobil Pick Up',  'pengemudi' => 'Muh. Fadli',      'nopol' => 'DD 3456 GH', 'merek' => 'Mitsubishi L300 2020', 'tahun' => '2020'],
        ];

        foreach ($kendaraanData as $k) {
            Kendaraan::create([
                'kelurahan_id'    => $kelurahan->id,
                'jenis_barang'    => $k['jenis'],
                'nama_pengemudi'  => $k['pengemudi'],
                'no_polisi'       => $k['nopol'],
                'no_rangka'       => strtoupper(substr(md5(rand()), 0, 17)),
                'no_mesin'        => strtoupper(substr(md5(rand()), 0, 12)),
                'tahun_perolehan' => $k['tahun'],
                'merek_type'      => $k['merek'],
            ]);
        }

        // ═══════════════════════════════════════════════════════
        // Petugas Kebersihan
        // ═══════════════════════════════════════════════════════
        $petugasData = [
            ['nama' => 'Muh. Fadli',    'nik' => 'PTT-KB-001', 'unit' => 'Kelurahan Batua', 'lokasi' => 'RW 001 - RW 004', 'jk' => 'L'],
            ['nama' => 'Dg. Rani',      'nik' => 'PTT-KB-002', 'unit' => 'Kelurahan Batua', 'lokasi' => 'RW 005 - RW 008', 'jk' => 'L'],
            ['nama' => 'Baharuddin',    'nik' => 'PTT-KB-003', 'unit' => 'Kelurahan Batua', 'lokasi' => 'Jl. Batua Raya',  'jk' => 'L'],
            ['nama' => 'Sumarni',       'nik' => 'PTT-KB-004', 'unit' => 'Kantor Kelurahan', 'lokasi' => 'Kantor Lurah',   'jk' => 'P'],
        ];

        foreach ($petugasData as $pt) {
            PetugasKebersihan::create([
                'kelurahan_id' => $kelurahan->id,
                'nama'         => $pt['nama'],
                'nik'          => $pt['nik'],
                'unit_kerja'   => $pt['unit'],
                'jenis_kelamin' => $pt['jk'],
                'pekerjaan'    => 'Petugas Kebersihan',
                'lokasi'       => $pt['lokasi'],
                'status'       => 'Aktif',
            ]);
        }

        // ═══════════════════════════════════════════════════════
        // PK5 (Pedagang Kaki Lima)
        // ═══════════════════════════════════════════════════════
        $pk5Data = [
            ['nama' => 'Dg. Tata',     'jabatan' => 'Koordinator PKL Batua',  'status' => 'Aktif'],
            ['nama' => 'Sahrul',        'jabatan' => 'Anggota PKL',            'status' => 'Aktif'],
            ['nama' => 'Muh. Yusuf',   'jabatan' => 'Anggota PKL',            'status' => 'Aktif'],
            ['nama' => 'Rosmini',       'jabatan' => 'Anggota PKL',            'status' => 'Aktif'],
            ['nama' => 'Dg. Sikki',    'jabatan' => 'Anggota PKL',            'status' => 'Tidak Aktif'],
        ];

        foreach ($pk5Data as $pk) {
            Pk5::create([
                'nama'             => $pk['nama'],
                'jabatan'          => $pk['jabatan'],
                'status'           => $pk['status'],
                'no_telp'          => '0811' . rand(1000000, 9999999),
                'tgl_input'        => $now,
                'petugas_input_id' => $petugasId,
            ]);
        }

        // ═══════════════════════════════════════════════════════
        // Warga Miskin
        // ═══════════════════════════════════════════════════════
        $wargaMiskinPenduduk = Penduduk::whereIn('pendidikan', ['SD/Sederajat', 'Tidak Sekolah', 'SMP/Sederajat'])
            ->where('status_kawin', 'Kawin')
            ->take(8)
            ->get();

        $noPeserta = 1;
        foreach ($wargaMiskinPenduduk as $wm) {
            WargaMiskin::create([
                'kelurahan_id' => $kelurahan->id,
                'penduduk_id'  => $wm->id,
                'rt_id'        => $wm->rt_id,
                'rw_id'        => $wm->rt?->rw_id,
                'no_peserta'   => 'DTKS-BTU-2026-' . str_pad($noPeserta++, 4, '0', STR_PAD_LEFT),
                'keterangan'   => 'Penerima bantuan sosial',
            ]);
        }

        // ═══════════════════════════════════════════════════════
        // Desa Wisma PKK Entry
        // ═══════════════════════════════════════════════════════
        $pendudukPerempuan = Penduduk::where('jenis_kelamin', 'P')
            ->where('status_kawin', 'Kawin')
            ->take(10)
            ->get();

        foreach ($pendudukPerempuan as $pp) {
            DesaWismaPkkEntry::create([
                'penduduk_id'  => $pp->id,
                'keluarga_id'  => $pp->keluarga_id,
                'rt_id'        => $pp->rt_id,
                'kelurahan_id' => $kelurahan->id,
                'keterangan'   => 'Anggota PKK aktif',
            ]);
        }

        // ═══════════════════════════════════════════════════════
        // Penduduk Asing
        // ═══════════════════════════════════════════════════════
        $pendudukAsingData = [
            ['paspor' => 'MY-A1234567', 'nama' => 'Ahmad bin Abdullah',    'jk' => 'L', 'alamat' => 'Jl. Batua Raya No. 50'],
            ['paspor' => 'CN-B8901234', 'nama' => 'Li Wei Chen',           'jk' => 'L', 'alamat' => 'Jl. Batua Raya No. 88'],
            ['paspor' => 'JP-C5678901', 'nama' => 'Tanaka Yuki',           'jk' => 'P', 'alamat' => 'Jl. Antang Raya No. 15'],
        ];

        foreach ($pendudukAsingData as $pa) {
            PendudukAsing::create([
                'no_paspor'        => $pa['paspor'],
                'nama'             => $pa['nama'],
                'alamat'           => $pa['alamat'],
                'rt_id'            => $rtList->random()->id,
                'jenis_kelamin'    => $pa['jk'],
                'tgl_input'        => $now,
                'petugas_input_id' => $petugasId,
            ]);
        }
    }
}
