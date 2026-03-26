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
            ->assertSee($layanan->nama);

        $this->get(route('guest.surat-online'))
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
            ->assertSee('Isi berita kegiatan bersih lingkungan.');
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
}
