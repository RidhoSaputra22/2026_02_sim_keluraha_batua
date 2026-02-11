<?php

namespace Tests\Feature\Admin;

use App\Models\Keluarga;
use App\Models\Penduduk;
use App\Models\Role;
use App\Models\Rt;
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
        Penduduk::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.penduduk.index'));

        $response->assertStatus(200);
        $response->assertViewHas('penduduk');
        $response->assertViewHas('rtList');
        $response->assertViewHas('rwList');
    }

    public function test_penduduk_index_search_by_nik(): void
    {
        Penduduk::factory()->create(['nik' => '7371012345678901', 'nama' => 'Ahmad Test']);

        $response = $this->actingAs($this->admin)->get(route('admin.penduduk.index', ['search' => '7371012345678901']));

        $response->assertStatus(200);
        $response->assertSee('Ahmad Test');
    }

    public function test_penduduk_index_search_by_nama(): void
    {
        Penduduk::factory()->create(['nama' => 'Siti Rahma Unik']);

        $response = $this->actingAs($this->admin)->get(route('admin.penduduk.index', ['search' => 'Siti Rahma Unik']));

        $response->assertStatus(200);
        $response->assertSee('Siti Rahma Unik');
    }

    public function test_penduduk_index_filter_by_rt(): void
    {
        $rt = Rt::factory()->create();
        Penduduk::factory()->create(['rt_id' => $rt->id, 'nama' => 'Warga RT Filter']);

        $response = $this->actingAs($this->admin)->get(route('admin.penduduk.index', ['rt' => $rt->id]));

        $response->assertStatus(200);
    }

    public function test_penduduk_index_filter_by_jenis_kelamin(): void
    {
        Penduduk::factory()->create(['jenis_kelamin' => 'L', 'nama' => 'Pria Test']);
        Penduduk::factory()->create(['jenis_kelamin' => 'P', 'nama' => 'Wanita Test']);

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
        $rt = Rt::factory()->create();

        $data = [
            'nik'            => '7371012345678901',
            'nama'           => 'Ahmad Budi',
            'jenis_kelamin'  => 'L',
            'agama'          => 'Islam',
            'status_kawin'   => 'Kawin',
            'alamat'         => 'Jl. Batua Raya No. 1',
            'rt_id'          => $rt->id,
            'status_data'    => 'aktif',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.penduduk.store'), $data);

        $response->assertRedirect(route('admin.penduduk.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('penduduks', [
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

    public function test_store_penduduk_validates_nik_max_length(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.penduduk.store'), [
            'nik'           => str_repeat('1', 33),
            'nama'          => 'Test',
            'jenis_kelamin' => 'L',
        ]);

        $response->assertSessionHasErrors('nik');
    }

    public function test_store_penduduk_validates_nik_unique(): void
    {
        Penduduk::factory()->create(['nik' => '7371012345678901']);

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

        $penduduk = Penduduk::where('nik', '7371012345678901')->first();
        $this->assertNotNull($penduduk);
        $this->assertEquals($this->admin->id, $penduduk->petugas_input_id);
        $this->assertNotNull($penduduk->tgl_input);
    }

    // ─── SHOW ─────────────────────────────────────────────────────

    public function test_admin_can_view_penduduk_detail(): void
    {
        $penduduk = Penduduk::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('admin.penduduk.show', $penduduk));

        $response->assertStatus(200);
        $response->assertViewHas('penduduk');
    }

    // ─── EDIT ─────────────────────────────────────────────────────

    public function test_admin_can_view_edit_penduduk_form(): void
    {
        $penduduk = Penduduk::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('admin.penduduk.edit', $penduduk));

        $response->assertStatus(200);
        $response->assertViewHas('penduduk');
    }

    // ─── UPDATE ───────────────────────────────────────────────────

    public function test_admin_can_update_penduduk(): void
    {
        $penduduk = Penduduk::factory()->create();

        $response = $this->actingAs($this->admin)->put(route('admin.penduduk.update', $penduduk), [
            'nik'           => $penduduk->nik,
            'nama'          => 'Nama Updated',
            'jenis_kelamin' => 'P',
        ]);

        $response->assertRedirect(route('admin.penduduk.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('penduduks', [
            'id'   => $penduduk->id,
            'nama' => 'Nama Updated',
        ]);
    }

    // ─── DESTROY ──────────────────────────────────────────────────

    public function test_admin_can_delete_penduduk(): void
    {
        $penduduk = Penduduk::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('admin.penduduk.destroy', $penduduk));

        $response->assertRedirect(route('admin.penduduk.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('penduduks', ['id' => $penduduk->id]);
    }
}
