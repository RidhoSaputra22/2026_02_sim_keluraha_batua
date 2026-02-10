<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Role $adminRole;
    private Role $operatorRole;

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

        $this->admin = User::factory()->create([
            'role_id'   => $this->adminRole->id,
            'is_active' => true,
        ]);
    }

    // ─── INDEX ────────────────────────────────────────────────────

    public function test_admin_can_view_users_index(): void
    {
        User::factory()->count(3)->create(['role_id' => $this->operatorRole->id, 'is_active' => true]);

        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertViewHas('users');
        $response->assertViewHas('roles');
    }

    public function test_users_index_search_works(): void
    {
        User::factory()->create([
            'name'    => 'Budi Santoso',
            'email'   => 'budi@example.com',
            'role_id' => $this->operatorRole->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.users.index', ['search' => 'Budi']));

        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');
    }

    public function test_users_index_filter_by_role(): void
    {
        User::factory()->create(['role_id' => $this->operatorRole->id, 'is_active' => true, 'name' => 'Operator User']);

        $response = $this->actingAs($this->admin)->get(route('admin.users.index', ['role_id' => $this->operatorRole->id]));

        $response->assertStatus(200);
    }

    // ─── CREATE ───────────────────────────────────────────────────

    public function test_admin_can_view_create_user_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.create'));

        $response->assertStatus(200);
        $response->assertViewHas('roles');
    }

    // ─── STORE ────────────────────────────────────────────────────

    public function test_admin_can_store_new_user(): void
    {
        $userData = [
            'name'      => 'User Baru',
            'email'     => 'userbaru@example.com',
            'password'  => 'password123',
            'role_id'   => $this->operatorRole->id,
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), $userData);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'name'  => 'User Baru',
            'email' => 'userbaru@example.com',
        ]);
    }

    public function test_store_user_validates_required_fields(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), []);

        $response->assertSessionHasErrors(['name', 'email', 'password', 'role_id']);
    }

    public function test_store_user_validates_unique_email(): void
    {
        User::factory()->create(['email' => 'duplicate@example.com', 'role_id' => $this->operatorRole->id]);

        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name'     => 'Duplicate',
            'email'    => 'duplicate@example.com',
            'password' => 'password123',
            'role_id'  => $this->operatorRole->id,
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_store_user_validates_password_minimum(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name'     => 'User',
            'email'    => 'user@example.com',
            'password' => '123',
            'role_id'  => $this->operatorRole->id,
        ]);

        $response->assertSessionHasErrors('password');
    }

    // ─── EDIT ─────────────────────────────────────────────────────

    public function test_admin_can_view_edit_user_form(): void
    {
        $user = User::factory()->create(['role_id' => $this->operatorRole->id, 'is_active' => true]);

        $response = $this->actingAs($this->admin)->get(route('admin.users.edit', $user));

        $response->assertStatus(200);
        $response->assertViewHas('user');
        $response->assertViewHas('roles');
    }

    // ─── UPDATE ───────────────────────────────────────────────────

    public function test_admin_can_update_user(): void
    {
        $user = User::factory()->create(['role_id' => $this->operatorRole->id, 'is_active' => true]);

        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $user), [
            'name'    => 'Updated Name',
            'email'   => $user->email,
            'role_id' => $this->operatorRole->id,
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id'   => $user->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_update_user_without_password_keeps_old_password(): void
    {
        $user = User::factory()->create([
            'role_id'  => $this->operatorRole->id,
            'is_active' => true,
        ]);
        $oldPasswordHash = $user->password;

        $this->actingAs($this->admin)->put(route('admin.users.update', $user), [
            'name'    => 'Updated',
            'email'   => $user->email,
            'role_id' => $this->operatorRole->id,
        ]);

        $user->refresh();
        $this->assertEquals($oldPasswordHash, $user->password);
    }

    // ─── DESTROY ──────────────────────────────────────────────────

    public function test_admin_can_delete_user(): void
    {
        $user = User::factory()->create(['role_id' => $this->operatorRole->id, 'is_active' => true]);

        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $user));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_cannot_delete_self(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $this->admin));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    // ─── TOGGLE ACTIVE ────────────────────────────────────────────

    public function test_admin_can_toggle_user_active_status(): void
    {
        $user = User::factory()->create([
            'role_id'   => $this->operatorRole->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.users.toggle-active', $user));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $user->refresh();
        $this->assertFalse($user->is_active);
    }

    public function test_admin_cannot_toggle_self_active(): void
    {
        $response = $this->actingAs($this->admin)->patch(route('admin.users.toggle-active', $this->admin));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->admin->refresh();
        $this->assertTrue($this->admin->is_active);
    }
}
