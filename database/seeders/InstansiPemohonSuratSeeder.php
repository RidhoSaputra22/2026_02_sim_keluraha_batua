<?php

namespace Database\Seeders;

use App\Models\Instansi;
use App\Models\Kelurahan;
use App\Models\LayananPublik;
use App\Models\Pemohon;
use App\Models\Penduduk;
use App\Models\Surat;
use App\Models\SuratJenis;
use App\Models\SuratSifat;
use App\Models\User;
use Illuminate\Database\Seeder;

class InstansiPemohonSuratSeeder extends Seeder
{
    public function run(): void
    {
        $petugasId = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first()?->id;
        $kelurahanId = Kelurahan::where('nama', 'Batua')->first()?->id;
        $now = now();

        // ─── Instansi ────────────────────────────────────────
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

        $instansiKelurahan = Instansi::where('nama', 'like', '%Kelurahan Batua%')->first();

        // ─── Pemohon (dari penduduk yang sudah ada) ──────────
        $pendudukSample = Penduduk::take(10)->get();
        $pemohonIds = [];

        foreach ($pendudukSample as $p) {
            $pemohon = Pemohon::create([
                'penduduk_id' => $p->id,
                'nama'        => $p->nama,
                'no_hp_wa'    => '0811' . rand(1000000, 9999999),
                'email'       => strtolower(str_replace([' ', '.', ','], '', $p->nama)) . '@gmail.com',
            ]);
            $pemohonIds[] = $pemohon->id;
        }

        // ─── Surat Masuk & Keluar ────────────────────────────
        $jenisIds     = SuratJenis::pluck('id')->toArray();
        $sifatIds     = SuratSifat::pluck('id')->toArray();
        $instansiIds  = Instansi::pluck('id')->toArray();
        $layananIds   = LayananPublik::pluck('id')->toArray();

        // Surat Keluar (layanan ke warga)
        $suratKeluar = [
            ['nomor' => '474.4/001/BTU/I/2026', 'jenis' => 'Surat Keterangan Tidak Mampu (SKTM)',     'perihal' => 'Keterangan Tidak Mampu a.n. Dg. Ngemba',      'tgl' => '2026-01-05', 'status' => 'signed'],
            ['nomor' => '474.1/002/BTU/I/2026', 'jenis' => 'Surat Keterangan Domisili',               'perihal' => 'Keterangan Domisili a.n. Andi Baso',           'tgl' => '2026-01-08', 'status' => 'signed'],
            ['nomor' => '517/003/BTU/I/2026',   'jenis' => 'Surat Keterangan Usaha',                  'perihal' => 'Keterangan Usaha Warung Makan Dg. Mattola',    'tgl' => '2026-01-10', 'status' => 'signed'],
            ['nomor' => '474.2/004/BTU/I/2026', 'jenis' => 'Surat Pengantar Nikah',                   'perihal' => 'Pengantar Nikah a.n. Akbar Kamaruddin',        'tgl' => '2026-01-12', 'status' => 'signed'],
            ['nomor' => '474.4/005/BTU/I/2026', 'jenis' => 'Surat Keterangan Tidak Mampu (SKTM)',     'perihal' => 'Keterangan Tidak Mampu a.n. Dg. Bella',        'tgl' => '2026-01-15', 'status' => 'signed'],
            ['nomor' => '471/006/BTU/I/2026',   'jenis' => 'Surat Pengantar KTP',                     'perihal' => 'Pengantar pembuatan KTP a.n. Muh. Asrul',      'tgl' => '2026-01-18', 'status' => 'signed'],
            ['nomor' => '474.3/007/BTU/II/2026', 'jenis' => 'Surat Pengantar SKCK',                   'perihal' => 'Pengantar SKCK a.n. Muh. Ikhsan',             'tgl' => '2026-02-01', 'status' => 'signed'],
            ['nomor' => '474.1/008/BTU/II/2026', 'jenis' => 'Surat Keterangan Domisili',              'perihal' => 'Keterangan Domisili a.n. Supriadi',            'tgl' => '2026-02-03', 'status' => 'proses'],
            ['nomor' => '474.4/009/BTU/II/2026', 'jenis' => 'Surat Keterangan Kelahiran',             'perihal' => 'Keterangan Kelahiran anak Rusdi',              'tgl' => '2026-02-05', 'status' => 'proses'],
            ['nomor' => '474.1/010/BTU/II/2026', 'jenis' => 'Surat Keterangan Belum Menikah',         'perihal' => 'Keterangan Belum Menikah a.n. Nurul Hikmah',   'tgl' => '2026-02-08', 'status' => 'draft'],
            ['nomor' => '517/011/BTU/II/2026',   'jenis' => 'Surat Keterangan Usaha',                 'perihal' => 'Keterangan Usaha Bengkel a.n. Hasanuddin',     'tgl' => '2026-02-10', 'status' => 'draft'],
        ];

        foreach ($suratKeluar as $i => $surat) {
            $jenisId = SuratJenis::where('nama', $surat['jenis'])->first()?->id;
            Surat::create([
                'kelurahan_id'    => $kelurahanId,
                'arah'            => 'keluar',
                'nomor_surat'     => $surat['nomor'],
                'tanggal_surat'   => $surat['tgl'],
                'tanggal_diterima' => $surat['tgl'],
                'jenis_id'        => $jenisId,
                'sifat_id'        => $sifatIds[0],  // Biasa
                'instansi_id'     => $instansiKelurahan?->id,
                'layanan_publik_id' => $layananIds[array_rand($layananIds)],
                'tujuan_surat'    => 'Warga Kelurahan Batua',
                'perihal'         => $surat['perihal'],
                'uraian'          => $surat['perihal'],
                'pemohon_id'      => $pemohonIds[$i % count($pemohonIds)] ?? null,
                'status_esign'    => $surat['status'],
                'tgl_input'       => $surat['tgl'],
                'petugas_input_id' => $petugasId,
            ]);
        }

        // Surat Masuk
        $suratMasuk = [
            ['nomor' => '005/100/Mgl/I/2026',   'perihal' => 'Undangan Musrenbang Kecamatan Manggala 2026',       'tgl' => '2026-01-03', 'instansi' => 1],
            ['nomor' => '470/045/Disdukcapil/I/2026', 'perihal' => 'Sosialisasi e-KTP dan KIA',                   'tgl' => '2026-01-07', 'instansi' => 2],
            ['nomor' => '800/012/Polsek/I/2026', 'perihal' => 'Koordinasi Keamanan Lingkungan Bulan Januari',      'tgl' => '2026-01-10', 'instansi' => 5],
            ['nomor' => '005/200/Mgl/I/2026',    'perihal' => 'Undangan Rapat Koordinasi Lurah se-Kec. Manggala',  'tgl' => '2026-01-15', 'instansi' => 1],
            ['nomor' => '400/025/Dinsos/II/2026', 'perihal' => 'Pendataan Penerima Bantuan Sosial 2026',           'tgl' => '2026-02-01', 'instansi' => 7],
            ['nomor' => '900/010/PLN/II/2026',    'perihal' => 'Pemberitahuan Pemadaman Listrik Terencana',        'tgl' => '2026-02-05', 'instansi' => 8],
            ['nomor' => '005/301/Mgl/II/2026',    'perihal' => 'Instruksi Pelaksanaan Kerja Bakti Lingkungan',     'tgl' => '2026-02-08', 'instansi' => 1],
        ];

        foreach ($suratMasuk as $surat) {
            Surat::create([
                'kelurahan_id'     => $kelurahanId,
                'arah'             => 'masuk',
                'nomor_surat'      => $surat['nomor'],
                'tanggal_surat'    => $surat['tgl'],
                'tanggal_diterima' => $surat['tgl'],
                'jenis_id'         => $jenisIds[array_rand($jenisIds)],
                'sifat_id'         => $sifatIds[array_rand($sifatIds)],
                'instansi_id'      => $instansiIds[$surat['instansi']] ?? $instansiIds[0],
                'tujuan_surat'     => 'Lurah Batua',
                'perihal'          => $surat['perihal'],
                'uraian'           => $surat['perihal'],
                'status_esign'     => null,
                'tgl_input'        => $surat['tgl'],
                'petugas_input_id' => $petugasId,
            ]);
        }
    }
}
