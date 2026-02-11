<?php

namespace Tests\Feature\Admin;

use App\Models\PegawaiStaff;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PegawaiControllerTest extends TestCase
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

    public function test_admin_can_view_pegawai_index(): void
    {
        PegawaiStaff::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.pegawai.index'));

        $response->assertStatus(200);
        $response->assertViewHas('pegawai');
    }

    public function test_pegawai_index_search_by_nama(): void
    {
        PegawaiStaff::factory()->create(['nama' => 'Pak Dosen Unik']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.pegawai.index', ['search' => 'Pak Dosen Unik']));

        $response->assertStatus(200);
        $response->assertSee('Pak Dosen Unik');
    }

    public function test_pegawai_index_search_by_nip(): void
    {
        PegawaiStaff::factory()->create([
            'nip'  => '199901012022011001',
            'nama' => 'Pegawai NIP Test',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.pegawai.index', ['search' => '199901012022011001']));

        $response->assertStatus(200);
        $response->assertSee('Pegawai NIP Test');
    }

    public function test_pegawai_index_filter_by_status_pegawai(): void
    {
        PegawaiStaff::factory()->create(['status_pegawai' => 'aktif', 'nama' => 'Aktif Staff']);
        PegawaiStaff::factory()->create(['status_pegawai' => 'nonaktif', 'nama' => 'Nonaktif Staff']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.pegawai.index', ['status_pegawai' => 'aktif']));

        $response->assertStatus(200);
    }

    public function test_pegawai_index_ordered_by_no_urut(): void
    {
        PegawaiStaff::factory()->create(['no_urut' => 3, 'nama' => 'Pegawai C']);
        PegawaiStaff::factory()->create(['no_urut' => 1, 'nama' => 'Pegawai A']);
        PegawaiStaff::factory()->create(['no_urut' => 2, 'nama' => 'Pegawai B']);

        $response = $this->actingAs($this->admin)->get(route('admin.pegawai.index'));

        $response->assertStatus(200);
        $pegawai = $response->viewData('pegawai');
        $this->assertEquals(1, $pegawai->first()->no_urut);
    }

    // ─── CREATE ───────────────────────────────────────────────────

    public function test_admin_can_view_create_pegawai_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.pegawai.create'));

        $response->assertStatus(200);
    }

    // ─── STORE ────────────────────────────────────────────────────

    public function test_admin_can_store_new_pegawai(): void
    {
        $data = [
            'nip'            => '199901012022011001',
            'nama'           => 'Andi Pegawai',
            'jabatan'        => 'Kasi Pelayanan',
            'gol'            => 'III/b',
            'pangkat'        => 'Penata Muda',
            'status_pegawai' => 'aktif',
            'no_urut'        => 5,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.pegawai.store'), $data);

        $response->assertRedirect(route('admin.pegawai.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('pegawai_staff', [
            'nama'    => 'Andi Pegawai',
            'jabatan' => 'Kasi Pelayanan',
            'nip'     => '199901012022011001',
        ]);
    }

    public function test_store_pegawai_validates_nama_required(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.pegawai.store'), [
            'nip'            => '199901012022011099',
            'jabatan'        => 'Staff',
            'status_pegawai' => 'aktif',
        ]);

        $response->assertSessionHasErrors('nama');
    }

    public function test_store_pegawai_validates_jabatan_required(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.pegawai.store'), [
            'nip'            => '199901012022011099',
            'nama'           => 'Test',
            'status_pegawai' => 'aktif',
        ]);

        $response->assertSessionHasErrors('jabatan');
    }

    public function test_store_pegawai_validates_status_in_valid_values(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.pegawai.store'), [
            'nip'            => '199901012022011099',
            'nama'           => 'Test',
            'jabatan'        => 'Staff',
            'status_pegawai' => 'invalid',
        ]);

        $response->assertSessionHasErrors('status_pegawai');
    }

    public function test_store_pegawai_validates_nip_required(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.pegawai.store'), [
            'nama'           => 'Test',
            'jabatan'        => 'Staff',
            'status_pegawai' => 'aktif',
        ]);

        $response->assertSessionHasErrors('nip');
    }

    public function test_store_pegawai_sets_petugas_and_tgl_input(): void
    {
        $data = [
            'nip'            => '199901019999011099',
            'nama'           => 'Test Petugas',
            'jabatan'        => 'Staff',
            'status_pegawai' => 'aktif',
        ];

        $this->actingAs($this->admin)->post(route('admin.pegawai.store'), $data);

        $pegawai = PegawaiStaff::where('nama', 'Test Petugas')->first();
        $this->assertNotNull($pegawai);
        $this->assertEquals($this->admin->id, $pegawai->petugas_input_id);
        $this->assertNotNull($pegawai->tgl_input);
    }

    // ─── EDIT ─────────────────────────────────────────────────────

    public function test_admin_can_view_edit_pegawai_form(): void
    {
        $pegawai = PegawaiStaff::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('admin.pegawai.edit', $pegawai));

        $response->assertStatus(200);
        $response->assertViewHas('pegawai');
    }

    // ─── UPDATE ───────────────────────────────────────────────────

    public function test_admin_can_update_pegawai(): void
    {
        $pegawai = PegawaiStaff::factory()->create();

        $response = $this->actingAs($this->admin)->put(route('admin.pegawai.update', $pegawai), [
            'nip'            => $pegawai->nip,
            'nama'           => 'Nama Updated',
            'jabatan'        => 'Sekretaris Lurah',
            'status_pegawai' => 'nonaktif',
        ]);

        $response->assertRedirect(route('admin.pegawai.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('pegawai_staff', [
            'id'             => $pegawai->id,
            'nama'           => 'Nama Updated',
            'status_pegawai' => 'nonaktif',
        ]);
    }

    // ─── DESTROY ──────────────────────────────────────────────────

    public function test_admin_can_delete_pegawai(): void
    {
        $pegawai = PegawaiStaff::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('admin.pegawai.destroy', $pegawai));

        $response->assertRedirect(route('admin.pegawai.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('pegawai_staff', ['id' => $pegawai->id]);
    }
}
