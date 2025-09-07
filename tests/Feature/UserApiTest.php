<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Company;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => 'admin',
        'login' => 'admin',
        'password' => bcrypt('password123'),
    ]);

    $this->company = Company::factory()->create([
        'name' => 'Test Company',
    ]);

    $this->department = Department::factory()->create([
        'com_id' => $this->company->id,
        'name' => 'Test Department',
    ]);

    $this->user = User::factory()->create([
        'role' => 'user',
        'com_id' => $this->company->id,
        'dep_id' => $this->department->id,
        'login' => 'testuser',
        'password' => bcrypt('password123'),
        'full_name' => 'Test User',
        'phone_number' => '+1234567890',
    ]);
});

test('admin can get list of users', function () {
    Sanctum::actingAs($this->admin);

    $response = $this->getJson('/api/users');

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => [
            '*' => ['id', 'login', 'full_name', 'phone_number']
        ],
        'links',
        'meta'
    ]);
});

test('admin can get specific user', function () {
    Sanctum::actingAs($this->admin);

    $response = $this->getJson('/api/users/' . $this->user->id);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => ['id', 'login', 'full_name', 'phone_number']
    ]);
    $response->assertJson([
        'data' => [
            'id' => $this->user->id,
            'login' => 'testuser',
            'full_name' => 'Test User',
            'phone_number' => '+1234567890'
        ]
    ]);
});

test('admin can check user status', function () {
    Sanctum::actingAs($this->admin);

    $response = $this->getJson('/api/users/' . $this->user->id . '/status');

    $response->assertStatus(200);
    $response->assertJson([
        'is_active' => true
    ]);
});

test('admin can search users by phone number', function () {
    Sanctum::actingAs($this->admin);

    $response = $this->getJson('/api/users?phone=1234567890');

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => [
            '*' => ['id', 'login', 'full_name', 'phone_number']
        ]
    ]);

    // Check that our user is in the results
    $response->assertJsonFragment([
        'login' => 'testuser',
        'full_name' => 'Test User',
        'phone_number' => '+1234567890'
    ]);
});

test('admin can paginate users', function () {
    // Create additional users
    User::factory()->count(20)->create([
        'role' => 'user',
        'com_id' => $this->company->id,
        'dep_id' => $this->department->id,
    ]);

    Sanctum::actingAs($this->admin);

    $response = $this->getJson('/api/users?per_page=5');

    $response->assertStatus(200);
    $response->assertJsonCount(5, 'data');
    $response->assertJsonStructure([
        'data',
        'links' => ['first', 'last', 'prev', 'next'],
        'meta' => ['current_page', 'from', 'last_page', 'path', 'per_page', 'to', 'total']
    ]);
});

test('non-admin cannot access user api', function () {
    Sanctum::actingAs($this->user);

    $response = $this->getJson('/api/users');

    $response->assertStatus(403);
});

test('api returns 404 for non-existent user', function () {
    Sanctum::actingAs($this->admin);

    $response = $this->getJson('/api/users/999999');

    $response->assertStatus(404);
    $response->assertJson([
        'message' => 'Пользователь не найден'
    ]);
});
