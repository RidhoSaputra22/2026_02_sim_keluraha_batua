<?php

namespace Tests\Feature\Admin;

use App\Models\Keluarga;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\PegawaiStaff;
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
        Penduduk::factory()->count(5)->create();
        Keluarga::factory()->count(3)->create();
        PegawaiStaff::factory()->count(2)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('totalPenduduk');
        $response->assertViewHas('totalKK');
        $response->assertViewHas('totalRT');
        $response->assertViewHas('totalRW');
        $response->assertViewHas('totalPegawai');
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
