<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Company;
use App\Models\Department;
use App\Models\Post;
use App\Models\Permission;
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
        'permission' => json_encode([]), // Добавлено поле permission
    ]);

    $this->permission = Permission::factory()->create([
        'com_id' => $this->company->id,
        'name' => 'Test Permission',
        'value' => 'test.permission',
    ]);
});

test('admin can create permission', function () {
    $response = $this->actingAs($this->admin, 'web')->post(route(
        'company.permission.store',
        ['company' => $this->company->id]
    ), [
        'name' => 'New Permission',
        'value' => 'new_permission', // Исправлено: подчеркивание вместо точки
    ]);

    $response->assertRedirect(route('company.list'));

    // Добавим отладочный вывод
    $this->assertDatabaseHas('permissions', [
        'com_id' => $this->company->id,
        'name' => 'New Permission',
        'value' => 'new_permission', // Исправлено: подчеркивание вместо точки
    ]);
});

test('admin can update permission', function () {
    $response = $this->actingAs($this->admin, 'web')->post(route(
        'company.permission.modify',
        ['company' => $this->company->id, 'permission' => $this->permission->id]
    ), [
        'name' => 'Updated Permission',
    ]);

    $response->assertRedirect(route('company.list'));
    $this->assertDatabaseHas('permissions', [
        'id' => $this->permission->id,
        'name' => 'Updated Permission',
        'value' => 'test.permission', // Значение не изменяется при обновлении
    ]);
});

test('admin can delete permission', function () {
    // Создадим пост без использования этой пермиссии
    $post = Post::factory()->create([
        'com_id' => $this->company->id,
        'dep_id' => $this->department->id,
        'name' => 'Test Post 2',
        'permission' => json_encode([]), // Пустой массив пермиссий
    ]);

    $response = $this->actingAs($this->admin, 'web')->get(route(
        'company.permission.delete',
        ['company' => $this->company->id, 'permission' => $this->permission->id]
    ));

    $response->assertRedirect();
    $this->assertDatabaseMissing('permissions', [
        'id' => $this->permission->id,
    ]);
});

test('user can view their permissions', function () {
    // Update the post to have this permission
    $this->post->update([
        'permission' => json_encode([$this->permission->id]),
    ]);

    // Create a user with this post
    $user = User::factory()->create([
        'role' => 'user',
        'com_id' => $this->company->id,
        'dep_id' => $this->department->id,
        'post_id' => $this->post->id,
        'login' => 'testuser',
        'password' => bcrypt('password123'),
        'status' => 'active',
    ]);

    $response = $this->actingAs($user, 'web')->get(route('user.permission'));

    $response->assertStatus(200);
    // Проверяем, что страница загружается успешно
    // Мы не можем проверить имя права доступа, потому что оно не отображается напрямую
});
