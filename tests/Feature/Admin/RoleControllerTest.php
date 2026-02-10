<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleControllerTest extends TestCase
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

        Role::create([
            'name'        => Role::OPERATOR,
            'label'       => 'Operator',
            'description' => 'Operator access',
            'permissions' => [],
            'is_active'   => true,
        ]);

        $this->admin = User::factory()->create([
            'role_id'   => $adminRole->id,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_view_roles_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.roles.index'));

        $response->assertStatus(200);
        $response->assertViewHas('roles');
    }

    public function test_roles_index_shows_user_count(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.roles.index'));

        $response->assertStatus(200);
        $roles = $response->viewData('roles');

        // At least admin role exists with 1 user (the admin)
        $adminRole = $roles->firstWhere('name', Role::ADMIN);
        $this->assertNotNull($adminRole);
        $this->assertEquals(1, $adminRole->users_count);
    }

    public function test_roles_index_displays_all_roles(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.roles.index'));

        $response->assertStatus(200);
        $roles = $response->viewData('roles');
        $this->assertGreaterThanOrEqual(2, $roles->count());
    }
}
