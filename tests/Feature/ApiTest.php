<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'login' => 'adminuser',
            'password' => bcrypt('password123'),
        ]);
    }

    public function test_api_health_check(): void
    {
        $response = $this->getJson('/api/up');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    public function test_admin_can_login_via_api(): void
    {
        $response = $this->postJson('/api/session', [
            'login' => 'adminuser',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token']);
    }

    public function test_non_admin_cannot_login_via_api(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'login' => 'regularuser',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/session', [
            'login' => 'regularuser',
            'password' => 'password123',
        ]);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Доступ запрещён']);
    }

    public function test_invalid_credentials_cannot_login(): void
    {
        $response = $this->postJson('/api/session', [
            'login' => 'adminuser',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Неверные данные']);
    }

    public function test_api_login_validates_required_fields(): void
    {
        $response = $this->postJson('/api/session', [
            'login' => '',
            'password' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['login', 'password']);
    }

    public function test_api_login_validates_minimum_length(): void
    {
        $response = $this->postJson('/api/session', [
            'login' => 'abc',
            'password' => '12345',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['login', 'password']);
    }

    public function test_authenticated_admin_can_logout(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->deleteJson('/api/session');

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Сессия завершена']);
    }

    public function test_unauthenticated_user_cannot_logout(): void
    {
        $response = $this->deleteJson('/api/session');

        $response->assertStatus(401);
    }

    public function test_admin_can_get_users_list(): void
    {
        Sanctum::actingAs($this->admin);

        $company = Company::factory()->create();
        $department = Department::factory()->create(['com_id' => $company->id]);

        User::factory()->count(5)->create([
            'com_id' => $company->id,
            'dep_id' => $department->id,
        ]);

        $response = $this->getJson('/api/users');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'login',
                    'role',
                ],
            ],
        ]);
    }

    public function test_admin_can_get_users_with_pagination(): void
    {
        Sanctum::actingAs($this->admin);

        $company = Company::factory()->create();
        $department = Department::factory()->create(['com_id' => $company->id]);

        User::factory()->count(20)->create([
            'com_id' => $company->id,
            'dep_id' => $department->id,
        ]);

        $response = $this->getJson('/api/users?per_page=10');

        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data');
    }

    public function test_admin_can_search_users_by_phone(): void
    {
        Sanctum::actingAs($this->admin);

        $company = Company::factory()->create();
        $department = Department::factory()->create(['com_id' => $company->id]);

        User::factory()->create([
            'com_id' => $company->id,
            'dep_id' => $department->id,
            'phone_number' => '+1234567890',
        ]);

        User::factory()->create([
            'com_id' => $company->id,
            'dep_id' => $department->id,
            'phone_number' => '+9876543210',
        ]);

        $response = $this->getJson('/api/users?phone=1234567890');

        $response->assertStatus(200);
        $this->assertGreaterThanOrEqual(1, count($response->json('data')));
    }

    public function test_admin_can_get_single_user(): void
    {
        Sanctum::actingAs($this->admin);

        $company = Company::factory()->create();
        $department = Department::factory()->create(['com_id' => $company->id]);

        $user = User::factory()->create([
            'com_id' => $company->id,
            'dep_id' => $department->id,
        ]);

        $response = $this->getJson("/api/users/{$user->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'login',
                'role',
            ],
        ]);
    }

    public function test_admin_gets_404_for_nonexistent_user(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->getJson('/api/users/99999');

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Пользователь не найден']);
    }

    public function test_admin_can_check_user_status(): void
    {
        Sanctum::actingAs($this->admin);

        $company = Company::factory()->create();
        $department = Department::factory()->create(['com_id' => $company->id]);

        $activeUser = User::factory()->create([
            'com_id' => $company->id,
            'dep_id' => $department->id,
            'status' => 'active',
        ]);

        $response = $this->getJson("/api/users/{$activeUser->id}/status");

        $response->assertStatus(200);
        $response->assertJson(['is_active' => true]);
    }

    public function test_admin_can_check_inactive_user_status(): void
    {
        Sanctum::actingAs($this->admin);

        $company = Company::factory()->create();
        $department = Department::factory()->create(['com_id' => $company->id]);

        $inactiveUser = User::factory()->create([
            'com_id' => $company->id,
            'dep_id' => $department->id,
            'status' => 'deactive',
        ]);

        $response = $this->getJson("/api/users/{$inactiveUser->id}/status");

        $response->assertStatus(200);
        $response->assertJson(['is_active' => false]);
    }

    public function test_non_admin_cannot_access_users_endpoint(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/users');

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_access_users_endpoint(): void
    {
        $response = $this->getJson('/api/users');

        $response->assertStatus(401);
    }
}
