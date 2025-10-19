<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_sign_in_page_is_accessible(): void
    {
        $response = $this->get(route('auth.sign_in'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.sign_in');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'login' => 'testuser',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->from(route('auth.sign_in'))
            ->post(route('auth.login'), [
                'login' => 'testuser',
                'password' => 'password123',
            ]);

        $response->assertRedirect(route('home.index'));
        $response->assertSessionHas('success', 'Вы успешно Аутентифицированы');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        User::factory()->create([
            'login' => 'testuser',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->from(route('auth.sign_in'))
            ->post(route('auth.login'), [
                'login' => 'testuser',
                'password' => 'wrongpassword',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_login_requires_login_field(): void
    {
        $response = $this->from(route('auth.sign_in'))
            ->post(route('auth.login'), [
                'password' => 'password123',
            ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_login_requires_password_field(): void
    {
        $response = $this->from(route('auth.sign_in'))
            ->post(route('auth.login'), [
                'login' => 'testuser',
            ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('auth.logout'));

        $response->assertRedirect(route('auth.sign_in'));
        $this->assertGuest();
    }
}
