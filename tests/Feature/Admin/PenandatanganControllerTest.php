<?php

namespace Tests\Feature\Admin;

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
        Penandatanganan::factory()->create(['nama' => 'Pak Lurah Istimewa']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.penandatangan.index', ['search' => 'Pak Lurah Istimewa']));

        $response->assertStatus(200);
        $response->assertSee('Pak Lurah Istimewa');
    }

    public function test_penandatangan_index_search_by_nip(): void
    {
        \App\Models\PegawaiStaff::factory()->create(['nip' => '199001012020011001']);
        Penandatanganan::factory()->create([
            'nip'  => '199001012020011001',
            'nama' => 'Pejabat NIP',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.penandatangan.index', ['search' => '199001012020011001']));

        $response->assertStatus(200);
        $response->assertSee('Pejabat NIP');
    }

    public function test_penandatangan_index_filter_by_status(): void
    {
        Penandatanganan::factory()->create(['status' => 'aktif', 'nama' => 'Aktif Pejabat']);
        Penandatanganan::factory()->create(['status' => 'nonaktif', 'nama' => 'Nonaktif Pejabat']);

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
        \App\Models\PegawaiStaff::factory()->create(['nip' => '199001012020011001']);

        $data = [
            'nama'     => 'H. Ahmad, S.Sos, M.Si',
            'jabatan'  => 'Lurah',
            'nip'      => '199001012020011001',
            'pangkat'  => 'Penata Tk. I',
            'golongan' => 'III/d',
            'status'   => 'aktif',
            'no_telp'  => '08123456789',
            'email'    => 'lurah@batua.com',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.penandatangan.store'), $data);

        $response->assertRedirect(route('admin.penandatangan.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('penandatanganan', [
            'nama'    => 'H. Ahmad, S.Sos, M.Si',
            'jabatan' => 'Lurah',
        ]);
    }

    public function test_store_penandatangan_validates_nama_required(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.penandatangan.store'), [
            'jabatan' => 'Lurah',
            'status'  => 'aktif',
        ]);

        $response->assertSessionHasErrors('nama');
    }

    public function test_store_penandatangan_validates_jabatan_required(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.penandatangan.store'), [
            'nama'   => 'Test',
            'status' => 'aktif',
        ]);

        $response->assertSessionHasErrors('jabatan');
    }

    public function test_store_penandatangan_validates_status_in_valid_values(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.penandatangan.store'), [
            'nama'    => 'Test',
            'jabatan' => 'Lurah',
            'status'  => 'invalid',
        ]);

        $response->assertSessionHasErrors('status');
    }

    public function test_store_penandatangan_validates_email_format(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.penandatangan.store'), [
            'nama'    => 'Test',
            'jabatan' => 'Lurah',
            'status'  => 'aktif',
            'email'   => 'not-an-email',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_store_penandatangan_sets_petugas_and_tgl_input(): void
    {
        $data = [
            'nama'    => 'Test Petugas',
            'jabatan' => 'Sekretaris Lurah',
            'status'  => 'aktif',
        ];

        $this->actingAs($this->admin)->post(route('admin.penandatangan.store'), $data);

        $penandatangan = Penandatanganan::where('nama', 'Test Petugas')->first();
        $this->assertNotNull($penandatangan);
        $this->assertEquals($this->admin->id, $penandatangan->petugas_input);
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

        $response = $this->actingAs($this->admin)->put(route('admin.penandatangan.update', $penandatangan), [
            'nama'    => 'Nama Updated',
            'jabatan' => 'Kasi Pemerintahan',
            'status'  => 'nonaktif',
        ]);

        $response->assertRedirect(route('admin.penandatangan.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('penandatanganan', [
            'id'     => $penandatangan->id,
            'nama'   => 'Nama Updated',
            'status' => 'nonaktif',
        ]);
    }

    // ─── DESTROY ──────────────────────────────────────────────────

    public function test_admin_can_delete_penandatangan(): void
    {
        $penandatangan = Penandatanganan::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('admin.penandatangan.destroy', $penandatangan));

        $response->assertRedirect(route('admin.penandatangan.index'));
        $response->assertSessionHas('success');
        $this->assertSoftDeleted('penandatanganan', ['id' => $penandatangan->id]);
    }
}
