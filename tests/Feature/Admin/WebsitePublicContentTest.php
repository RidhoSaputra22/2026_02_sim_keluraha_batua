<?php

namespace Tests\Feature\Admin;

use App\Models\DokumenPublik;
use App\Models\LayananSurat;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WebsitePublicContentTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $adminRole = Role::create([
            'name' => Role::ADMIN,
            'label' => 'Admin Sistem',
            'description' => 'Full system access',
            'permissions' => ['*'],
            'is_active' => true,
        ]);

        $this->admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_store_layanan_surat_with_multiple_requirements(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.website.layanan-surat.store'), [
            'nama' => 'Surat Keterangan Domisili',
            'deskripsi' => 'Layanan untuk warga yang membutuhkan surat domisili.',
            'icon' => 'home',
            'estimasi_layanan' => '1 hari kerja',
            'biaya' => 'Gratis',
            'kontak_petugas' => '081234567890',
            'is_active' => '1',
            'persyaratan' => [
                ['nama' => 'Fotokopi KTP', 'keterangan' => '1 lembar', 'is_required' => '1'],
                ['nama' => 'Fotokopi KK', 'keterangan' => '1 lembar', 'is_required' => '1'],
            ],
        ]);

        $response->assertRedirect(route('admin.website.layanan-surat.index'));
        $response->assertSessionHas('success');

        $layanan = LayananSurat::first();

        $this->assertNotNull($layanan);
        $this->assertSame('Surat Keterangan Domisili', $layanan->nama);
        $this->assertDatabaseCount('layanan_surat_persyaratans', 2);
        $this->assertDatabaseHas('layanan_surat_persyaratans', [
            'layanan_surat_id' => $layanan->id,
            'nama' => 'Fotokopi KTP',
        ]);
    }

    public function test_admin_can_store_dokumen_publik_with_file_upload(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin)->post(route('admin.website.dokumen-publik.store'), [
            'judul' => 'Laporan Transparansi',
            'kategori' => 'transparansi',
            'deskripsi' => 'Dokumen laporan untuk warga.',
            'is_published' => '1',
            'cover_image' => UploadedFile::fake()->image('cover.jpg'),
            'file_dokumen' => UploadedFile::fake()->create('laporan.pdf', 120, 'application/pdf'),
        ]);

        $response->assertRedirect(route('admin.website.dokumen-publik.index'));
        $response->assertSessionHas('success');

        $dokumen = DokumenPublik::first();

        $this->assertNotNull($dokumen);
        $this->assertSame('Laporan Transparansi', $dokumen->judul);
        Storage::disk('public')->assertExists($dokumen->file_path);
        Storage::disk('public')->assertExists($dokumen->cover_image);
    }
}
