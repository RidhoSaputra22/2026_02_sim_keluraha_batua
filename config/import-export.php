<?php

/**
 * Konfigurasi modul Import & Export
 *
 * Setiap modul mendefinisikan:
 * - title          : Judul halaman
 * - model          : Fully-qualified model class
 * - date_column    : Kolom tanggal untuk filter rentang (null = created_at)
 * - back_route     : Route name untuk tombol kembali
 * - with           : Eager-load relasi saat export
 * - columns        : Kolom DB yang di-export / di-import
 * - headers        : Label header untuk CSV
 * - required       : Kolom wajib saat import
 * - resolvers      : Mapping kolom → relasi readable (untuk export)
 * - importers      : Mapping kolom → logic lookup (untuk import)
 */

return [
    // ── KEPENDUDUKAN ─────────────────────────────────────────────
    'penduduk' => [
        'title'       => 'Data Penduduk',
        'model'       => \App\Models\Penduduk::class,
        'date_column' => 'tgl_input',
        'back_route'  => 'kependudukan.penduduk.index',
        'with'        => ['keluarga', 'rt.rw'],
        'columns'     => ['no_urut', 'no_urut_kk', 'nik', 'nama', 'jenis_kelamin', 'umur', 'status_dalam_keluarga', 'pendidikan', 'pekerjaan', 'wilayah', 'status_data', 'gol_darah'],
        'headers'     => ['NO URUT', 'NO URUT KK', 'NIK', 'NAMA WARGA', 'JENIS KELAMIN', 'UMUR', 'STATUS DALAM KELUARGA', 'PENDIDIKAN', 'PEKERJAAN', 'WILAYAH', 'STATUS', 'KATEGORI'],
        'required'    => ['nik', 'nama', 'jenis_kelamin'],
        'resolvers'   => [
            'no_urut'               => 'penduduk_no_urut',
            'no_urut_kk'            => 'penduduk_no_urut_kk',
            'umur'                  => 'penduduk_umur',
            'status_dalam_keluarga' => 'penduduk_status_dalam_keluarga',
            'wilayah'               => 'penduduk_wilayah',
            'status_data'           => 'penduduk_status_data',
            'gol_darah'             => 'penduduk_kategori',
        ],
        'import_columns' => ['no_urut', 'no_urut_kk', 'nik', 'nama', 'jenis_kelamin', 'umur', 'status_dalam_keluarga', 'pendidikan', 'pekerjaan', 'wilayah', 'status_data', 'gol_darah'],
        'import_headers' => ['NO URUT', 'NO URUT KK', 'NIK', 'NAMA WARGA', 'JENIS KELAMIN', 'UMUR', 'STATUS DALAM KELUARGA', 'PENDIDIKAN', 'PEKERJAAN', 'WILAYAH', 'STATUS', 'KATEGORI'],
        'unique_by'      => ['nik'],
        'example_row'    => ['1', '1', '7371125201800002', 'ROSALINA PONGANAN', 'P', '46', 'Kepala Rumah Tangga', 'SMU/SMA/Sederajat', 'Lainnya', 'RT 04 / RW 05', 'AKTIF', 'A'],
    ],

    'keluarga' => [
        'title'       => 'Data Keluarga',
        'model'       => \App\Models\Keluarga::class,
        'date_column' => 'tgl_input',
        'back_route'  => 'kependudukan.keluarga.index',
        'with'        => ['kepalaKeluarga', 'rt.rw'],
        'columns'     => ['no_kk', 'kepala_keluarga', 'jumlah_anggota_keluarga', 'rt_rw'],
        'headers'     => ['No KK', 'Kepala Keluarga', 'Jumlah Anggota', 'RT/RW'],
        'required'    => ['no_kk'],
        'resolvers'   => [
            'kepala_keluarga' => 'kepalaKeluarga.nama',
            'rt_rw'           => 'rt_rw_label',
        ],
        'import_columns' => ['no_kk', 'jumlah_anggota_keluarga'],
        'import_headers' => ['No KK', 'Jumlah Anggota'],
    ],

    'mutasi' => [
        'title'       => 'Mutasi Penduduk',
        'model'       => \App\Models\MutasiPenduduk::class,
        'date_column' => 'tanggal_mutasi',
        'back_route'  => 'kependudukan.mutasi.index',
        'with'        => ['penduduk', 'rtAsal.rw', 'rtTujuan.rw'],
        'columns'     => ['nama_penduduk', 'nik_penduduk', 'jenis_mutasi', 'tanggal_mutasi', 'alamat_asal', 'alamat_tujuan', 'alasan', 'keterangan', 'no_surat_pindah', 'status'],
        'headers'     => ['Nama Penduduk', 'NIK', 'Jenis Mutasi', 'Tanggal Mutasi', 'Alamat Asal', 'Alamat Tujuan', 'Alasan', 'Keterangan', 'No Surat Pindah', 'Status'],
        'required'    => ['jenis_mutasi', 'tanggal_mutasi'],
        'resolvers'   => [
            'nama_penduduk' => 'penduduk.nama',
            'nik_penduduk'  => 'penduduk.nik',
        ],
        'import_columns' => ['jenis_mutasi', 'tanggal_mutasi', 'alamat_asal', 'alamat_tujuan', 'alasan', 'keterangan', 'no_surat_pindah', 'status'],
        'import_headers' => ['Jenis Mutasi', 'Tanggal Mutasi', 'Alamat Asal', 'Alamat Tujuan', 'Alasan', 'Keterangan', 'No Surat Pindah', 'Status'],
    ],

    'kelahiran' => [
        'title'       => 'Data Kelahiran',
        'model'       => \App\Models\Kelahiran::class,
        'date_column' => 'tanggal_lahir',
        'back_route'  => 'kependudukan.kelahiran.index',
        'with'        => ['ibu', 'ayah', 'rt.rw'],
        'columns'     => ['nama_bayi', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'jam_lahir', 'nama_ibu', 'nama_ayah', 'no_akte', 'keterangan', 'rt_rw'],
        'headers'     => ['Nama Bayi', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir', 'Jam Lahir', 'Nama Ibu', 'Nama Ayah', 'No Akte', 'Keterangan', 'RT/RW'],
        'required'    => ['nama_bayi', 'jenis_kelamin', 'tanggal_lahir'],
        'resolvers'   => [
            'nama_ibu'  => 'ibu.nama',
            'nama_ayah' => 'ayah.nama',
            'rt_rw'     => 'rt_rw_label',
        ],
        'import_columns' => ['nama_bayi', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'jam_lahir', 'no_akte', 'keterangan'],
        'import_headers' => ['Nama Bayi', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir', 'Jam Lahir', 'No Akte', 'Keterangan'],
    ],

    'kematian' => [
        'title'       => 'Data Kematian',
        'model'       => \App\Models\Kematian::class,
        'date_column' => 'tanggal_meninggal',
        'back_route'  => 'kependudukan.kematian.index',
        'with'        => ['penduduk'],
        'columns'     => ['nama_penduduk', 'nik_penduduk', 'tanggal_meninggal', 'tempat_meninggal', 'penyebab', 'no_akte_kematian', 'keterangan'],
        'headers'     => ['Nama', 'NIK', 'Tanggal Meninggal', 'Tempat Meninggal', 'Penyebab', 'No Akte Kematian', 'Keterangan'],
        'required'    => ['tanggal_meninggal'],
        'resolvers'   => [
            'nama_penduduk' => 'penduduk.nama',
            'nik_penduduk'  => 'penduduk.nik',
        ],
        'import_columns' => ['tanggal_meninggal', 'tempat_meninggal', 'penyebab', 'no_akte_kematian', 'keterangan'],
        'import_headers' => ['Tanggal Meninggal', 'Tempat Meninggal', 'Penyebab', 'No Akte Kematian', 'Keterangan'],
    ],

    // ── DATA UMUM ────────────────────────────────────────────────
    'faskes' => [
        'title'       => 'Fasilitas Kesehatan',
        'model'       => \App\Models\Faskes::class,
        'date_column' => 'created_at',
        'back_route'  => 'data-umum.faskes.index',
        'with'        => ['kelurahan', 'rw'],
        'columns'     => ['nama_rs', 'jenis', 'kelas', 'jenis_pelayanan', 'alamat', 'rw_nomor', 'akreditasi', 'telp'],
        'headers'     => ['Nama Faskes', 'Jenis', 'Kelas', 'Jenis Pelayanan', 'Alamat', 'RW', 'Akreditasi', 'Telp'],
        'required'    => ['nama_rs'],
        'resolvers'   => [
            'rw_nomor' => 'rw.nomor',
        ],
        'import_columns' => ['nama_rs', 'jenis', 'kelas', 'jenis_pelayanan', 'alamat', 'akreditasi', 'telp'],
        'import_headers' => ['Nama Faskes', 'Jenis', 'Kelas', 'Jenis Pelayanan', 'Alamat', 'Akreditasi', 'Telp'],
    ],

    'sekolah' => [
        'title'       => 'Data Sekolah',
        'model'       => \App\Models\Sekolah::class,
        'date_column' => 'created_at',
        'back_route'  => 'data-umum.sekolah.index',
        'with'        => ['kelurahan'],
        'columns'     => ['npsn', 'nama_sekolah', 'jenjang', 'status', 'alamat', 'tahun_ajar', 'jumlah_siswa', 'rombel', 'jumlah_guru', 'jumlah_pegawai', 'ruang_kelas', 'jumlah_r_lab', 'jumlah_r_perpus'],
        'headers'     => ['NPSN', 'Nama Sekolah', 'Jenjang', 'Status', 'Alamat', 'Tahun Ajar', 'Jumlah Siswa', 'Rombel', 'Jumlah Guru', 'Jumlah Pegawai', 'Ruang Kelas', 'R. Lab', 'R. Perpustakaan'],
        'required'    => ['nama_sekolah'],
        'resolvers'   => [],
        'import_columns' => ['npsn', 'nama_sekolah', 'jenjang', 'status', 'alamat', 'tahun_ajar', 'jumlah_siswa', 'rombel', 'jumlah_guru', 'jumlah_pegawai', 'ruang_kelas', 'jumlah_r_lab', 'jumlah_r_perpus'],
        'import_headers' => ['NPSN', 'Nama Sekolah', 'Jenjang', 'Status', 'Alamat', 'Tahun Ajar', 'Jumlah Siswa', 'Rombel', 'Jumlah Guru', 'Jumlah Pegawai', 'Ruang Kelas', 'R. Lab', 'R. Perpustakaan'],
    ],

    'tempat-ibadah' => [
        'title'       => 'Tempat Ibadah',
        'model'       => \App\Models\TempatIbadah::class,
        'date_column' => 'created_at',
        'back_route'  => 'data-umum.tempat-ibadah.index',
        'with'        => ['kelurahan', 'rt.rw'],
        'columns'     => ['tempat_ibadah', 'nama', 'alamat', 'rt_rw', 'pengurus'],
        'headers'     => ['Jenis', 'Nama', 'Alamat', 'RT/RW', 'Pengurus'],
        'required'    => ['tempat_ibadah', 'nama'],
        'resolvers'   => [
            'rt_rw' => 'rt_rw_label',
        ],
        'import_columns' => ['tempat_ibadah', 'nama', 'alamat', 'pengurus'],
        'import_headers' => ['Jenis', 'Nama', 'Alamat', 'Pengurus'],
    ],

    'petugas-kebersihan' => [
        'title'       => 'Petugas Kebersihan',
        'model'       => \App\Models\PetugasKebersihan::class,
        'date_column' => 'created_at',
        'back_route'  => 'data-umum.petugas-kebersihan.index',
        'with'        => ['kelurahan'],
        'columns'     => ['nama', 'nik', 'jenis_kelamin', 'pekerjaan', 'unit_kerja', 'lokasi', 'status'],
        'headers'     => ['Nama', 'NIK', 'Jenis Kelamin', 'Pekerjaan', 'Unit Kerja', 'Lokasi', 'Status'],
        'required'    => ['nama'],
        'resolvers'   => [],
        'import_columns' => ['nama', 'nik', 'jenis_kelamin', 'pekerjaan', 'unit_kerja', 'lokasi', 'status'],
        'import_headers' => ['Nama', 'NIK', 'Jenis Kelamin', 'Pekerjaan', 'Unit Kerja', 'Lokasi', 'Status'],
    ],

    'kendaraan' => [
        'title'       => 'Data Kendaraan',
        'model'       => \App\Models\Kendaraan::class,
        'date_column' => 'created_at',
        'back_route'  => 'data-umum.kendaraan.index',
        'with'        => ['kelurahan'],
        'columns'     => ['jenis_barang', 'nama_pengemudi', 'no_polisi', 'no_rangka', 'no_mesin', 'tahun_perolehan', 'merek_type'],
        'headers'     => ['Jenis Barang', 'Nama Pengemudi', 'No Polisi', 'No Rangka', 'No Mesin', 'Tahun Perolehan', 'Merek/Type'],
        'required'    => ['jenis_barang'],
        'resolvers'   => [],
        'import_columns' => ['jenis_barang', 'nama_pengemudi', 'no_polisi', 'no_rangka', 'no_mesin', 'tahun_perolehan', 'merek_type'],
        'import_headers' => ['Jenis Barang', 'Nama Pengemudi', 'No Polisi', 'No Rangka', 'No Mesin', 'Tahun Perolehan', 'Merek/Type'],
    ],

    'kontrakan' => [
        'title'       => 'Data Kontrakan & Rumah Kost',
        'model'       => \App\Models\Kontrakan::class,
        'date_column' => 'created_at',
        'back_route'  => 'data-umum.kontrakan.index',
        'with'        => ['kelurahan', 'rw', 'rt'],
        'columns'     => ['nama', 'alamat', 'pemilik', 'rt_rw', 'jumlah_kontrakan', 'jumlah_kost_putera', 'jumlah_kost_putri', 'jumlah_kost_campur', 'keterangan'],
        'headers'     => ['Nama', 'Alamat', 'Pemilik', 'RT/RW', 'Jml Kontrakan', 'Kost Putera', 'Kost Putri', 'Kost Campur', 'Keterangan'],
        'required'    => ['nama'],
        'resolvers'   => [
            'rt_rw' => 'rt_rw_label',
        ],
        'import_columns' => ['nama', 'alamat', 'pemilik', 'jumlah_kontrakan', 'jumlah_kost_putera', 'jumlah_kost_putri', 'jumlah_kost_campur', 'keterangan'],
        'import_headers' => ['Nama', 'Alamat', 'Pemilik', 'Jml Kontrakan', 'Kost Putera', 'Kost Putri', 'Kost Campur', 'Keterangan'],
    ],

    'asrama' => [
        'title'       => 'Data Asrama',
        'model'       => \App\Models\Asrama::class,
        'date_column' => 'created_at',
        'back_route'  => 'data-umum.asrama.index',
        'with'        => ['kelurahan', 'rw', 'rt'],
        'columns'     => ['nama', 'alamat', 'jenis', 'jumlah', 'rt_rw', 'keterangan'],
        'headers'     => ['Nama', 'Alamat', 'Jenis', 'Jumlah', 'RT/RW', 'Keterangan'],
        'required'    => ['nama'],
        'resolvers'   => [
            'rt_rw' => 'rt_rw_label',
        ],
        'import_columns' => ['nama', 'alamat', 'jenis', 'jumlah', 'keterangan'],
        'import_headers' => ['Nama', 'Alamat', 'Jenis', 'Jumlah', 'Keterangan'],
    ],

    'pbb' => [
        'title'       => 'Data PBB',
        'model'       => \App\Models\Pbb::class,
        'date_column' => 'created_at',
        'back_route'  => 'data-umum.pbb.index',
        'with'        => ['kelurahan', 'rw', 'rt'],
        'columns'     => ['nama_wajib_pajak', 'objek_pajak', 'nob', 'beban', 'rt_rw', 'status', 'tahun_pajak', 'keterangan'],
        'headers'     => ['Nama Wajib Pajak', 'Objek Pajak', 'NOB', 'Beban', 'RT/RW', 'Status', 'Tahun Pajak', 'Keterangan'],
        'required'    => ['nama_wajib_pajak'],
        'resolvers'   => [
            'rt_rw' => 'rt_rw_label',
        ],
        'import_columns' => ['nama_wajib_pajak', 'objek_pajak', 'nob', 'beban', 'status', 'tahun_pajak', 'keterangan'],
        'import_headers' => ['Nama Wajib Pajak', 'Objek Pajak', 'NOB', 'Beban', 'Status', 'Tahun Pajak', 'Keterangan'],
    ],

    'retribusi-sampah' => [
        'title'       => 'Data Retribusi Sampah',
        'model'       => \App\Models\RetribusiSampah::class,
        'date_column' => 'created_at',
        'back_route'  => 'data-umum.retribusi-sampah.index',
        'with'        => ['kelurahan', 'rw', 'rt'],
        'columns'     => ['npwr', 'nama_nasabah', 'no_skrd', 'alamat', 'rw', 'rt', 'beban', 'status'],
        'headers'     => ['NPWR', 'NAMA', 'NO. SKRD', 'ALAMAT', 'RW', 'RT', 'TAGIHAN', 'KET (LUNAS/BELUM)'],
        'required'    => ['npwr', 'nama_nasabah', 'rw', 'rt'],
        'resolvers'   => [
            'rt' => 'rt',
            'rw' => 'rw',
            'beban' => 'retribusi_beban',
            'status' => 'retribusi_status',
        ],
        'import_columns' => ['npwr', 'nama_nasabah', 'no_skrd', 'alamat', 'rw', 'rt', 'beban', 'status'],
        'import_headers' => ['NPWR', 'NAMA', 'NO. SKRD', 'ALAMAT', 'RW', 'RT', 'TAGIHAN', 'KET (LUNAS/BELUM)'],
        'unique_by' => ['npwr'],
        'example_row' => ['71.F.02.05.01.001', 'Alfamart (Batua)', '240', 'Jl. Batua Raya No.3', '05', '01', '268000', ''],
    ],

    // ── USAHA ────────────────────────────────────────────────────
    'usaha' => [
        'title'       => 'Data Usaha UMKM',
        'model'       => \App\Models\Umkm::class,
        'date_column' => 'created_at',
        'back_route'  => 'usaha.index',
        'with'        => ['kelurahan', 'rt.rw', 'jenisUsaha', 'penduduk'],
        'columns'     => ['nama_ukm', 'nama_pemilik', 'nik_pemilik', 'no_hp', 'alamat', 'sektor_umkm', 'jenis_usaha_nama', 'rt_rw', 'status'],
        'headers'     => ['Nama Usaha', 'Nama Pemilik', 'NIK Pemilik', 'No HP', 'Alamat', 'Sektor', 'Jenis Usaha', 'RT/RW', 'Status'],
        'required'    => ['nama_ukm', 'nama_pemilik'],
        'resolvers'   => [
            'jenis_usaha_nama' => 'jenisUsaha.nama',
            'rt_rw'            => 'rt_rw_label',
        ],
        'import_columns' => ['nama_ukm', 'nama_pemilik', 'nik_pemilik', 'no_hp', 'alamat', 'sektor_umkm', 'status'],
        'import_headers' => ['Nama Usaha', 'Nama Pemilik', 'NIK Pemilik', 'No HP', 'Alamat', 'Sektor', 'Status'],
    ],

    'pengurus' => [
        'title'       => 'Pengurus RT/RW',
        'model'       => \App\Models\RtRwPengurus::class,
        'date_column' => 'tgl_mulai',
        'back_route'  => 'master.pengurus.index',
        'with'        => ['penduduk', 'jabatan', 'rw', 'rt'],
        'columns'     => ['rw', 'rt', 'nama', 'nik', 'alamat', 'pekerjaan', 'pendidikan', 'no_telp', 'keterangan'],
        'headers'     => ['RW', 'RT', 'NAMA', 'NIK', 'ALAMAT', 'PEKERJAAN', 'PENDIDIKAN TERAKHIR', 'NO. TLP', 'KET'],
        'required'    => ['rw', 'nama', 'nik'],
        'resolvers'   => [
            'rw'         => 'rw.nomor',
            'rt'         => 'rt.nomor',
            'nama'       => 'penduduk.nama',
            'nik'        => 'penduduk.nik',
            'pekerjaan'  => 'penduduk.pekerjaan',
            'pendidikan' => 'penduduk.pendidikan',
            'keterangan' => 'pengurus_keterangan',
        ],
        'import_columns' => ['rw', 'rt', 'nama', 'nik', 'alamat', 'pekerjaan', 'pendidikan', 'no_telp', 'keterangan'],
        'import_headers' => ['RW', 'RT', 'NAMA', 'NIK', 'ALAMAT', 'PEKERJAAN', 'PENDIDIKAN TERAKHIR', 'NO. TLP', 'KET'],
        'example_row'    => ['1', '1', 'DR. YOHANIS SATTU', '7371121404680011', 'JL. INSPEKSI PAM LR. 4 NO.17', 'PNS', 'S3', '08121262294', ''],
    ],

    // ── WILAYAH ──────────────────────────────────────────────────
    'wilayah' => [
        'title'       => 'Data Wilayah RT/RW',
        'model'       => \App\Models\Rt::class,
        'date_column' => 'created_at',
        'back_route'  => 'master.rt.index',
        'with'        => ['rw'],
        'columns'     => ['nomor', 'rw_nomor'],
        'headers'     => ['Nomor RT', 'Nomor RW'],
        'required'    => ['nomor'],
        'resolvers'   => [
            'rw_nomor' => 'rw.nomor',
        ],
        'import_columns' => ['nomor'],
        'import_headers' => ['Nomor RT'],
    ],
];
