<?php

namespace Tests\Feature\Admin;

use App\Models\DataRtRw;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WilayahControllerTest extends TestCase
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

    public function test_admin_can_view_wilayah_index(): void
    {
        DataRtRw::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.wilayah.index'));

        $response->assertStatus(200);
        $response->assertViewHas('wilayah');
        $response->assertViewHas('rwList');
        $response->assertViewHas('totalRT');
        $response->assertViewHas('totalRW');
        $response->assertViewHas('totalAktif');
        $response->assertViewHas('totalNonaktif');
    }

    public function test_wilayah_index_shows_correct_stats(): void
    {
        DataRtRw::factory()->count(3)->create(['jabatan' => 'RT', 'status' => 'aktif']);
        DataRtRw::factory()->count(2)->create(['jabatan' => 'RW', 'status' => 'aktif']);
        DataRtRw::factory()->count(1)->create(['jabatan' => 'RT', 'status' => 'nonaktif']);

        $response = $this->actingAs($this->admin)->get(route('admin.wilayah.index'));

        $response->assertStatus(200);
        $response->assertViewHas('totalRT', 4);  // 3 aktif + 1 nonaktif
        $response->assertViewHas('totalRW', 2);
        $response->assertViewHas('totalAktif', 5); // 3 RT aktif + 2 RW aktif
        $response->assertViewHas('totalNonaktif', 1);
    }

    public function test_wilayah_index_search_by_nama(): void
    {
        DataRtRw::factory()->create(['nama' => 'Pak Usman Unik']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.wilayah.index', ['search' => 'Pak Usman Unik']));

        $response->assertStatus(200);
        $response->assertSee('Pak Usman Unik');
    }

    public function test_wilayah_index_filter_by_jabatan(): void
    {
        DataRtRw::factory()->create(['jabatan' => 'RT', 'nama' => 'Ketua RT']);
        DataRtRw::factory()->create(['jabatan' => 'RW', 'nama' => 'Ketua RW']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.wilayah.index', ['jabatan' => 'RT']));

        $response->assertStatus(200);
    }

    public function test_wilayah_index_filter_by_status(): void
    {
        DataRtRw::factory()->create(['status' => 'aktif']);
        DataRtRw::factory()->create(['status' => 'nonaktif']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.wilayah.index', ['status' => 'aktif']));

        $response->assertStatus(200);
    }

    public function test_wilayah_index_filter_by_rw(): void
    {
        DataRtRw::factory()->create(['rw' => '001']);
        DataRtRw::factory()->create(['rw' => '002']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.wilayah.index', ['rw' => '001']));

        $response->assertStatus(200);
    }

    // ─── CREATE ───────────────────────────────────────────────────

    public function test_admin_can_view_create_wilayah_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.wilayah.create'));

        $response->assertStatus(200);
    }

    // ─── STORE ────────────────────────────────────────────────────

    public function test_admin_can_store_new_wilayah(): void
    {
        $data = [
            'nama'      => 'Muhammad Ali',
            'jabatan'   => 'RT',
            'rt'        => '005',
            'rw'        => '002',
            'status'    => 'aktif',
            'kelurahan' => 'Batua',
            'kecamatan' => 'Manggala',
            'alamat'    => 'Jl. Batua Raya',
            'no_telp'   => '08123456789',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.wilayah.store'), $data);

        $response->assertRedirect(route('admin.wilayah.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('data_rt_rw', [
            'nama'    => 'Muhammad Ali',
            'jabatan' => 'RT',
            'rw'      => '002',
        ]);
    }

    public function test_store_wilayah_validates_nama_required(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.wilayah.store'), [
            'jabatan' => 'RT',
            'rw'      => '001',
            'status'  => 'aktif',
        ]);

        $response->assertSessionHasErrors('nama');
    }

    public function test_store_wilayah_validates_jabatan_in_rt_rw(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.wilayah.store'), [
            'nama'    => 'Test',
            'jabatan' => 'INVALID',
            'rw'      => '001',
            'status'  => 'aktif',
        ]);

        $response->assertSessionHasErrors('jabatan');
    }

    public function test_store_wilayah_validates_status_in_aktif_nonaktif(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.wilayah.store'), [
            'nama'    => 'Test',
            'jabatan' => 'RT',
            'rw'      => '001',
            'status'  => 'invalid_status',
        ]);

        $response->assertSessionHasErrors('status');
    }

    public function test_store_wilayah_validates_rw_required(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.wilayah.store'), [
            'nama'    => 'Test',
            'jabatan' => 'RT',
            'status'  => 'aktif',
        ]);

        $response->assertSessionHasErrors('rw');
    }

    public function test_store_wilayah_sets_petugas_and_tgl_input(): void
    {
        $data = [
            'nama'    => 'Test Petugas',
            'jabatan' => 'RT',
            'rw'      => '001',
            'status'  => 'aktif',
        ];

        $this->actingAs($this->admin)->post(route('admin.wilayah.store'), $data);

        $wilayah = DataRtRw::where('nama', 'Test Petugas')->first();
        $this->assertNotNull($wilayah);
        $this->assertEquals($this->admin->id, $wilayah->petugas_input);
        $this->assertNotNull($wilayah->tgl_input);
    }

    // ─── EDIT ─────────────────────────────────────────────────────

    public function test_admin_can_view_edit_wilayah_form(): void
    {
        $wilayah = DataRtRw::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('admin.wilayah.edit', $wilayah));

        $response->assertStatus(200);
        $response->assertViewHas('wilayah');
    }

    // ─── UPDATE ───────────────────────────────────────────────────

    public function test_admin_can_update_wilayah(): void
    {
        $wilayah = DataRtRw::factory()->create();

        $response = $this->actingAs($this->admin)->put(route('admin.wilayah.update', $wilayah), [
            'nama'    => 'Nama Updated',
            'jabatan' => 'RW',
            'rw'      => '003',
            'status'  => 'aktif',
        ]);

        $response->assertRedirect(route('admin.wilayah.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('data_rt_rw', [
            'id'   => $wilayah->id,
            'nama' => 'Nama Updated',
        ]);
    }

    // ─── DESTROY ──────────────────────────────────────────────────

    public function test_admin_can_delete_wilayah(): void
    {
        $wilayah = DataRtRw::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('admin.wilayah.destroy', $wilayah));

        $response->assertRedirect(route('admin.wilayah.index'));
        $response->assertSessionHas('success');
        $this->assertSoftDeleted('data_rt_rw', ['id' => $wilayah->id]);
    }
}
