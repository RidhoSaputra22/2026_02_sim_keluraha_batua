<?php

namespace Tests\Feature\Admin;

use App\Models\DataKeluarga;
use App\Models\DataPenduduk;
use App\Models\DataRtRw;
use App\Models\PegawaiStaff;
use App\Models\Penandatanganan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Role $adminRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::create([
            'name'        => Role::ADMIN,
            'label'       => 'Admin Sistem',
            'description' => 'Full system access',
            'permissions' => ['*'],
            'is_active'   => true,
        ]);

        $this->admin = User::factory()->create([
            'role_id'   => $this->adminRole->id,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    public function test_dashboard_displays_correct_statistics(): void
    {
        // Seed some data
        DataPenduduk::factory()->count(5)->create();
        DataKeluarga::factory()->count(3)->create();
        DataRtRw::factory()->count(2)->create(['jabatan' => 'RT', 'status' => 'aktif']);
        DataRtRw::factory()->count(1)->create(['jabatan' => 'RW', 'status' => 'aktif']);
        PegawaiStaff::factory()->count(2)->create();
        Penandatanganan::factory()->count(1)->create(['status' => 'aktif']);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('totalPenduduk', 5);
        $response->assertViewHas('totalKK', 3);
        $response->assertViewHas('totalRT', 2);
        $response->assertViewHas('totalRW', 1);
        $response->assertViewHas('totalPegawai', 2);
        $response->assertViewHas('totalPenandatangan', 1);
    }

    public function test_dashboard_shows_user_counts(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('totalUsers');
        $response->assertViewHas('activeUsers');
        $response->assertViewHas('usersPerRole');
        $response->assertViewHas('recentUsers');
    }

    public function test_unauthenticated_user_cannot_access_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }
}
