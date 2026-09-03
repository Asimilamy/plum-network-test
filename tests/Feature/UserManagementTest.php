<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_default_to_the_standard_role(): void
    {
        $user = User::create([
            'name' => 'Fresh User',
            'email' => 'fresh@example.com',
            'password' => 'Password123!',
        ]);

        $this->assertSame(UserRole::StandardUser, $user->fresh()->role);
        $this->assertFalse($user->isSuperAdmin());
    }

    public function test_a_super_admin_can_open_the_user_management_page(): void
    {
        $this->actingAs(User::factory()->superAdmin()->create())
            ->get(route('users.index'))
            ->assertOk()
            ->assertSee('"name":"Admin\/Users"')
            ->assertSee('Super admin')
            ->assertSee('Standard user');
    }

    public function test_a_super_admin_can_read_a_page_of_users(): void
    {
        $admin = User::factory()->superAdmin()->create(['name' => 'Ada Admin']);
        User::factory()->count(4)->create();

        $response = $this->actingAs($admin)
            ->getJson(route('api.users.index', ['per_page' => 2]));

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 5)
            ->assertJsonPath('meta.per_page', 2)
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.last_page', 3)
            ->assertJsonStructure([
                'data' => [['id', 'name', 'email', 'role', 'roleLabel', 'createdAt']],
                'links',
                'meta',
            ]);
    }

    public function test_the_user_list_is_ordered_newest_first_across_pages(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $second = User::factory()->create();
        $third = User::factory()->create();

        $this->actingAs($admin)
            ->getJson(route('api.users.index', ['per_page' => 2]))
            ->assertOk()
            ->assertJsonPath('data.0.id', $third->id)
            ->assertJsonPath('data.1.id', $second->id);

        $this->actingAs($admin)
            ->getJson(route('api.users.index', ['per_page' => 2, 'page' => 2]))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $admin->id)
            ->assertJsonPath('meta.current_page', 2);
    }

    public function test_the_user_list_rejects_an_out_of_range_page_size(): void
    {
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)
            ->getJson(route('api.users.index', ['per_page' => 500]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('per_page');
    }

    public function test_a_standard_user_cannot_read_a_page_of_users(): void
    {
        $this->actingAs(User::factory()->create())
            ->getJson(route('api.users.index'))
            ->assertForbidden();
    }

    public function test_guests_cannot_read_a_page_of_users(): void
    {
        $this->getJson(route('api.users.index'))->assertUnauthorized();
    }

    public function test_a_standard_user_cannot_read_the_user_list(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('users.index'))
            ->assertForbidden();
    }

    public function test_guests_cannot_read_the_user_list(): void
    {
        $this->get(route('users.index'))->assertRedirect(route('login'));
    }

    public function test_a_super_admin_can_create_a_user(): void
    {
        $admin = User::factory()->superAdmin()->create();

        $response = $this->actingAs($admin)->fromSpa()->postJson(route('api.users.store'), [
            'name' => 'New Admin',
            'email' => 'new-admin@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => UserRole::SuperAdmin->value,
        ]);

        $response->assertCreated()->assertJsonPath('user.email', 'new-admin@example.com');
        $response->assertJsonPath('user.roleLabel', 'Super admin');

        $this->assertDatabaseHas('users', [
            'email' => 'new-admin@example.com',
            'role' => UserRole::SuperAdmin->value,
        ]);
    }

    public function test_creating_a_user_rejects_an_unknown_role(): void
    {
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)->fromSpa()->postJson(route('api.users.store'), [
            'name' => 'New User',
            'email' => 'new-user@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'owner',
        ])->assertUnprocessable()->assertJsonValidationErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'new-user@example.com']);
    }

    public function test_a_standard_user_cannot_create_a_user(): void
    {
        $this->actingAs(User::factory()->create())->fromSpa()->postJson(route('api.users.store'), [
            'name' => 'New User',
            'email' => 'new-user@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => UserRole::SuperAdmin->value,
        ])->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'new-user@example.com']);
    }

    public function test_guests_cannot_create_a_user(): void
    {
        $this->fromSpa()->postJson(route('api.users.store'), [
            'name' => 'New User',
            'email' => 'new-user@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => UserRole::SuperAdmin->value,
        ])->assertUnauthorized();
    }

    public function test_the_seeder_creates_both_default_roles(): void
    {
        $this->seed();

        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
            'role' => UserRole::SuperAdmin->value,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'user@example.com',
            'role' => UserRole::StandardUser->value,
        ]);
    }
}
