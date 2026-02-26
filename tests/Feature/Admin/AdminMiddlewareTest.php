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
    private Role $rtRwRole;

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

        $this->rtRwRole = Role::create([
            'name'        => Role::RT_RW,
            'label'       => 'RT/RW',
            'description' => 'RT/RW access',
            'permissions' => ['penduduk.view'],
            'is_active'   => true,
        ]);
    }

    // ─── NON-ADMIN ROLE DENIAL ────────────────────────────────────

    public function test_rtrw_cannot_access_admin_dashboard(): void
    {
        /** @var User $rtRw */
        $rtRw = User::factory()->create([
            'role_id'   => $this->rtRwRole->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($rtRw)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    public function test_rtrw_cannot_access_admin_users(): void
    {
        /** @var User $rtRw */
        $rtRw = User::factory()->create([
            'role_id'   => $this->rtRwRole->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($rtRw)->get(route('admin.users.index'));

        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_routes(): void
    {
        /** @var User $admin */
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
        /** @var User $inactiveAdmin */
        $inactiveAdmin = User::factory()->create([
            'role_id'   => $this->adminRole->id,
            'is_active' => false,
        ]);

        $response = $this->actingAs($inactiveAdmin)->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_inactive_user_session_is_invalidated(): void
    {
        /** @var User $inactiveUser */
        $inactiveUser = User::factory()->create([
            'role_id'   => $this->rtRwRole->id,
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
