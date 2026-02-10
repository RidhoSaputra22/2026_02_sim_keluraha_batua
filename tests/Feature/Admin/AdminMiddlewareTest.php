<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private Role $adminRole;
    private Role $operatorRole;
    private Role $wargaRole;

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

        $this->operatorRole = Role::create([
            'name'        => Role::OPERATOR,
            'label'       => 'Operator',
            'description' => 'Operator access',
            'permissions' => ['penduduk.view'],
            'is_active'   => true,
        ]);

        $this->wargaRole = Role::create([
            'name'        => Role::WARGA,
            'label'       => 'Warga',
            'description' => 'Citizen portal',
            'permissions' => [],
            'is_active'   => true,
        ]);
    }

    // ─── NON-ADMIN ROLE DENIAL ────────────────────────────────────

    public function test_operator_cannot_access_admin_dashboard(): void
    {
        $operator = User::factory()->create([
            'role_id'   => $this->operatorRole->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($operator)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    public function test_warga_cannot_access_admin_dashboard(): void
    {
        $warga = User::factory()->create([
            'role_id'   => $this->wargaRole->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($warga)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    public function test_operator_cannot_access_admin_users(): void
    {
        $operator = User::factory()->create([
            'role_id'   => $this->operatorRole->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($operator)->get(route('admin.users.index'));

        $response->assertStatus(403);
    }

    public function test_operator_cannot_access_admin_penduduk(): void
    {
        $operator = User::factory()->create([
            'role_id'   => $this->operatorRole->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($operator)->get(route('admin.penduduk.index'));

        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_routes(): void
    {
        $admin = User::factory()->create([
            'role_id'   => $this->adminRole->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    // ─── INACTIVE USER ────────────────────────────────────────────

    public function test_inactive_admin_gets_logged_out(): void
    {
        $inactiveAdmin = User::factory()->create([
            'role_id'   => $this->adminRole->id,
            'is_active' => false,
        ]);

        $response = $this->actingAs($inactiveAdmin)->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_inactive_user_session_is_invalidated(): void
    {
        $inactiveUser = User::factory()->create([
            'role_id'   => $this->operatorRole->id,
            'is_active' => false,
        ]);

        $response = $this->actingAs($inactiveUser)->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    // ─── UNAUTHENTICATED ──────────────────────────────────────────

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_unauthenticated_user_cannot_access_admin_users(): void
    {
        $response = $this->get(route('admin.users.index'));

        $response->assertRedirect(route('login'));
    }
}
