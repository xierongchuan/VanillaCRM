<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Department;
use App\Models\Permission;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Company $company;
    private Department $department;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->company = Company::factory()->create();
        $this->department = Department::factory()->create([
            'com_id' => $this->company->id,
        ]);
    }

    public function test_admin_can_view_user_create_form(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('company.user.create', $this->company));

        $response->assertStatus(200);
        $response->assertViewIs('company.user.create');
        $response->assertViewHas('company', $this->company);
        $response->assertViewHas('departments');
    }

    public function test_admin_can_create_user(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        $this->actingAs($this->admin);

        $userData = [
            'login' => 'newuser',
            'full_name' => 'New User Name',
            'department' => $this->department->id,
            'phone_number' => '+1234567890',
            'password' => 'password123',
            'in_bot_role' => 'user',
        ];

        $response = $this->from(route('company.user.create', $this->company))
            ->post(
                route('company.user.store', $this->company),
                $userData
            );

        $response->assertRedirect(route('company.list'));

        $this->assertDatabaseHas('users', [
            'login' => 'newuser',
            'full_name' => 'New User Name',
            'com_id' => $this->company->id,
            'dep_id' => $this->department->id,
            'phone_number' => '+1234567890',
            'role' => 'user',
            'in_bot_role' => 'user',
        ]);
    }

    public function test_user_login_must_be_unique(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        $this->actingAs($this->admin);

        User::factory()->create(['login' => 'existinguser']);

        $response = $this->from(route('company.user.create', $this->company))
            ->post(route('company.user.store', $this->company), [
                'login' => 'existinguser',
                'full_name' => 'New User',
                'department' => $this->department->id,
                'phone_number' => '+1234567890',
                'password' => 'password123',
                'in_bot_role' => 'user',
            ]);

        $response->assertSessionHasErrors('login');
    }

    public function test_user_full_name_is_required(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        $this->actingAs($this->admin);

        $response = $this->from(route('company.user.create', $this->company))
            ->post(route('company.user.store', $this->company), [
                'login' => 'testuser',
                'full_name' => '',
                'department' => $this->department->id,
                'phone_number' => '+1234567890',
                'password' => 'password123',
                'in_bot_role' => 'user',
            ]);

        $response->assertSessionHasErrors('full_name');
    }

    public function test_user_phone_number_is_required(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        $this->actingAs($this->admin);

        $response = $this->from(route('company.user.create', $this->company))
            ->post(route('company.user.store', $this->company), [
                'login' => 'testuser',
                'full_name' => 'Test User',
                'department' => $this->department->id,
                'phone_number' => '',
                'password' => 'password123',
                'in_bot_role' => 'user',
            ]);

        $response->assertSessionHasErrors('phone_number');
    }

    public function test_user_password_must_be_at_least_6_characters(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        $this->actingAs($this->admin);

        $response = $this->from(route('company.user.create', $this->company))
            ->post(route('company.user.store', $this->company), [
                'login' => 'testuser',
                'full_name' => 'Test User',
                'department' => $this->department->id,
                'phone_number' => '+1234567890',
                'password' => '12345',
                'in_bot_role' => 'user',
            ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_only_one_director_per_company(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        $this->actingAs($this->admin);

        User::factory()->create([
            'com_id' => $this->company->id,
            'dep_id' => $this->department->id,
            'in_bot_role' => 'director',
        ]);

        $response = $this->from(route('company.user.create', $this->company))
            ->post(route('company.user.store', $this->company), [
                'login' => 'testuser',
                'full_name' => 'Test User',
                'department' => $this->department->id,
                'phone_number' => '+1234567890',
                'password' => 'password123',
                'in_bot_role' => 'director',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    public function test_only_one_cashier_per_company(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        $this->actingAs($this->admin);

        User::factory()->create([
            'com_id' => $this->company->id,
            'dep_id' => $this->department->id,
            'in_bot_role' => 'cashier',
        ]);

        $response = $this->from(route('company.user.create', $this->company))
            ->post(route('company.user.store', $this->company), [
                'login' => 'testuser',
                'full_name' => 'Test User',
                'department' => $this->department->id,
                'phone_number' => '+1234567890',
                'password' => 'password123',
                'in_bot_role' => 'cashier',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    public function test_admin_can_view_user_update_form(): void
    {
        $this->actingAs($this->admin);

        $user = User::factory()->create([
            'com_id' => $this->company->id,
            'dep_id' => $this->department->id,
        ]);

        $response = $this->get(route('company.user.update', [
            'company' => $this->company,
            'user' => $user,
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('company.user.update');
        $response->assertViewHas('company', $this->company);
        $response->assertViewHas('user', $user);
    }

    public function test_admin_can_update_user(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        $this->actingAs($this->admin);

        $user = User::factory()->create([
            'com_id' => $this->company->id,
            'dep_id' => $this->department->id,
            'full_name' => 'Old Name',
        ]);

        $response = $this->from(route('company.user.update', [
            'company' => $this->company,
            'user' => $user,
        ]))
            ->post(route('company.user.modify', [
                'company' => $this->company,
                'user' => $user,
            ]), [
                'department' => $this->department->id,
                'full_name' => 'Updated Name',
                'phone_number' => '+9876543210',
                'in_bot_role' => 'user',
            ]);

        $response->assertRedirect(route('company.list'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'full_name' => 'Updated Name',
            'phone_number' => '+9876543210',
        ]);
    }

    public function test_admin_can_update_user_password(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        $this->actingAs($this->admin);

        $user = User::factory()->create([
            'com_id' => $this->company->id,
            'dep_id' => $this->department->id,
        ]);

        $response = $this->from(route('company.user.update', [
            'company' => $this->company,
            'user' => $user,
        ]))
            ->post(route('company.user.modify', [
                'company' => $this->company,
                'user' => $user,
            ]), [
                'department' => $this->department->id,
                'full_name' => 'Test User',
                'phone_number' => '+1234567890',
                'password' => 'newpassword123',
                'in_bot_role' => 'user',
            ]);

        $response->assertRedirect(route('company.list'));

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    public function test_admin_can_activate_user(): void
    {
        $this->actingAs($this->admin);

        $user = User::factory()->create([
            'com_id' => $this->company->id,
            'dep_id' => $this->department->id,
            'status' => 'deactive',
        ]);

        $response = $this->get(route('company.user.activate', [
            'company' => $this->company,
            'user' => $user,
        ]));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Сотрудник успешно активирован');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => 'active',
        ]);
    }

    public function test_admin_can_deactivate_user(): void
    {
        $this->actingAs($this->admin);

        $user = User::factory()->create([
            'com_id' => $this->company->id,
            'dep_id' => $this->department->id,
            'status' => 'active',
        ]);

        $response = $this->get(route('company.user.deactivate', [
            'company' => $this->company,
            'user' => $user,
        ]));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Сотрудник успешно деактивирован');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => 'deactive',
        ]);
    }

    public function test_admin_can_delete_user(): void
    {
        $this->actingAs($this->admin);

        $user = User::factory()->create([
            'com_id' => $this->company->id,
            'dep_id' => $this->department->id,
        ]);

        $response = $this->get(route('company.user.delete', [
            'company' => $this->company,
            'user' => $user,
        ]));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Сотрудник успешно удален');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_admin_can_view_admin_management_page(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.index');
        $response->assertViewHas('admins');
    }

    public function test_admin_can_create_another_admin(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        $this->actingAs($this->admin);

        $adminData = [
            'login' => 'newadmin',
            'full_name' => 'New Admin',
            'password' => 'adminpassword123',
        ];

        $response = $this->from(route('admin.index'))
            ->post(route('admin.store'), $adminData);

        $response->assertRedirect(route('company.list'));

        $this->assertDatabaseHas('users', [
            'login' => 'newadmin',
            'full_name' => 'New Admin',
            'role' => 'admin',
        ]);
    }

    public function test_admin_can_delete_another_admin(): void
    {
        $this->actingAs($this->admin);

        $anotherAdmin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->get(route('admin.delete', $anotherAdmin));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Администратор успешно удален');

        $this->assertDatabaseMissing('users', [
            'id' => $anotherAdmin->id,
        ]);
    }

    public function test_user_can_view_permission_page(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        $post = Post::factory()->create([
            'com_id' => $this->company->id,
            'dep_id' => $this->department->id,
        ]);

        $user = User::factory()->create([
            'com_id' => $this->company->id,
            'dep_id' => $this->department->id,
            'post_id' => $post->id,
            'role' => 'user',
        ]);

        $this->actingAs($user);

        $response = $this->from(route('home.index'))
            ->get(route('user.permission'));

        $response->assertStatus(200);
        $response->assertViewIs('user.permission');
        $response->assertViewHas('data');
    }

    public function test_non_admin_cannot_access_user_management(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        $response = $this->from(route('company.list'))
            ->get(route('company.user.create', $this->company));

        $response->assertStatus(403);
    }
}
