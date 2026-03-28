<?php

namespace Tests\Feature\Guest;

use App\Models\Berita;
use App\Models\DestinasiWisata;
use App\Models\DokumenPublik;
use App\Models\LayananSurat;
use App\Models\PengaduanWarga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicWebsiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_guest_pages_render_admin_managed_content(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('website/dokumen/file/laporan.pdf', 'isi dokumen');

        $berita = Berita::create([
            'judul' => 'Kegiatan Bersih Lingkungan',
            'kategori' => 'kegiatan',
            'ringkasan' => 'Warga bergotong royong membersihkan drainase.',
            'isi' => 'Isi berita kegiatan bersih lingkungan.',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $beritaTerkait = Berita::create([
            'judul' => 'Jadwal Kerja Bakti RW 04',
            'kategori' => 'kegiatan',
            'ringkasan' => 'Agenda kerja bakti mingguan untuk warga.',
            'isi' => 'Isi berita kerja bakti warga.',
            'is_published' => true,
            'published_at' => now()->subHour(),
        ]);

        Berita::create([
            'judul' => 'Draft Internal',
            'kategori' => 'berita',
            'isi' => 'Tidak boleh tampil di guest.',
            'is_published' => false,
        ]);

        $dokumen = DokumenPublik::create([
            'judul' => 'Laporan Transparansi 2026',
            'kategori' => 'transparansi',
            'deskripsi' => 'Dokumen yang bisa diunduh warga.',
            'file_path' => 'website/dokumen/file/laporan.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'is_published' => true,
            'published_at' => now(),
        ]);

        $layanan = LayananSurat::create([
            'nama' => 'Surat Domisili',
            'deskripsi' => 'Surat keterangan tempat tinggal warga.',
            'biaya' => 'Gratis',
            'is_active' => true,
        ]);

        $layanan->persyaratans()->create([
            'nama' => 'Fotokopi KTP',
            'is_required' => true,
            'sort_order' => 10,
        ]);

        DestinasiWisata::create([
            'nama' => 'Taman Kelurahan',
            'kategori' => 'ruang-terbuka',
            'ringkasan' => 'Ruang terbuka untuk warga.',
            'is_featured' => true,
            'is_published' => true,
        ]);

        $this->get(route('guest.welcome'))
            ->assertOk()
            ->assertSee($berita->judul)
            ->assertSee($dokumen->judul)
            ->assertSee('Taman Kelurahan');

        $this->get(route('guest.administrasi'))
            ->assertOk()
            ->assertSee($layanan->nama)
            ->assertSee('Lihat Detail');

        $this->get(route('guest.administrasi.show', $layanan))
            ->assertOk()
            ->assertSee($layanan->nama)
            ->assertSee('Fotokopi KTP');

        $this->get(route('guest.publikasi'))
            ->assertOk()
            ->assertSee($berita->judul)
            ->assertSee($dokumen->judul)
            ->assertDontSee('Draft Internal');

        $this->get(route('guest.berita.show', $berita))
            ->assertOk()
            ->assertSee('Isi berita kegiatan bersih lingkungan.')
            ->assertSee('Bagikan Informasi')
            ->assertSee('Salin Link')
            ->assertSee($beritaTerkait->judul);
    }

    public function test_guest_can_submit_pengaduan(): void
    {
        Storage::fake('public');

        $response = $this->post(route('guest.pengaduan.store'), [
            'nama' => 'Warga Batua',
            'no_hp' => '081234567890',
            'email' => 'warga@example.com',
            'kategori' => 'kebersihan',
            'subjek' => 'Sampah belum terangkut',
            'lokasi' => 'Jalan Batua Raya',
            'isi_laporan' => 'Sampah menumpuk sejak dua hari lalu.',
            'lampiran' => UploadedFile::fake()->image('aduan.jpg'),
        ]);

        $response->assertRedirect(route('guest.pengaduan'));
        $response->assertSessionHas('success');

        $pengaduan = PengaduanWarga::first();

        $this->assertNotNull($pengaduan);
        $this->assertSame('Sampah belum terangkut', $pengaduan->subjek);
        $this->assertSame('baru', $pengaduan->status);
        Storage::disk('public')->assertExists($pengaduan->lampiran);
        $this->assertDatabaseHas('pengaduan_wargas', [
            'nama' => 'Warga Batua',
            'kategori' => 'kebersihan',
        ]);
    }

    public function test_guest_administrasi_search_results_are_paginated(): void
    {
        foreach (range(1, 9) as $number) {
            LayananSurat::create([
                'nama' => "Surat Pagination {$number}",
                'deskripsi' => "Deskripsi layanan {$number}",
                'is_active' => true,
            ]);
        }

        $this->get(route('guest.administrasi', ['q' => 'Surat Pagination']))
            ->assertOk()
            ->assertSee('Surat Pagination 1')
            ->assertDontSee('Surat Pagination 9')
            ->assertSee('page=2', false);

        $this->get(route('guest.administrasi', ['q' => 'Surat Pagination', 'page' => 2]))
            ->assertOk()
            ->assertSee('Surat Pagination 9')
            ->assertDontSee('Surat Pagination 1');
    }

    public function test_guest_global_search_results_are_paginated(): void
    {
        foreach (range(1, 11) as $number) {
            $label = str_pad((string) $number, 2, '0', STR_PAD_LEFT);

            LayananSurat::create([
                'nama' => "Hasil Global {$label}",
                'deskripsi' => "Pencarian global {$label}",
                'is_active' => true,
            ]);
        }

        $this->get(route('guest.search', ['q' => 'Hasil Global']))
            ->assertOk()
            ->assertSee('Hasil Global 01')
            ->assertDontSee('Hasil Global 11')
            ->assertSee('page=2', false);

        $this->get(route('guest.search', ['q' => 'Hasil Global', 'page' => 2]))
            ->assertOk()
            ->assertSee('Hasil Global 11')
            ->assertDontSee('Hasil Global 01');
    }

    public function test_guest_publikasi_document_search_results_show_document_pagination(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('website/dokumen/file/arsip.pdf', 'isi dokumen');

        foreach (range(1, 9) as $number) {
            $label = str_pad((string) $number, 2, '0', STR_PAD_LEFT);

            DokumenPublik::create([
                'judul' => "Dokumen Arsip {$label}",
                'kategori' => 'transparansi',
                'deskripsi' => "Deskripsi dokumen {$label}",
                'file_path' => 'website/dokumen/file/arsip.pdf',
                'mime_type' => 'application/pdf',
                'file_size' => 1024,
                'is_published' => true,
                'published_at' => now()->subSeconds(20 - $number),
            ]);
        }

        $this->get(route('guest.publikasi', ['q' => 'Dokumen Arsip']))
            ->assertOk()
            ->assertSee('Dokumen Arsip 09')
            ->assertDontSee('Dokumen Arsip 01')
            ->assertSee('dokumen_page=2', false);

        $this->get(route('guest.publikasi', ['q' => 'Dokumen Arsip', 'dokumen_page' => 2]))
            ->assertOk()
            ->assertSee('Dokumen Arsip 01')
            ->assertDontSee('Dokumen Arsip 09');
    }
}
