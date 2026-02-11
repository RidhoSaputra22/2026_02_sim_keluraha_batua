<?php

namespace Tests\Feature\Admin;

use App\Models\PegawaiStaff;
use App\Models\Penandatanganan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenandatanganControllerTest extends TestCase
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

    public function test_admin_can_view_penandatangan_index(): void
    {
        Penandatanganan::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.penandatangan.index'));

        $response->assertStatus(200);
        $response->assertViewHas('penandatangan');
    }

    public function test_penandatangan_index_search_by_nama(): void
    {
        $pegawai = PegawaiStaff::factory()->create(['nama' => 'Pak Lurah Istimewa']);
        Penandatanganan::factory()->create(['pegawai_id' => $pegawai->id]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.penandatangan.index', ['search' => 'Pak Lurah Istimewa']));

        $response->assertStatus(200);
    }

    public function test_penandatangan_index_search_by_nip(): void
    {
        $pegawai = PegawaiStaff::factory()->create(['nip' => '199001012020011001']);
        Penandatanganan::factory()->create(['pegawai_id' => $pegawai->id]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.penandatangan.index', ['search' => '199001012020011001']));

        $response->assertStatus(200);
    }

    public function test_penandatangan_index_filter_by_status(): void
    {
        Penandatanganan::factory()->create(['status' => 'aktif']);
        Penandatanganan::factory()->create(['status' => 'nonaktif']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.penandatangan.index', ['status' => 'aktif']));

        $response->assertStatus(200);
    }

    // ─── CREATE ───────────────────────────────────────────────────

    public function test_admin_can_view_create_penandatangan_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.penandatangan.create'));

        $response->assertStatus(200);
    }

    // ─── STORE ────────────────────────────────────────────────────

    public function test_admin_can_store_new_penandatangan(): void
    {
        $pegawai = PegawaiStaff::factory()->create();

        $data = [
            'pegawai_id' => $pegawai->id,
            'status'     => 'aktif',
            'no_telp'    => '08123456789',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.penandatangan.store'), $data);

        $response->assertRedirect(route('admin.penandatangan.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('penandatanganans', [
            'pegawai_id' => $pegawai->id,
            'status'     => 'aktif',
        ]);
    }

    public function test_store_penandatangan_validates_pegawai_id_required(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.penandatangan.store'), [
            'status' => 'aktif',
        ]);

        $response->assertSessionHasErrors('pegawai_id');
    }

    public function test_store_penandatangan_validates_status_in_valid_values(): void
    {
        $pegawai = PegawaiStaff::factory()->create();

        $response = $this->actingAs($this->admin)->post(route('admin.penandatangan.store'), [
            'pegawai_id' => $pegawai->id,
            'status'     => 'invalid',
        ]);

        $response->assertSessionHasErrors('status');
    }

    public function test_store_penandatangan_sets_petugas_and_tgl_input(): void
    {
        $pegawai = PegawaiStaff::factory()->create();

        $data = [
            'pegawai_id' => $pegawai->id,
            'status'     => 'aktif',
        ];

        $this->actingAs($this->admin)->post(route('admin.penandatangan.store'), $data);

        $penandatangan = Penandatanganan::where('pegawai_id', $pegawai->id)->first();
        $this->assertNotNull($penandatangan);
        $this->assertEquals($this->admin->id, $penandatangan->petugas_input_id);
        $this->assertNotNull($penandatangan->tgl_input);
    }

    // ─── EDIT ─────────────────────────────────────────────────────

    public function test_admin_can_view_edit_penandatangan_form(): void
    {
        $penandatangan = Penandatanganan::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('admin.penandatangan.edit', $penandatangan));

        $response->assertStatus(200);
        $response->assertViewHas('penandatangan');
    }

    // ─── UPDATE ───────────────────────────────────────────────────

    public function test_admin_can_update_penandatangan(): void
    {
        $penandatangan = Penandatanganan::factory()->create();
        $newPegawai    = PegawaiStaff::factory()->create();

        $response = $this->actingAs($this->admin)->put(route('admin.penandatangan.update', $penandatangan), [
            'pegawai_id' => $newPegawai->id,
            'status'     => 'nonaktif',
        ]);

        $response->assertRedirect(route('admin.penandatangan.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('penandatanganans', [
            'id'         => $penandatangan->id,
            'pegawai_id' => $newPegawai->id,
            'status'     => 'nonaktif',
        ]);
    }

    // ─── DESTROY ──────────────────────────────────────────────────

    public function test_admin_can_delete_penandatangan(): void
    {
        $penandatangan = Penandatanganan::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('admin.penandatangan.destroy', $penandatangan));

        $response->assertRedirect(route('admin.penandatangan.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('penandatanganans', ['id' => $penandatangan->id]);
    }
}
