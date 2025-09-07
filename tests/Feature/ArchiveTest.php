<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Company;
use App\Models\Department;
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
});

test('admin can view company archive', function () {
    $response = $this->actingAs($this->admin, 'web')->get("/company/{$this->company->id}/archive");

    $response->assertStatus(200);
});

test('user can view permission page', function () {
    // Create a user
    $user = User::factory()->create([
        'role' => 'user',
        'com_id' => $this->company->id,
        'dep_id' => $this->department->id,
        'login' => 'testuser',
        'password' => bcrypt('password123'),
        'status' => 'active',
    ]);

    $response = $this->actingAs($user, 'web')->get('/permission');

    $response->assertStatus(200);
});
