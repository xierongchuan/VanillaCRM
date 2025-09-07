<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => 'admin',
        'login' => 'admin',
        'password' => bcrypt('password123'),
    ]);

    $this->user = User::factory()->create([
        'role' => 'user',
        'login' => 'testuser',
        'password' => bcrypt('password123'),
        'status' => 'active',
    ]);
});

test('user can view login page', function () {
    $response = $this->get('/sign_in');

    $response->assertStatus(200);
    $response->assertSee('Войти');
});

test('user can login with correct credentials', function () {
    $response = $this->post('/login', [
        'login' => 'admin',
        'password' => 'password123',
    ]);

    $response->assertRedirect('/');
    $response->assertSessionHas('success', 'Вы успешно Аутентифицированы');
    $this->assertAuthenticatedAs($this->admin);
});

test('user cannot login with incorrect credentials', function () {
    $response = $this->post('/login', [
        'login' => 'admin',
        'password' => 'wrongpassword',
    ]);

    $response->assertRedirect('/');
    $response->assertSessionHasErrors(['login']);
    $this->assertGuest();
});

test('authenticated user can logout', function () {
    $response = $this->actingAs($this->admin)->get('/logout'); // Исправлено на GET запрос

    $response->assertRedirect('/sign_in');
    $this->assertGuest();
});

test('admin can create another admin', function () {
    $response = $this->actingAs($this->admin)->post('/admin/store', [
        'login' => 'newadmin',
        'full_name' => 'New Admin',
        'password' => 'password123',
    ]);

    $response->assertRedirect('/company/list');
    $this->assertDatabaseHas('users', [
        'login' => 'newadmin',
        'role' => 'admin',
    ]);
});

test('admin can delete another admin', function () {
    $anotherAdmin = User::factory()->create([
        'role' => 'admin',
        'login' => 'anotheradmin',
    ]);

    $response = $this->actingAs($this->admin)->get("/admin/{$anotherAdmin->id}/delete");

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Администратор успешно удален');
    $this->assertDatabaseMissing('users', [
        'id' => $anotherAdmin->id,
    ]);
});
