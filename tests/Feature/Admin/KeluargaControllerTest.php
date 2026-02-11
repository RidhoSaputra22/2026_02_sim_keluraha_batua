<?php

namespace Tests\Feature\Admin;

use App\Models\Keluarga;
use App\Models\Penduduk;
use App\Models\Role;
use App\Models\Rt;
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
        Keluarga::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.keluarga.index'));

        $response->assertStatus(200);
        $response->assertViewHas('keluarga');
        $response->assertViewHas('rtList');
        $response->assertViewHas('rwList');
    }

    public function test_keluarga_index_search_by_no_kk(): void
    {
        $keluarga = Keluarga::factory()->create(['no_kk' => '7371012345670001']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.keluarga.index', ['search' => '7371012345670001']));

        $response->assertStatus(200);
    }

    public function test_keluarga_index_search_by_nama_kepala(): void
    {
        $penduduk = Penduduk::factory()->create(['nama' => 'Pak Budi Istimewa']);
        Keluarga::factory()->create(['kepala_keluarga_id' => $penduduk->id]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.keluarga.index', ['search' => 'Pak Budi Istimewa']));

        $response->assertStatus(200);
    }

    public function test_keluarga_index_filter_by_rt(): void
    {
        $rt = Rt::factory()->create();
        Keluarga::factory()->create(['rt_id' => $rt->id]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.keluarga.index', ['rt' => $rt->id]));

        $response->assertStatus(200);
    }

    public function test_keluarga_index_filter_by_rw(): void
    {
        $keluarga = Keluarga::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.keluarga.index', ['rw' => $keluarga->rt->rw_id]));

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
        $rt = Rt::factory()->create();
        $penduduk = Penduduk::factory()->create();

        $data = [
            'no_kk'                   => '7371012345670001',
            'kepala_keluarga_id'      => $penduduk->id,
            'jumlah_anggota_keluarga' => 4,
            'rt_id'                   => $rt->id,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.keluarga.store'), $data);

        $response->assertRedirect(route('admin.keluarga.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('keluargas', [
            'no_kk'              => '7371012345670001',
            'kepala_keluarga_id' => $penduduk->id,
        ]);
    }

    public function test_store_keluarga_validates_no_kk_required(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.keluarga.store'), [
            'kepala_keluarga_id' => 1,
        ]);

        $response->assertSessionHasErrors('no_kk');
    }

    public function test_store_keluarga_validates_no_kk_unique(): void
    {
        Keluarga::factory()->create(['no_kk' => '7371012345670001']);

        $response = $this->actingAs($this->admin)->post(route('admin.keluarga.store'), [
            'no_kk'              => '7371012345670001',
            'kepala_keluarga_id' => 1,
        ]);

        $response->assertSessionHasErrors('no_kk');
    }

    public function test_store_keluarga_sets_petugas_and_tgl_input(): void
    {
        $rt = Rt::factory()->create();
        $penduduk = Penduduk::factory()->create();

        $data = [
            'no_kk'              => '7371012345670001',
            'kepala_keluarga_id' => $penduduk->id,
            'rt_id'              => $rt->id,
        ];

        $this->actingAs($this->admin)->post(route('admin.keluarga.store'), $data);

        $keluarga = Keluarga::where('no_kk', '7371012345670001')->first();
        $this->assertNotNull($keluarga);
        $this->assertEquals($this->admin->id, $keluarga->petugas_input_id);
        $this->assertNotNull($keluarga->tgl_input);
    }

    // ─── SHOW ─────────────────────────────────────────────────────

    public function test_admin_can_view_keluarga_detail(): void
    {
        $keluarga = Keluarga::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('admin.keluarga.show', $keluarga));

        $response->assertStatus(200);
        $response->assertViewHas('keluarga');
    }

    public function test_keluarga_show_loads_anggota_relation(): void
    {
        $keluarga = Keluarga::factory()->create();
        Penduduk::factory()->count(3)->create(['keluarga_id' => $keluarga->id]);

        $response = $this->actingAs($this->admin)->get(route('admin.keluarga.show', $keluarga));

        $response->assertStatus(200);
    }

    // ─── EDIT ─────────────────────────────────────────────────────

    public function test_admin_can_view_edit_keluarga_form(): void
    {
        $keluarga = Keluarga::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('admin.keluarga.edit', $keluarga));

        $response->assertStatus(200);
        $response->assertViewHas('keluarga');
    }

    // ─── UPDATE ───────────────────────────────────────────────────

    public function test_admin_can_update_keluarga(): void
    {
        $keluarga = Keluarga::factory()->create();
        $newPenduduk = Penduduk::factory()->create();

        $response = $this->actingAs($this->admin)->put(route('admin.keluarga.update', $keluarga), [
            'no_kk'              => $keluarga->no_kk,
            'kepala_keluarga_id' => $newPenduduk->id,
        ]);

        $response->assertRedirect(route('admin.keluarga.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('keluargas', [
            'id'                 => $keluarga->id,
            'kepala_keluarga_id' => $newPenduduk->id,
        ]);
    }

    public function test_update_keluarga_allows_same_no_kk(): void
    {
        $keluarga = Keluarga::factory()->create(['no_kk' => '7371012345670001']);

        $response = $this->actingAs($this->admin)->put(route('admin.keluarga.update', $keluarga), [
            'no_kk' => '7371012345670001',
        ]);

        $response->assertRedirect(route('admin.keluarga.index'));
        $response->assertSessionHas('success');
    }

    // ─── DESTROY ──────────────────────────────────────────────────

    public function test_admin_can_delete_keluarga(): void
    {
        $keluarga = Keluarga::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('admin.keluarga.destroy', $keluarga));

        $response->assertRedirect(route('admin.keluarga.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('keluargas', ['id' => $keluarga->id]);
    }
}
