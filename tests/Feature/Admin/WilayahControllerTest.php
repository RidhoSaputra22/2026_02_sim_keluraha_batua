<?php

namespace Tests\Feature\Admin;

use App\Models\JabatanRtRw;
use App\Models\Kelurahan;
use App\Models\Penduduk;
use App\Models\Role;
use App\Models\Rt;
use App\Models\RtRwPengurus;
use App\Models\Rw;
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

    // ─── INDEX ────────────────────────────────────────────────────

    public function test_admin_can_view_wilayah_index(): void
    {
        RtRwPengurus::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('master.wilayah.index'));

        $response->assertStatus(200);
        $response->assertViewHas('wilayah');
        $response->assertViewHas('rwList');
        $response->assertViewHas('jabatanList');
        $response->assertViewHas('totalRT');
        $response->assertViewHas('totalRW');
        $response->assertViewHas('totalAktif');
        $response->assertViewHas('totalNonaktif');
    }

    public function test_wilayah_index_search_by_nama(): void
    {
        $penduduk = Penduduk::factory()->create(['nama' => 'Pak Usman Unik']);
        RtRwPengurus::factory()->create(['penduduk_id' => $penduduk->id]);

        $response = $this->actingAs($this->admin)
            ->get(route('master.wilayah.index', ['search' => 'Pak Usman Unik']));

        $response->assertStatus(200);
    }

    public function test_wilayah_index_filter_by_status(): void
    {
        RtRwPengurus::factory()->create(['status' => 'aktif']);
        RtRwPengurus::factory()->create(['status' => 'nonaktif']);

        $response = $this->actingAs($this->admin)
            ->get(route('master.wilayah.index', ['status' => 'aktif']));

        $response->assertStatus(200);
    }

    public function test_wilayah_index_filter_by_rw(): void
    {
        $rw = Rw::factory()->create();
        RtRwPengurus::factory()->create(['rw_id' => $rw->id]);

        $response = $this->actingAs($this->admin)
            ->get(route('master.wilayah.index', ['rw' => $rw->id]));

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
        $penduduk = Penduduk::factory()->create();
        $kelurahan = Kelurahan::factory()->create();
        $jabatan = JabatanRtRw::factory()->create();
        $rw = Rw::factory()->create();
        $rt = Rt::factory()->create();

        $data = [
            'penduduk_id' => $penduduk->id,
            'kelurahan_id' => $kelurahan->id,
            'jabatan_id' => $jabatan->id,
            'rw_id' => $rw->id,
            'rt_id' => $rt->id,
            'status' => 'aktif',
            'alamat' => 'Jl. Batua Raya',
            'no_telp' => '08123456789',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.wilayah.store'), $data);

        $response->assertRedirect(route('master.wilayah.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('rt_rw_pengurus', [
            'penduduk_id' => $penduduk->id,
            'jabatan_id' => $jabatan->id,
        ]);
    }

    public function test_store_wilayah_validates_penduduk_id_required(): void
    {
        $jabatan = JabatanRtRw::factory()->create();

        $response = $this->actingAs($this->admin)->post(route('admin.wilayah.store'), [
            'jabatan_id' => $jabatan->id,
            'status' => 'aktif',
        ]);

        $response->assertSessionHasErrors('penduduk_id');
    }

    public function test_store_wilayah_validates_status_in_aktif_nonaktif(): void
    {
        $penduduk = Penduduk::factory()->create();
        $jabatan = JabatanRtRw::factory()->create();

        $response = $this->actingAs($this->admin)->post(route('admin.wilayah.store'), [
            'penduduk_id' => $penduduk->id,
            'jabatan_id' => $jabatan->id,
            'status' => 'invalid_status',
        ]);

        $response->assertSessionHasErrors('status');
    }

    // ─── EDIT ─────────────────────────────────────────────────────

    public function test_admin_can_view_edit_wilayah_form(): void
    {
        $wilayah = RtRwPengurus::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('admin.wilayah.edit', $wilayah));

        $response->assertStatus(200);
        $response->assertViewHas('wilayah');
    }

    // ─── UPDATE ───────────────────────────────────────────────────

    public function test_admin_can_update_wilayah(): void
    {
        $wilayah = RtRwPengurus::factory()->create();
        $newJabatan = JabatanRtRw::factory()->create();

        $response = $this->actingAs($this->admin)->put(route('admin.wilayah.update', $wilayah), [
            'penduduk_id' => $wilayah->penduduk_id,
            'kelurahan_id' => $wilayah->kelurahan_id,
            'jabatan_id' => $newJabatan->id,
            'status' => 'aktif',
        ]);

        $response->assertRedirect(route('master.wilayah.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('rt_rw_pengurus', [
            'id' => $wilayah->id,
            'jabatan_id' => $newJabatan->id,
        ]);
    }

    // ─── DESTROY ──────────────────────────────────────────────────

    public function test_admin_can_delete_wilayah(): void
    {
        $wilayah = RtRwPengurus::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('admin.wilayah.destroy', $wilayah));

        $response->assertRedirect(route('master.wilayah.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('rt_rw_pengurus', ['id' => $wilayah->id]);
    }
}
