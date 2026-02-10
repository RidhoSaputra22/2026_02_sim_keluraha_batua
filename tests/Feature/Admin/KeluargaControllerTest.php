<?php

namespace Tests\Feature\Admin;

use App\Models\DataKeluarga;
use App\Models\DataPenduduk;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KeluargaControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::create([
            'name'        => Role::ADMIN,
            'label'       => 'Admin Sistem',
            'description' => 'Full system access',
            'permissions' => ['*'],
            'is_active'   => true,
        ]);

        $this->admin = User::factory()->create([
            'role_id'   => $adminRole->id,
            'is_active' => true,
        ]);
    }

    // ─── INDEX ────────────────────────────────────────────────────

    public function test_admin_can_view_keluarga_index(): void
    {
        DataKeluarga::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.keluarga.index'));

        $response->assertStatus(200);
        $response->assertViewHas('keluarga');
        $response->assertViewHas('rtList');
        $response->assertViewHas('rwList');
    }

    public function test_keluarga_index_search_by_no_kk(): void
    {
        DataKeluarga::factory()->create([
            'no_kk'                => '7371012345670001',
            'nama_kepala_keluarga' => 'Keluarga Unik',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.keluarga.index', ['search' => '7371012345670001']));

        $response->assertStatus(200);
        $response->assertSee('Keluarga Unik');
    }

    public function test_keluarga_index_search_by_nama_kepala(): void
    {
        DataKeluarga::factory()->create([
            'nama_kepala_keluarga' => 'Pak Budi Istimewa',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.keluarga.index', ['search' => 'Pak Budi Istimewa']));

        $response->assertStatus(200);
        $response->assertSee('Pak Budi Istimewa');
    }

    public function test_keluarga_index_filter_by_rt(): void
    {
        DataKeluarga::factory()->create(['rt' => '001', 'nama_kepala_keluarga' => 'KK RT 1']);
        DataKeluarga::factory()->create(['rt' => '002', 'nama_kepala_keluarga' => 'KK RT 2']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.keluarga.index', ['rt' => '001']));

        $response->assertStatus(200);
    }

    public function test_keluarga_index_filter_by_rw(): void
    {
        DataKeluarga::factory()->create(['rw' => '001']);
        DataKeluarga::factory()->create(['rw' => '002']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.keluarga.index', ['rw' => '001']));

        $response->assertStatus(200);
    }

    // ─── CREATE ───────────────────────────────────────────────────

    public function test_admin_can_view_create_keluarga_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.keluarga.create'));

        $response->assertStatus(200);
    }

    // ─── STORE ────────────────────────────────────────────────────

    public function test_admin_can_store_new_keluarga(): void
    {
        $data = [
            'no_kk'                   => '7371012345670001',
            'nama_kepala_keluarga'    => 'Ahmad Budi',
            'nik_kepala_keluarga'     => '7371012345678901',
            'jumlah_anggota_keluarga' => 4,
            'alamat'                  => 'Jl. Batua Raya No. 1',
            'rt'                      => '001',
            'rw'                      => '001',
            'kecamatan'               => 'Manggala',
            'kelurahan'               => 'Batua',
            'status'                  => 'aktif',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.keluarga.store'), $data);

        $response->assertRedirect(route('admin.keluarga.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('data_keluarga', [
            'no_kk'                => '7371012345670001',
            'nama_kepala_keluarga' => 'Ahmad Budi',
        ]);
    }

    public function test_store_keluarga_validates_no_kk_required(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.keluarga.store'), [
            'nama_kepala_keluarga' => 'Test',
        ]);

        $response->assertSessionHasErrors('no_kk');
    }

    public function test_store_keluarga_validates_nama_kepala_required(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.keluarga.store'), [
            'no_kk' => '7371012345670001',
        ]);

        $response->assertSessionHasErrors('nama_kepala_keluarga');
    }

    public function test_store_keluarga_validates_no_kk_unique(): void
    {
        DataKeluarga::factory()->create(['no_kk' => '7371012345670001']);

        $response = $this->actingAs($this->admin)->post(route('admin.keluarga.store'), [
            'no_kk'                => '7371012345670001',
            'nama_kepala_keluarga' => 'Duplicate',
        ]);

        $response->assertSessionHasErrors('no_kk');
    }

    public function test_store_keluarga_sets_petugas_and_tgl_input(): void
    {
        $data = [
            'no_kk'                => '7371012345670001',
            'nama_kepala_keluarga' => 'Test Petugas',
        ];

        $this->actingAs($this->admin)->post(route('admin.keluarga.store'), $data);

        $keluarga = DataKeluarga::where('no_kk', '7371012345670001')->first();
        $this->assertNotNull($keluarga);
        $this->assertEquals($this->admin->id, $keluarga->petugas_input);
        $this->assertNotNull($keluarga->tgl_input);
    }

    // ─── SHOW ─────────────────────────────────────────────────────

    public function test_admin_can_view_keluarga_detail(): void
    {
        $keluarga = DataKeluarga::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('admin.keluarga.show', $keluarga));

        $response->assertStatus(200);
        $response->assertViewHas('keluarga');
    }

    public function test_keluarga_show_loads_penduduk_relation(): void
    {
        $keluarga = DataKeluarga::factory()->create();
        DataPenduduk::factory()->count(3)->create(['no_kk' => $keluarga->no_kk]);

        $response = $this->actingAs($this->admin)->get(route('admin.keluarga.show', $keluarga));

        $response->assertStatus(200);
        $kk = $response->viewData('keluarga');
        $this->assertTrue($kk->relationLoaded('penduduk'));
        $this->assertCount(3, $kk->penduduk);
    }

    // ─── EDIT ─────────────────────────────────────────────────────

    public function test_admin_can_view_edit_keluarga_form(): void
    {
        $keluarga = DataKeluarga::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('admin.keluarga.edit', $keluarga));

        $response->assertStatus(200);
        $response->assertViewHas('keluarga');
    }

    // ─── UPDATE ───────────────────────────────────────────────────

    public function test_admin_can_update_keluarga(): void
    {
        $keluarga = DataKeluarga::factory()->create();

        $response = $this->actingAs($this->admin)->put(route('admin.keluarga.update', $keluarga), [
            'no_kk'                => $keluarga->no_kk,
            'nama_kepala_keluarga' => 'Nama Updated',
        ]);

        $response->assertRedirect(route('admin.keluarga.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('data_keluarga', [
            'id'                   => $keluarga->id,
            'nama_kepala_keluarga' => 'Nama Updated',
        ]);
    }

    public function test_update_keluarga_allows_same_no_kk(): void
    {
        $keluarga = DataKeluarga::factory()->create(['no_kk' => '7371012345670001']);

        $response = $this->actingAs($this->admin)->put(route('admin.keluarga.update', $keluarga), [
            'no_kk'                => '7371012345670001',
            'nama_kepala_keluarga' => 'Same KK Updated',
        ]);

        $response->assertRedirect(route('admin.keluarga.index'));
        $response->assertSessionHas('success');
    }

    // ─── DESTROY ──────────────────────────────────────────────────

    public function test_admin_can_delete_keluarga(): void
    {
        $keluarga = DataKeluarga::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('admin.keluarga.destroy', $keluarga));

        $response->assertRedirect(route('admin.keluarga.index'));
        $response->assertSessionHas('success');
        $this->assertSoftDeleted('data_keluarga', ['id' => $keluarga->id]);
    }
}
