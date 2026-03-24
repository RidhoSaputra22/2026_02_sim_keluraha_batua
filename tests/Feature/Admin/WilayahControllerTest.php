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

    private function createRtRwRole(): Role
    {
        return Role::firstOrCreate(
            ['name' => Role::RT_RW],
            [
                'label' => 'Ketua RT/RW',
                'description' => 'Scoped RT/RW access',
                'permissions' => ['penduduk.view'],
                'is_active' => true,
            ]
        );
    }

    // ═══════════════════════════════════════════════════════════════
    // RT SHOW (includes pengurus section)
    // ═══════════════════════════════════════════════════════════════

    public function test_admin_can_view_rt_show_with_pengurus(): void
    {
        $rt = Rt::factory()->create();
        RtRwPengurus::factory()->create(['rt_id' => $rt->id, 'rw_id' => $rt->rw_id]);

        $response = $this->actingAs($this->admin)->get(route('master.rt.show', $rt));

        $response->assertStatus(200);
    }

    // ═══════════════════════════════════════════════════════════════
    // RT → PENGURUS STORE
    // ═══════════════════════════════════════════════════════════════

    public function test_admin_can_store_pengurus_for_rt(): void
    {
        $rt = Rt::factory()->create();
        $penduduk = Penduduk::factory()->create();
        $jabatan = JabatanRtRw::factory()->create();

        $data = [
            'penduduk_id' => $penduduk->id,
            'jabatan_id' => $jabatan->id,
            'status' => 'aktif',
            'alamat' => 'Jl. Batua Raya',
            'no_telp' => '08123456789',
        ];

        $response = $this->actingAs($this->admin)->post(route('master.rt.pengurus.store', $rt), $data);

        $response->assertRedirect(route('master.rt.show', $rt));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('rt_rw_pengurus', [
            'penduduk_id' => $penduduk->id,
            'jabatan_id' => $jabatan->id,
            'rt_id' => $rt->id,
        ]);
    }

    public function test_store_rt_pengurus_validates_penduduk_id_required(): void
    {
        $rt = Rt::factory()->create();
        $jabatan = JabatanRtRw::factory()->create();

        $response = $this->actingAs($this->admin)->post(route('master.rt.pengurus.store', $rt), [
            'jabatan_id' => $jabatan->id,
            'status' => 'aktif',
        ]);

        $response->assertSessionHasErrors('penduduk_id');
    }

    public function test_store_rt_pengurus_validates_status(): void
    {
        $rt = Rt::factory()->create();
        $penduduk = Penduduk::factory()->create();
        $jabatan = JabatanRtRw::factory()->create();

        $response = $this->actingAs($this->admin)->post(route('master.rt.pengurus.store', $rt), [
            'penduduk_id' => $penduduk->id,
            'jabatan_id' => $jabatan->id,
            'status' => 'invalid_status',
        ]);

        $response->assertSessionHasErrors('status');
    }

    // ═══════════════════════════════════════════════════════════════
    // RT → PENGURUS UPDATE
    // ═══════════════════════════════════════════════════════════════

    public function test_admin_can_update_pengurus_for_rt(): void
    {
        $rt = Rt::factory()->create();
        $pengurus = RtRwPengurus::factory()->create(['rt_id' => $rt->id, 'rw_id' => $rt->rw_id]);
        $newJabatan = JabatanRtRw::factory()->create();

        $response = $this->actingAs($this->admin)->put(
            route('master.rt.pengurus.update', [$rt, $pengurus]),
            [
                'penduduk_id' => $pengurus->penduduk_id,
                'jabatan_id' => $newJabatan->id,
                'status' => 'aktif',
            ]
        );

        $response->assertRedirect(route('master.rt.show', $rt));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('rt_rw_pengurus', [
            'id' => $pengurus->id,
            'jabatan_id' => $newJabatan->id,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════
    // RT → PENGURUS DESTROY
    // ═══════════════════════════════════════════════════════════════

    public function test_admin_can_delete_pengurus_for_rt(): void
    {
        $rt = Rt::factory()->create();
        $pengurus = RtRwPengurus::factory()->create(['rt_id' => $rt->id, 'rw_id' => $rt->rw_id]);

        $response = $this->actingAs($this->admin)->delete(
            route('master.rt.pengurus.destroy', [$rt, $pengurus])
        );

        $response->assertRedirect(route('master.rt.show', $rt));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('rt_rw_pengurus', ['id' => $pengurus->id]);
    }

    public function test_deleting_rt_pengurus_clears_linked_user_wilayah(): void
    {
        $rt = Rt::factory()->create();
        $rtRwRole = $this->createRtRwRole();
        $user = User::factory()->create([
            'role_id' => $rtRwRole->id,
            'wilayah_rw' => $rt->rw->nomor,
            'wilayah_rt' => $rt->nomor,
        ]);
        $pengurus = RtRwPengurus::factory()->create([
            'rt_id' => $rt->id,
            'rw_id' => $rt->rw_id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($this->admin)->delete(
            route('master.rt.pengurus.destroy', [$rt, $pengurus])
        );

        $response->assertRedirect(route('master.rt.show', $rt));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'wilayah_rw' => null,
            'wilayah_rt' => null,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════
    // RW SHOW (includes pengurus section)
    // ═══════════════════════════════════════════════════════════════

    public function test_admin_can_view_rw_show_with_pengurus(): void
    {
        $rw = Rw::factory()->create();
        RtRwPengurus::factory()->create(['rw_id' => $rw->id, 'rt_id' => null]);

        $response = $this->actingAs($this->admin)->get(route('master.rw.show', $rw));

        $response->assertStatus(200);
    }

    // ═══════════════════════════════════════════════════════════════
    // RW → PENGURUS STORE
    // ═══════════════════════════════════════════════════════════════

    public function test_admin_can_store_pengurus_for_rw(): void
    {
        $rw = Rw::factory()->create();
        $penduduk = Penduduk::factory()->create();
        $jabatan = JabatanRtRw::factory()->create();

        $data = [
            'penduduk_id' => $penduduk->id,
            'jabatan_id' => $jabatan->id,
            'status' => 'aktif',
            'alamat' => 'Jl. Batua Raya',
            'no_telp' => '08123456789',
        ];

        $response = $this->actingAs($this->admin)->post(route('master.rw.pengurus.store', $rw), $data);

        $response->assertRedirect(route('master.rw.show', $rw));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('rt_rw_pengurus', [
            'penduduk_id' => $penduduk->id,
            'jabatan_id' => $jabatan->id,
            'rw_id' => $rw->id,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════
    // RW → PENGURUS DESTROY
    // ═══════════════════════════════════════════════════════════════

    public function test_admin_can_delete_pengurus_for_rw(): void
    {
        $rw = Rw::factory()->create();
        $pengurus = RtRwPengurus::factory()->create(['rw_id' => $rw->id, 'rt_id' => null]);

        $response = $this->actingAs($this->admin)->delete(
            route('master.rw.pengurus.destroy', [$rw, $pengurus])
        );

        $response->assertRedirect(route('master.rw.show', $rw));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('rt_rw_pengurus', ['id' => $pengurus->id]);
    }

    public function test_updating_rw_pengurus_to_none_clears_previous_user_wilayah(): void
    {
        $rw = Rw::factory()->create();
        $rtRwRole = $this->createRtRwRole();
        $user = User::factory()->create([
            'role_id' => $rtRwRole->id,
            'wilayah_rw' => $rw->nomor,
            'wilayah_rt' => null,
        ]);
        $pengurus = RtRwPengurus::factory()->create([
            'rw_id' => $rw->id,
            'rt_id' => null,
            'user_id' => $user->id,
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($this->admin)->put(
            route('master.rw.pengurus.update', [$rw, $pengurus]),
            [
                'penduduk_id' => $pengurus->penduduk_id,
                'jabatan_id' => $pengurus->jabatan_id,
                'status' => 'aktif',
                'assign_user_mode' => 'none',
            ]
        );

        $response->assertRedirect(route('master.rw.show', $rw));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'wilayah_rw' => null,
            'wilayah_rt' => null,
        ]);
    }

    public function test_cannot_delete_rw_pengurus_from_another_rw(): void
    {
        $rw = Rw::factory()->create();
        $otherRw = Rw::factory()->create();
        $pengurus = RtRwPengurus::factory()->create([
            'rw_id' => $otherRw->id,
            'rt_id' => null,
        ]);

        $response = $this->actingAs($this->admin)->delete(
            route('master.rw.pengurus.destroy', [$rw, $pengurus])
        );

        $response->assertNotFound();
        $this->assertDatabaseHas('rt_rw_pengurus', ['id' => $pengurus->id]);
    }
}
