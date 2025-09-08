<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Company;
use App\Models\Department;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    $this->post = Post::factory()->create([
        'com_id' => $this->company->id,
        'dep_id' => $this->department->id,
        'name' => 'Test Post',
    ]);

    $this->user = User::factory()->create([
        'role' => 'user',
        'com_id' => $this->company->id,
        'dep_id' => $this->department->id,
        'post_id' => $this->post->id,
        'login' => 'testuser',
        'password' => bcrypt('password123'),
        'full_name' => 'Test User',
        'status' => 'active',
        'in_bot_role' => 'user',
    ]);
});

test('admin can access admin routes', function () {
    $response = $this->actingAs($this->admin, 'web')->get(route('admin.index'));

    $response->assertStatus(200);
});

test('regular user cannot access admin routes', function () {
    $response = $this->actingAs($this->user, 'web')->get(route('admin.index'));

    $response->assertRedirect(route('auth.sign_in'));
});

test('active user can access user routes', function () {
    $response = $this->actingAs($this->user, 'web')->get(route('user.permission'));

    $response->assertStatus(200);
});

test('inactive user cannot access user routes', function () {
    $this->user->update(['status' => 'deactive']);

    $response = $this->actingAs($this->user, 'web')->get(route('user.permission'));

    $response->assertRedirect(route('auth.sign_in'));
});

test('user with director role can be created', function () {
    $response = $this->actingAs($this->admin, 'web')->post(route(
        'company.user.store',
        ['company' => $this->company->id]
    ), [
        'login' => 'director',
        'full_name' => 'Director User',
        'department' => $this->department->id,
        'phone_number' => '+1234567890',
        'password' => 'password123',
        'in_bot_role' => 'director',
    ]);

    $response->assertRedirect(route('company.list'));
    $this->assertDatabaseHas('users', [
        'login' => 'director',
        'in_bot_role' => 'director',
    ]);
});

test('only one director can exist per company', function () {
    // Create a director user
    $director = User::factory()->create([
        'role' => 'user',
        'com_id' => $this->company->id,
        'dep_id' => $this->department->id,
        'login' => 'director1',
        'password' => bcrypt('password123'),
        'in_bot_role' => 'director',
    ]);

    // Try to create another director
    $response = $this->actingAs($this->admin, 'web')->post(route(
        'company.user.store',
        ['company' => $this->company->id]
    ), [
        'login' => 'director2',
        'full_name' => 'Second Director',
        'department' => $this->department->id,
        'phone_number' => '+1234567891',
        'password' => 'password123',
        'in_bot_role' => 'director',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrors();
});

test('user with cashier role can be created', function () {
    $response = $this->actingAs($this->admin, 'web')->post(route(
        'company.user.store',
        ['company' => $this->company->id]
    ), [
        'login' => 'cashier',
        'full_name' => 'Cashier User',
        'department' => $this->department->id,
        'phone_number' => '+1234567890',
        'password' => 'password123',
        'in_bot_role' => 'cashier',
    ]);

    $response->assertRedirect(route('company.list'));
    $this->assertDatabaseHas('users', [
        'login' => 'cashier',
        'in_bot_role' => 'cashier',
    ]);
});

test('only one cashier can exist per company', function () {
    // Create an cashier user
    $cashier = User::factory()->create([
        'role' => 'user',
        'com_id' => $this->company->id,
        'dep_id' => $this->department->id,
        'login' => 'cashier1',
        'password' => bcrypt('password123'),
        'in_bot_role' => 'cashier',
    ]);

    // Try to create another cashier
    $response = $this->actingAs($this->admin, 'web')->post(route(
        'company.user.store',
        ['company' => $this->company->id]
    ), [
        'login' => 'cashier2',
        'full_name' => 'Second Cashier',
        'department' => $this->department->id,
        'phone_number' => '+1234567891',
        'password' => 'password123',
        'in_bot_role' => 'cashier',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrors();
});
