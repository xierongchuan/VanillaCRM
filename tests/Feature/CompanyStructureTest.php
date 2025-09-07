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
});

test('admin can view company list', function () {
    $response = $this->actingAs($this->admin, 'web')->get(route('company.list'));

    $response->assertStatus(200);
    $response->assertSee($this->company->name);
});

test('admin can create a new company', function () {
    $response = $this->actingAs($this->admin, 'web')->post(route('company.store'), [
        'name' => 'New Test Company',
    ]);

    $response->assertRedirect(route('company.list'));
    $this->assertDatabaseHas('companies', [
        'name' => 'New Test Company',
    ]);
});

test('admin can update company', function () {
    $response = $this->actingAs($this->admin, 'web')->post(route('company.modify', ['company' => $this->company->id]), [
        'name' => 'Updated Company Name',
    ]);

    $response->assertRedirect(route('company.list'));
    $this->assertDatabaseHas('companies', [
        'id' => $this->company->id,
        'name' => 'Updated Company Name',
    ]);
});

test('admin can delete company', function () {
    // Create a company without departments or permissions for deletion
    $company = Company::factory()->create([
        'name' => 'Deletable Company',
    ]);

    $response = $this->actingAs($this->admin, 'web')->get(route('company.delete', ['company' => $company->id]));

    // Проверяем, что перенаправление происходит (может быть на любую страницу)
    $response->assertStatus(302); // 302 - код перенаправления
    $this->assertDatabaseMissing('companies', [
        'id' => $company->id,
    ]);
});

test('admin can create department', function () {
    // First create a new company for this test
    $company = Company::factory()->create([
        'name' => 'Test Company 2',
    ]);

    $response = $this->actingAs($this->admin, 'web')->post(route(
        'company.department.store',
        ['company' => $company->id]
    ), [
        'name' => 'New Department',
    ]);

    $response->assertRedirect(route('company.list'));
    $this->assertDatabaseHas('departments', [
        'com_id' => $company->id,
        'name' => 'New Department',
    ]);
});

test('admin can update department', function () {
    $response = $this->actingAs($this->admin, 'web')->post(route(
        'company.department.modify',
        ['company' => $this->company->id, 'department' => $this->department->id]
    ), [
        'name' => 'Updated Dept Name', // Укороченное имя
    ]);

    $response->assertRedirect(route('company.list'));
    $this->assertDatabaseHas('departments', [
        'id' => $this->department->id,
        'name' => 'Updated Dept Name', // Укороченное имя
    ]);
});

test('admin can delete department', function () {
    // Create a new department that is not used
    $department = Department::factory()->create([
        'com_id' => $this->company->id,
        'name' => 'Test Department 2',
    ]);

    $response = $this->actingAs($this->admin, 'web')->get(route(
        'company.department.delete',
        ['company' => $this->company->id, 'department' => $department->id]
    ));

    $response->assertRedirect();
    $this->assertDatabaseMissing('departments', [
        'id' => $department->id,
    ]);
});

test('admin can create post', function () {
    // Create a new department for this test
    $department = Department::factory()->create([
        'com_id' => $this->company->id,
        'name' => 'Test Department 2',
    ]);

    $response = $this->actingAs($this->admin, 'web')->post(route(
        'company.department.post.store',
        ['company' => $this->company->id, 'department' => $department->id]
    ), [
        'name' => 'New Post',
        'permission' => [], // Добавлено поле permission
    ]);

    $response->assertRedirect(route(
        'company.department.index',
        ['company' => $this->company->id, 'department' => $department->id]
    ));
    $this->assertDatabaseHas('posts', [
        'dep_id' => $department->id,
        'name' => 'New Post',
    ]);
});

test('admin can update post', function () {
    $response = $this->actingAs($this->admin, 'web')->post(route(
        'company.department.post.modify',
        ['company' => $this->company->id, 'department' => $this->department->id, 'post' => $this->post->id]
    ), [
        'name' => 'Updated Post Name',
        'permission' => [], // Добавлено поле permission
    ]);

    $response->assertRedirect(route(
        'company.department.index',
        ['company' => $this->company->id, 'department' => $this->department->id]
    ));
    $this->assertDatabaseHas('posts', [
        'id' => $this->post->id,
        'name' => 'Updated Post Name',
    ]);
});

test('admin can delete post', function () {
    // Create a post without users
    $post = Post::factory()->create([
        'com_id' => $this->company->id,
        'dep_id' => $this->department->id,
        'name' => 'Test Post 2',
    ]);

    $response = $this->actingAs($this->admin, 'web')->get(route(
        'company.department.post.delete',
        ['company' => $this->company->id, 'department' => $this->department->id, 'post' => $post->id]
    ));

    $response->assertRedirect();
    $this->assertDatabaseMissing('posts', [
        'id' => $post->id,
    ]);
});
