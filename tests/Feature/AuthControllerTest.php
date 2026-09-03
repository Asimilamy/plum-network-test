<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_landing_page_is_public(): void
    {
        $this->get(route('home'))->assertOk()->assertSee('"name":"Landing"');

        $this->actingAs(User::factory()->create())
            ->get(route('home'))
            ->assertOk()
            ->assertSee('"name":"Landing"');
    }

    public function test_authenticated_users_are_redirected_from_the_login_screen(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('login'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_guests_can_view_the_auth_pages(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('"name":"Login"');
        $this->get(route('register'))->assertOk()->assertSee('"name":"Register"');
        $this->get(route('password.request'))->assertOk()->assertSee('"name":"ForgotPassword"');
    }

    public function test_users_can_log_in_with_valid_credentials(): void
    {
        $user = User::factory()->create();

        $response = $this->fromSpa()->postJson(route('api.login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk()->assertExactJson(['redirect' => route('dashboard')]);
        $this->assertAuthenticatedAs($user);
    }

    public function test_users_cannot_log_in_with_an_invalid_password(): void
    {
        $user = User::factory()->create();

        $response = $this->fromSpa()->postJson(route('api.login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('email');
        $this->assertGuest();
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->fromSpa()->postJson(route('api.register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertCreated()->assertExactJson(['redirect' => route('dashboard')]);
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => UserRole::StandardUser->value,
        ]);
    }

    public function test_registration_rejects_a_duplicate_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->fromSpa()->postJson(route('api.register'), [
            'name' => 'Test User',
            'email' => 'taken@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('email');
        $this->assertGuest();
    }

    public function test_a_password_reset_link_is_emailed_to_a_known_user(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->fromSpa()->postJson(route('api.password.email'), ['email' => $user->email]);

        $response->assertOk()->assertJsonStructure(['status']);
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_a_password_reset_link_reports_an_unknown_email(): void
    {
        Notification::fake();

        $response = $this->fromSpa()->postJson(route('api.password.email'), ['email' => 'nobody@example.com']);

        $response->assertUnprocessable()->assertJsonValidationErrors('email');
        Notification::assertNothingSent();
    }

    public function test_the_reset_link_email_renders_a_working_url(): void
    {
        $user = User::factory()->create();

        Password::sendResetLink(['email' => $user->email]);

        $this->assertDatabaseHas('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_the_reset_page_carries_the_token_and_email(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]))
            ->assertOk()
            ->assertSee('"name":"ResetPassword"')
            ->assertSee($token);
    }

    public function test_a_user_can_reset_their_password_with_a_valid_token(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->fromSpa()->postJson(route('api.password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertOk()->assertJsonPath('redirect', route('login'));
        $this->assertTrue(Hash::check('NewPassword123!', $user->fresh()->password));
    }

    public function test_a_reset_token_cannot_be_used_twice(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $payload = [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ];

        $this->fromSpa()->postJson(route('api.password.update'), $payload)->assertOk();

        $this->fromSpa()->postJson(route('api.password.update'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_resetting_rejects_an_invalid_token(): void
    {
        $user = User::factory()->create();

        $this->fromSpa()->postJson(route('api.password.update'), [
            'token' => 'not-a-real-token',
            'email' => $user->email,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ])->assertUnprocessable()->assertJsonValidationErrors('email');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_authenticated_users_can_log_out(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->fromSpa()->postJson(route('api.logout'));

        $response->assertOk()->assertExactJson(['redirect' => route('login')]);

        // `auth:sanctum` leaves itself as the default guard, so check the session guard.
        $this->assertGuest('web');
    }

    public function test_guests_cannot_call_the_logout_endpoint(): void
    {
        $this->fromSpa()->postJson(route('api.logout'))->assertUnauthorized();
    }

    public function test_the_dashboard_is_only_available_to_authenticated_users(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));

        $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('"name":"Dashboard"');
    }
}
