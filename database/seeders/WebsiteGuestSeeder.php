<?php

namespace Database\Seeders;

use App\Models\Berita;
use App\Models\DestinasiWisata;
use App\Models\DokumenPublik;
use App\Models\LayananSurat;
use App\Models\PengaduanWarga;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class WebsiteGuestSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedBerita();
        $this->seedDokumenPublik();
        $this->seedLayananSurat();
        $this->seedDestinasiWisata();
        $this->seedPengaduanWarga();
    }

    private function seedBerita(): void
    {
        $items = [
            [
                'slug' => 'kerja-bakti-rutin-rw-04',
                'judul' => 'Kerja Bakti Rutin RW 04 Fokus pada Pembersihan Drainase',
                'kategori' => 'kegiatan',
                'ringkasan' => 'Warga bersama pengurus RW dan RT membersihkan saluran air untuk mencegah genangan saat musim hujan.',
                'isi' => "Warga RW 04 kembali menggelar kerja bakti rutin pada akhir pekan ini.\n\nKegiatan difokuskan pada pembersihan drainase lingkungan, pemangkasan rumput liar, dan penataan titik pembuangan sampah sementara.\n\nKelurahan mengapresiasi partisipasi warga karena kegiatan seperti ini sangat membantu menjaga kebersihan kawasan sekaligus memperkuat gotong royong antarwarga.",
                'is_featured' => true,
                'published_at' => now()->subDays(2),
                'warna' => '#DC2626',
            ],
            [
                'slug' => 'pengumuman-posyandu-balita',
                'judul' => 'Pengumuman Jadwal Posyandu Balita dan Ibu Hamil Bulan Ini',
                'kategori' => 'pengumuman',
                'ringkasan' => 'Pelayanan posyandu akan dilakukan bertahap per RW untuk memudahkan warga memeriksa kesehatan balita dan ibu hamil.',
                'isi' => "Pelayanan Posyandu bulan ini akan dilaksanakan secara bertahap di tiap RW.\n\nWarga diimbau membawa buku KIA, kartu identitas, dan memastikan datang sesuai jadwal agar antrean lebih tertib.\n\nInformasi teknis lebih lanjut dapat ditanyakan ke kader posyandu masing-masing wilayah.",
                'is_featured' => false,
                'published_at' => now()->subDays(5),
                'warna' => '#0F766E',
            ],
            [
                'slug' => 'pelatihan-umkm-digital',
                'judul' => 'Pelatihan UMKM Digital Siap Dukung Promosi Produk Warga',
                'kategori' => 'berita',
                'ringkasan' => 'Kelurahan membuka pelatihan singkat pemasaran digital untuk membantu pelaku usaha lokal memperluas pasar.',
                'isi' => "Pelaku UMKM di wilayah kelurahan mendapatkan kesempatan mengikuti pelatihan pemasaran digital.\n\nMateri yang dibahas meliputi foto produk sederhana, pengelolaan katalog, penulisan caption promosi, dan pemanfaatan WhatsApp Business.\n\nProgram ini diharapkan membantu usaha warga tampil lebih profesional di ruang digital.",
                'is_featured' => false,
                'published_at' => now()->subDays(8),
                'warna' => '#2563EB',
            ],
            [
                'slug' => 'agenda-musrenbang-kelurahan',
                'judul' => 'Agenda Musrenbang Kelurahan Dibuka untuk Usulan Prioritas Lingkungan',
                'kategori' => 'agenda',
                'ringkasan' => 'Masyarakat dapat menyampaikan usulan prioritas sarana, kebersihan, dan pelayanan lingkungan melalui forum musrenbang kelurahan.',
                'isi' => "Forum musrenbang kelurahan akan menjadi ruang sinkronisasi usulan pembangunan dari warga, RT, dan RW.\n\nUsulan yang didorong meliputi perbaikan jalan lingkungan, penerangan, drainase, dan penguatan layanan sosial dasar.\n\nWarga diharapkan menyampaikan usulan yang jelas, terukur, dan sesuai kebutuhan prioritas lingkungan.",
                'is_featured' => false,
                'published_at' => now()->subDays(12),
                'warna' => '#7C3AED',
            ],
        ];

        foreach ($items as $item) {
            $gambar = $this->storeSvg(
                "website/seed/berita/{$item['slug']}.svg",
                $item['judul'],
                ucfirst($item['kategori']),
                $item['warna']
            );

            Berita::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'judul' => $item['judul'],
                    'kategori' => $item['kategori'],
                    'ringkasan' => $item['ringkasan'],
                    'isi' => $item['isi'],
                    'gambar' => $gambar,
                    'is_featured' => $item['is_featured'],
                    'is_published' => true,
                    'published_at' => $item['published_at'],
                ]
            );
        }
    }

    private function seedDokumenPublik(): void
    {
        $items = [
            [
                'slug' => 'laporan-kinerja-kelurahan-2026',
                'judul' => 'Laporan Kinerja Kelurahan 2026',
                'kategori' => 'laporan',
                'deskripsi' => 'Ringkasan capaian layanan, kegiatan wilayah, dan tindak lanjut prioritas kelurahan.',
                'published_at' => now()->subDays(1),
                'warna' => '#1D4ED8',
                'isi' => "Laporan Kinerja Kelurahan 2026\n\nDokumen ini memuat ringkasan capaian pelayanan, kegiatan wilayah, dan fokus pembenahan layanan publik tahun berjalan.",
            ],
            [
                'slug' => 'jadwal-pelayanan-bulan-ini',
                'judul' => 'Jadwal Pelayanan Bulan Ini',
                'kategori' => 'pengumuman',
                'deskripsi' => 'Jadwal operasional loket dan layanan administrasi kelurahan per bulan berjalan.',
                'published_at' => now()->subDays(3),
                'warna' => '#DC2626',
                'isi' => "Jadwal Pelayanan Bulan Ini\n\nSilakan cek pembagian jam pelayanan, agenda keliling, dan penyesuaian layanan pada hari libur nasional.",
            ],
            [
                'slug' => 'panduan-persyaratan-surat',
                'judul' => 'Panduan Persyaratan Surat',
                'kategori' => 'formulir',
                'deskripsi' => 'Panduan ringkas persiapan dokumen sebelum mengurus surat di kantor kelurahan.',
                'published_at' => now()->subDays(6),
                'warna' => '#0F766E',
                'isi' => "Panduan Persyaratan Surat\n\nPanduan ini membantu warga menyiapkan KTP, KK, surat pengantar, dan lampiran pendukung sesuai kebutuhan layanan.",
            ],
            [
                'slug' => 'profil-ringkas-kelurahan',
                'judul' => 'Profil Ringkas Kelurahan',
                'kategori' => 'regulasi',
                'deskripsi' => 'Dokumen singkat yang berisi gambaran umum wilayah, kontak, dan layanan utama kelurahan.',
                'published_at' => now()->subDays(9),
                'warna' => '#7C3AED',
                'isi' => "Profil Ringkas Kelurahan\n\nDokumen ini merangkum informasi wilayah, kontak kantor, dan tautan penting layanan publik kelurahan.",
            ],
        ];

        foreach ($items as $item) {
            $dokumen = $this->storeTextDocument(
                "website/seed/dokumen/{$item['slug']}.txt",
                $item['judul'],
                $item['isi']
            );

            $cover = $this->storeSvg(
                "website/seed/dokumen/{$item['slug']}.svg",
                $item['judul'],
                ucfirst($item['kategori']),
                $item['warna']
            );

            DokumenPublik::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'judul' => $item['judul'],
                    'kategori' => $item['kategori'],
                    'deskripsi' => $item['deskripsi'],
                    'file_path' => $dokumen['path'],
                    'cover_image' => $cover,
                    'mime_type' => $dokumen['mime_type'],
                    'file_size' => $dokumen['file_size'],
                    'is_published' => true,
                    'published_at' => $item['published_at'],
                ]
            );
        }
    }

    private function seedLayananSurat(): void
    {
        $items = [
            [
                'slug' => 'surat-keterangan-domisili',
                'nama' => 'Surat Keterangan Domisili',
                'icon' => 'home',
                'deskripsi' => 'Digunakan untuk menerangkan domisili warga sesuai alamat tinggal saat ini.',
                'estimasi_layanan' => '1 hari kerja',
                'biaya' => 'Gratis',
                'kontak_petugas' => 'Loket Pelayanan Kelurahan',
                'catatan' => 'Pastikan alamat pada dokumen pendukung sesuai dengan data domisili yang diajukan.',
                'persyaratan' => [
                    ['nama' => 'Fotokopi KTP pemohon', 'keterangan' => '1 lembar', 'is_required' => true],
                    ['nama' => 'Fotokopi Kartu Keluarga', 'keterangan' => '1 lembar', 'is_required' => true],
                    ['nama' => 'Surat pengantar RT/RW', 'keterangan' => 'Asli', 'is_required' => true],
                ],
            ],
            [
                'slug' => 'surat-keterangan-usaha',
                'nama' => 'Surat Keterangan Usaha',
                'icon' => 'storefront',
                'deskripsi' => 'Digunakan untuk kebutuhan administrasi usaha, perbankan, atau verifikasi usaha warga.',
                'estimasi_layanan' => '1-2 hari kerja',
                'biaya' => 'Gratis',
                'kontak_petugas' => 'Seksi Pelayanan',
                'catatan' => 'Petugas dapat melakukan verifikasi lapangan bila diperlukan.',
                'persyaratan' => [
                    ['nama' => 'Fotokopi KTP pemilik usaha', 'keterangan' => null, 'is_required' => true],
                    ['nama' => 'Fotokopi Kartu Keluarga', 'keterangan' => null, 'is_required' => true],
                    ['nama' => 'Surat pengantar RT/RW', 'keterangan' => null, 'is_required' => true],
                    ['nama' => 'Foto lokasi / bukti usaha', 'keterangan' => 'Opsional namun dianjurkan', 'is_required' => false],
                ],
            ],
            [
                'slug' => 'sktm',
                'nama' => 'Surat Keterangan Tidak Mampu',
                'icon' => 'receipt_long',
                'deskripsi' => 'Digunakan sebagai dokumen pendukung untuk bantuan sosial, pendidikan, atau layanan tertentu.',
                'estimasi_layanan' => '1 hari kerja',
                'biaya' => 'Gratis',
                'kontak_petugas' => 'Loket Administrasi',
                'catatan' => 'Data pemohon akan diverifikasi berdasarkan kondisi keluarga dan keterangan lingkungan.',
                'persyaratan' => [
                    ['nama' => 'Fotokopi KTP dan KK', 'keterangan' => 'Masing-masing 1 lembar', 'is_required' => true],
                    ['nama' => 'Surat pengantar RT/RW', 'keterangan' => 'Asli', 'is_required' => true],
                    ['nama' => 'Dokumen pendukung tujuan penggunaan', 'keterangan' => 'Contoh: surat sekolah / rumah sakit', 'is_required' => false],
                ],
            ],
            [
                'slug' => 'pengantar-nikah',
                'nama' => 'Surat Pengantar Nikah',
                'icon' => 'favorite',
                'deskripsi' => 'Persiapan administrasi warga sebelum melanjutkan proses ke KUA atau instansi terkait.',
                'estimasi_layanan' => '1 hari kerja',
                'biaya' => 'Gratis',
                'kontak_petugas' => 'Seksi Pemerintahan',
                'catatan' => 'Data calon pengantin harus sinkron dengan dokumen kependudukan.',
                'persyaratan' => [
                    ['nama' => 'Fotokopi KTP calon pengantin', 'keterangan' => 'Masing-masing 1 lembar', 'is_required' => true],
                    ['nama' => 'Fotokopi Kartu Keluarga', 'keterangan' => null, 'is_required' => true],
                    ['nama' => 'Surat pengantar RT/RW', 'keterangan' => null, 'is_required' => true],
                ],
            ],
            [
                'slug' => 'surat-kematian',
                'nama' => 'Surat Keterangan Kematian',
                'icon' => 'personal_injury',
                'deskripsi' => 'Digunakan untuk pencatatan administrasi dan pembaruan data kependudukan keluarga.',
                'estimasi_layanan' => '1 hari kerja',
                'biaya' => 'Gratis',
                'kontak_petugas' => 'Loket Pelayanan',
                'catatan' => 'Mohon membawa dokumen asli saat verifikasi di loket.',
                'persyaratan' => [
                    ['nama' => 'Fotokopi KTP almarhum / almarhumah', 'keterangan' => null, 'is_required' => true],
                    ['nama' => 'Fotokopi Kartu Keluarga', 'keterangan' => null, 'is_required' => true],
                    ['nama' => 'Surat keterangan kematian dari rumah sakit / pihak terkait', 'keterangan' => null, 'is_required' => true],
                ],
            ],
        ];

        foreach ($items as $index => $item) {
            $layanan = LayananSurat::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'nama' => $item['nama'],
                    'deskripsi' => $item['deskripsi'],
                    'icon' => $item['icon'],
                    'estimasi_layanan' => $item['estimasi_layanan'],
                    'biaya' => $item['biaya'],
                    'catatan' => $item['catatan'],
                    'kontak_petugas' => $item['kontak_petugas'],
                    'is_active' => true,
                    'sort_order' => ($index + 1) * 10,
                ]
            );

            $layanan->persyaratans()->delete();
            $layanan->persyaratans()->createMany(
                collect($item['persyaratan'])->map(function (array $persyaratan, int $idx) {
                    return [
                        'nama' => $persyaratan['nama'],
                        'keterangan' => $persyaratan['keterangan'],
                        'is_required' => $persyaratan['is_required'],
                        'sort_order' => ($idx + 1) * 10,
                    ];
                })->all()
            );
        }
    }

    private function seedDestinasiWisata(): void
    {
        $items = [
            [
                'slug' => 'taman-batua-hijau',
                'nama' => 'Taman Batua Hijau',
                'kategori' => 'ruang-terbuka',
                'ringkasan' => 'Ruang terbuka warga untuk olahraga pagi, bermain anak, dan kegiatan komunitas.',
                'deskripsi' => 'Taman ini menjadi salah satu ruang berkumpul warga yang sering dipakai untuk senam pagi, kerja bakti, dan kegiatan lingkungan.',
                'alamat' => 'Jl. Batua Raya, dekat kantor kelurahan',
                'jam_operasional' => '06.00 - 18.00 WITA',
                'harga_tiket' => 'Gratis',
                'kontak' => '0411-123456',
                'maps_url' => 'https://maps.google.com/?q=Batua+Makassar',
                'is_featured' => true,
                'warna' => '#16A34A',
            ],
            [
                'slug' => 'sentra-kuliner-batua',
                'nama' => 'Sentra Kuliner Batua',
                'kategori' => 'kuliner',
                'ringkasan' => 'Pusat jajanan dan makanan lokal warga yang ramai saat sore hingga malam hari.',
                'deskripsi' => 'Di area ini warga dapat menemukan aneka kuliner rumahan, minuman segar, dan jajanan khas Makassar.',
                'alamat' => 'Koridor utama lingkungan Batua',
                'jam_operasional' => '16.00 - 22.00 WITA',
                'harga_tiket' => 'Sesuai menu',
                'kontak' => '081245678901',
                'maps_url' => 'https://maps.google.com/?q=Batua+Kuliner',
                'is_featured' => true,
                'warna' => '#EA580C',
            ],
            [
                'slug' => 'panggung-warga-batua',
                'nama' => 'Panggung Warga Batua',
                'kategori' => 'budaya',
                'ringkasan' => 'Area kegiatan warga untuk pertunjukan seni, edukasi komunitas, dan agenda kampung.',
                'deskripsi' => 'Panggung ini dipakai untuk lomba warga, pertunjukan seni lokal, dan forum kegiatan kepemudaan.',
                'alamat' => 'Lapangan serbaguna RW tengah',
                'jam_operasional' => 'Menyesuaikan agenda',
                'harga_tiket' => 'Gratis',
                'kontak' => 'Kelurahan Batua',
                'maps_url' => 'https://maps.google.com/?q=Batua+Budaya',
                'is_featured' => false,
                'warna' => '#7C3AED',
            ],
            [
                'slug' => 'pojok-baca-kelurahan',
                'nama' => 'Pojok Baca Kelurahan',
                'kategori' => 'layanan',
                'ringkasan' => 'Layanan baca ringan dan ruang tunggu edukatif bagi warga yang datang ke kantor kelurahan.',
                'deskripsi' => 'Pojok baca menyediakan bahan bacaan ringan, informasi layanan, dan ruang tunggu yang lebih nyaman bagi warga.',
                'alamat' => 'Area kantor kelurahan',
                'jam_operasional' => '08.00 - 16.00 WITA',
                'harga_tiket' => 'Gratis',
                'kontak' => 'Loket Pelayanan',
                'maps_url' => 'https://maps.google.com/?q=Kantor+Kelurahan+Batua',
                'is_featured' => false,
                'warna' => '#2563EB',
            ],
        ];

        foreach ($items as $index => $item) {
            $gambar = $this->storeSvg(
                "website/seed/wisata/{$item['slug']}.svg",
                $item['nama'],
                ucfirst(str_replace('-', ' ', $item['kategori'])),
                $item['warna']
            );

            DestinasiWisata::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'nama' => $item['nama'],
                    'kategori' => $item['kategori'],
                    'ringkasan' => $item['ringkasan'],
                    'deskripsi' => $item['deskripsi'],
                    'gambar' => $gambar,
                    'alamat' => $item['alamat'],
                    'jam_operasional' => $item['jam_operasional'],
                    'harga_tiket' => $item['harga_tiket'],
                    'kontak' => $item['kontak'],
                    'maps_url' => $item['maps_url'],
                    'is_featured' => $item['is_featured'],
                    'is_published' => true,
                    'sort_order' => ($index + 1) * 10,
                ]
            );
        }
    }

    private function seedPengaduanWarga(): void
    {
        $items = [
            [
                'kode_pengaduan' => 'ADU-SEED-001',
                'nama' => 'Rahmawati',
                'email' => 'rahmawati@example.com',
                'no_hp' => '081245600001',
                'kategori' => 'kebersihan',
                'subjek' => 'Sampah di sudut gang belum terangkut',
                'lokasi' => 'Gang Melati RW 03',
                'isi_laporan' => 'Tumpukan sampah terlihat sejak dua hari terakhir dan mulai menimbulkan bau.',
                'status' => 'ditinjau',
                'catatan_admin' => 'Menunggu koordinasi petugas kebersihan wilayah.',
                'ditindaklanjuti_at' => now()->subDay(),
            ],
            [
                'kode_pengaduan' => 'ADU-SEED-002',
                'nama' => 'Fadli',
                'email' => 'fadli@example.com',
                'no_hp' => '081245600002',
                'kategori' => 'infrastruktur',
                'subjek' => 'Lampu jalan padam',
                'lokasi' => 'Lorong utama dekat lapangan warga',
                'isi_laporan' => 'Lampu jalan padam pada malam hari sehingga akses lingkungan menjadi gelap.',
                'status' => 'diproses',
                'catatan_admin' => 'Sudah diteruskan ke tim teknis untuk pengecekan.',
                'ditindaklanjuti_at' => now()->subHours(12),
            ],
            [
                'kode_pengaduan' => 'ADU-SEED-003',
                'nama' => 'Nur Aisyah',
                'email' => 'aisyah@example.com',
                'no_hp' => '081245600003',
                'kategori' => 'layanan',
                'subjek' => 'Usulan penambahan informasi persyaratan surat',
                'lokasi' => 'Portal website publik',
                'isi_laporan' => 'Warga berharap daftar persyaratan surat selalu diperbarui agar tidak salah membawa berkas.',
                'status' => 'selesai',
                'catatan_admin' => 'Sudah ditindaklanjuti melalui pembaruan modul layanan surat.',
                'ditindaklanjuti_at' => now()->subHours(4),
            ],
        ];

        foreach ($items as $item) {
            PengaduanWarga::updateOrCreate(
                ['kode_pengaduan' => $item['kode_pengaduan']],
                $item
            );
        }
    }

    private function storeSvg(string $path, string $title, string $subtitle, string $primaryColor): string
    {
        $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $safeSubtitle = htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8');

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1280" height="720" viewBox="0 0 1280 720">
  <defs>
    <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="{$primaryColor}"/>
      <stop offset="100%" stop-color="#0F172A"/>
    </linearGradient>
  </defs>
  <rect width="1280" height="720" fill="url(#bg)"/>
  <circle cx="1080" cy="140" r="120" fill="rgba(255,255,255,0.12)"/>
  <circle cx="180" cy="560" r="180" fill="rgba(255,255,255,0.08)"/>
  <rect x="90" y="90" width="1100" height="540" rx="32" fill="rgba(255,255,255,0.08)" stroke="rgba(255,255,255,0.2)"/>
  <text x="120" y="220" fill="#ffffff" font-family="Arial, Helvetica, sans-serif" font-size="64" font-weight="700">{$safeTitle}</text>
  <text x="120" y="300" fill="#FDE68A" font-family="Arial, Helvetica, sans-serif" font-size="32" font-weight="600">{$safeSubtitle}</text>
  <text x="120" y="380" fill="rgba(255,255,255,0.8)" font-family="Arial, Helvetica, sans-serif" font-size="28">Seeder Website Publik Kelurahan</text>
</svg>
SVG;

        Storage::disk('public')->put($path, $svg);

        return $path;
    }

    private function storeTextDocument(string $path, string $title, string $body): array
    {
        $content = $title . PHP_EOL . str_repeat('=', mb_strlen($title)) . PHP_EOL . PHP_EOL . $body . PHP_EOL . PHP_EOL . 'Dokumen contoh hasil WebsiteGuestSeeder.';

        Storage::disk('public')->put($path, $content);

        return [
            'path' => $path,
            'file_size' => strlen($content),
            'mime_type' => 'text/plain',
        ];
    }
}
