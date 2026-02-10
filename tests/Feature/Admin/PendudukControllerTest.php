<?php

namespace Tests\Feature\Admin;

use App\Models\DataPenduduk;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PendudukControllerTest extends TestCase
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

    public function test_admin_can_view_penduduk_index(): void
    {
        DataPenduduk::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.penduduk.index'));

        $response->assertStatus(200);
        $response->assertViewHas('penduduk');
        $response->assertViewHas('rtList');
        $response->assertViewHas('rwList');
    }

    public function test_penduduk_index_search_by_nik(): void
    {
        DataPenduduk::factory()->create(['nik' => '7371012345678901', 'nama' => 'Ahmad Test']);

        $response = $this->actingAs($this->admin)->get(route('admin.penduduk.index', ['search' => '7371012345678901']));

        $response->assertStatus(200);
        $response->assertSee('Ahmad Test');
    }

    public function test_penduduk_index_search_by_nama(): void
    {
        DataPenduduk::factory()->create(['nama' => 'Siti Rahma Unik']);

        $response = $this->actingAs($this->admin)->get(route('admin.penduduk.index', ['search' => 'Siti Rahma Unik']));

        $response->assertStatus(200);
        $response->assertSee('Siti Rahma Unik');
    }

    public function test_penduduk_index_filter_by_rt(): void
    {
        DataPenduduk::factory()->create(['rt' => '001', 'nama' => 'Warga RT 1']);
        DataPenduduk::factory()->create(['rt' => '002', 'nama' => 'Warga RT 2']);

        $response = $this->actingAs($this->admin)->get(route('admin.penduduk.index', ['rt' => '001']));

        $response->assertStatus(200);
        $response->assertSee('Warga RT 1');
    }

    public function test_penduduk_index_filter_by_jenis_kelamin(): void
    {
        DataPenduduk::factory()->create(['jenis_kelamin' => 'L', 'nama' => 'Pria Test']);
        DataPenduduk::factory()->create(['jenis_kelamin' => 'P', 'nama' => 'Wanita Test']);

        $response = $this->actingAs($this->admin)->get(route('admin.penduduk.index', ['jenis_kelamin' => 'L']));

        $response->assertStatus(200);
    }

    // ─── CREATE ───────────────────────────────────────────────────

    public function test_admin_can_view_create_penduduk_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.penduduk.create'));

        $response->assertStatus(200);
    }

    // ─── STORE ────────────────────────────────────────────────────

    public function test_admin_can_store_new_penduduk(): void
    {
        $data = [
            'nik'            => '7371012345678901',
            'nama'           => 'Ahmad Budi',
            'jenis_kelamin'  => 'L',
            'tempat_lahir'   => 'Makassar',
            'tanggal_lahir'  => '1990-05-15',
            'agama'          => 'Islam',
            'status_kawin'   => 'Kawin',
            'pekerjaan'      => 'PNS',
            'alamat'         => 'Jl. Batua Raya No. 1',
            'rt'             => '001',
            'rw'             => '001',
            'kelurahan'      => 'Batua',
            'kecamatan'      => 'Manggala',
            'status_data'    => 'aktif',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.penduduk.store'), $data);

        $response->assertRedirect(route('admin.penduduk.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('data_penduduk', [
            'nik'  => '7371012345678901',
            'nama' => 'Ahmad Budi',
        ]);
    }

    public function test_store_penduduk_validates_nik_required(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.penduduk.store'), [
            'nama'          => 'Test',
            'jenis_kelamin' => 'L',
        ]);

        $response->assertSessionHasErrors('nik');
    }

    public function test_store_penduduk_validates_nik_size_16(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.penduduk.store'), [
            'nik'           => '12345',
            'nama'          => 'Test',
            'jenis_kelamin' => 'L',
        ]);

        $response->assertSessionHasErrors('nik');
    }

    public function test_store_penduduk_validates_nik_unique(): void
    {
        DataPenduduk::factory()->create(['nik' => '7371012345678901']);

        $response = $this->actingAs($this->admin)->post(route('admin.penduduk.store'), [
            'nik'           => '7371012345678901',
            'nama'          => 'Duplicate',
            'jenis_kelamin' => 'L',
        ]);

        $response->assertSessionHasErrors('nik');
    }

    public function test_store_sets_petugas_input_and_tgl_input(): void
    {
        $data = [
            'nik'           => '7371012345678901',
            'nama'          => 'Test Petugas',
            'jenis_kelamin' => 'L',
        ];

        $this->actingAs($this->admin)->post(route('admin.penduduk.store'), $data);

        $penduduk = DataPenduduk::where('nik', '7371012345678901')->first();
        $this->assertNotNull($penduduk);
        $this->assertEquals($this->admin->id, $penduduk->petugas_input);
        $this->assertNotNull($penduduk->tgl_input);
    }

    // ─── SHOW ─────────────────────────────────────────────────────

    public function test_admin_can_view_penduduk_detail(): void
    {
        $penduduk = DataPenduduk::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('admin.penduduk.show', $penduduk));

        $response->assertStatus(200);
        $response->assertViewHas('penduduk');
    }

    // ─── EDIT ─────────────────────────────────────────────────────

    public function test_admin_can_view_edit_penduduk_form(): void
    {
        $penduduk = DataPenduduk::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('admin.penduduk.edit', $penduduk));

        $response->assertStatus(200);
        $response->assertViewHas('penduduk');
    }

    // ─── UPDATE ───────────────────────────────────────────────────

    public function test_admin_can_update_penduduk(): void
    {
        $penduduk = DataPenduduk::factory()->create();

        $response = $this->actingAs($this->admin)->put(route('admin.penduduk.update', $penduduk), [
            'nik'           => $penduduk->nik,
            'nama'          => 'Nama Updated',
            'jenis_kelamin' => 'P',
        ]);

        $response->assertRedirect(route('admin.penduduk.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('data_penduduk', [
            'id'   => $penduduk->id,
            'nama' => 'Nama Updated',
        ]);
    }

    // ─── DESTROY ──────────────────────────────────────────────────

    public function test_admin_can_delete_penduduk(): void
    {
        $penduduk = DataPenduduk::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('admin.penduduk.destroy', $penduduk));

        $response->assertRedirect(route('admin.penduduk.index'));
        $response->assertSessionHas('success');
        $this->assertSoftDeleted('data_penduduk', ['id' => $penduduk->id]);
    }
}
