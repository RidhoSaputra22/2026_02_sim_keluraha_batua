<?php

namespace Database\Seeders;

use App\Models\AgendaKegiatan;
use App\Models\HasilKegiatan;
use App\Models\Instansi;
use App\Models\Kelurahan;
use Illuminate\Database\Seeder;

class AgendaSurveySeeder extends Seeder
{
    public function run(): void
    {
        $kelurahan = Kelurahan::where('nama', 'Batua')->first();
        $instansiIds = Instansi::pluck('id')->toArray();

        // ═══════════════════════════════════════════════════════
        // Agenda Kegiatan
        // ═══════════════════════════════════════════════════════
        $agendaData = [
            [
                'hari_kegiatan'    => '2026-01-10',
                'jam'              => '09:00',
                'lokasi'           => 'Aula Kantor Kelurahan Batua',
                'instansi_idx'     => 0,
                'perihal'          => 'Musyawarah Perencanaan Pembangunan (Musrenbang) Kelurahan 2026',
                'penanggung_jawab' => 'Drs. H. Muhammad Arif, M.Si',
                'keterangan'       => 'Musyawarah perencanaan pembangunan tingkat kelurahan tahun anggaran 2026',
                'hasil' => [
                    'notulen'    => "Musrenbang Kelurahan Batua 2026\n\nHadir: 45 peserta (unsur RT/RW, tokoh masyarakat, BKM, LPM)\n\nHasil:\n1. Prioritas perbaikan jalan lorong di RW 003 dan RW 006\n2. Pembangunan drainase di RW 005\n3. Pengadaan lampu jalan di RW 007\n4. Program pemberdayaan UMKM kelurahan\n5. Revitalisasi posyandu di RW 001 dan RW 003",
                    'keterangan' => 'Dilaksanakan sesuai rencana, seluruh usulan diteruskan ke Musrenbang Kecamatan',
                ],
            ],
            [
                'hari_kegiatan'    => '2026-01-15',
                'jam'              => '08:00',
                'lokasi'           => 'Sepanjang Jl. Batua Raya',
                'instansi_idx'     => 0,
                'perihal'          => 'Kerja Bakti Lingkungan Kelurahan Batua',
                'penanggung_jawab' => 'Seluruh Ketua RW',
                'keterangan'       => 'Kerja bakti kebersihan lingkungan serentak seluruh RW',
                'hasil' => [
                    'notulen'    => "Kerja Bakti Lingkungan\n\nPartisipasi: 150+ warga dari 8 RW\nKegiatan: pembersihan selokan, pemotongan rumput, pengecatan trotoar\nDurasi: 08:00 - 12:00 WITA",
                    'keterangan' => 'Berjalan lancar, sisa sampah diangkut oleh DLH Kota Makassar',
                ],
            ],
            [
                'hari_kegiatan'    => '2026-01-22',
                'jam'              => '10:00',
                'lokasi'           => 'Puskesmas Batua',
                'instansi_idx'     => 4,
                'perihal'          => 'Posyandu Balita dan Lansia Bulan Januari',
                'penanggung_jawab' => 'Kader Posyandu Melati',
                'keterangan'       => 'Pelayanan imunisasi, penimbangan balita, dan pemeriksaan lansia',
                'hasil' => null,
            ],
            [
                'hari_kegiatan'    => '2026-02-05',
                'jam'              => '14:00',
                'lokasi'           => 'Aula Kantor Kelurahan Batua',
                'instansi_idx'     => 0,
                'perihal'          => 'Sosialisasi Program Bantuan Sosial 2026',
                'penanggung_jawab' => 'Kasi Kesejahteraan Sosial',
                'keterangan'       => 'Sosialisasi pendataan dan verifikasi penerima bantuan sosial tahun 2026',
                'hasil' => [
                    'notulen'    => "Sosialisasi Bansos 2026\n\nHadir: Ketua RT/RW, perwakilan warga penerima\n\nAgenda:\n1. Penjelasan mekanisme DTKS\n2. Verifikasi data penerima\n3. Jadwal penyaluran bantuan\n4. Tanya jawab",
                    'keterangan' => 'Warga memahami prosedur verifikasi DTKS',
                ],
            ],
            [
                'hari_kegiatan'    => '2026-02-14',
                'jam'              => '09:00',
                'lokasi'           => 'Lapangan Kelurahan Batua',
                'instansi_idx'     => 0,
                'perihal'          => 'Peringatan Hari Ulang Tahun Kota Makassar ke-417',
                'penanggung_jawab' => 'Panitia HUT Kota Makassar',
                'keterangan'       => 'Rangkaian kegiatan HUT Kota Makassar: upacara, lomba, dan pertunjukan budaya',
                'hasil' => null,
            ],
            [
                'hari_kegiatan'    => '2026-02-20',
                'jam'              => '13:00',
                'lokasi'           => 'Aula Kantor Kelurahan Batua',
                'instansi_idx'     => 2,
                'perihal'          => 'Sosialisasi e-KTP dan Kartu Identitas Anak (KIA)',
                'penanggung_jawab' => 'Dinas Kependudukan & Catatan Sipil',
                'keterangan'       => 'Sosialisasi perekaman e-KTP dan penerbitan KIA untuk anak di bawah 17 tahun',
                'hasil' => null,
            ],
        ];

        foreach ($agendaData as $a) {
            $agenda = AgendaKegiatan::create([
                'kelurahan_id'     => $kelurahan->id,
                'hari_kegiatan'    => $a['hari_kegiatan'],
                'jam'              => $a['jam'],
                'lokasi'           => $a['lokasi'],
                'instansi_id'      => $instansiIds[$a['instansi_idx']] ?? null,
                'perihal'          => $a['perihal'],
                'penanggung_jawab' => $a['penanggung_jawab'],
                'keterangan'       => $a['keterangan'],
            ]);

            if ($a['hasil']) {
                HasilKegiatan::create([
                    'agenda_id'    => $agenda->id,
                    'hari_tanggal' => $a['hari_kegiatan'] . ' ' . $a['jam'],
                    'notulen'      => $a['hasil']['notulen'],
                    'keterangan'   => $a['hasil']['keterangan'],
                ]);
            }
        }
    }
}
