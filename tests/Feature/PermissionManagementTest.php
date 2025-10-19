<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Department;
use App\Models\Permission;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->company = Company::factory()->create();
    }

    public function test_admin_can_view_permission_create_form(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('company.permission.create', $this->company));

        $response->assertStatus(200);
        $response->assertViewIs('company.permission.create');
        $response->assertViewHas('company', $this->company);
    }

    public function test_admin_can_create_permission(): void
    {
        $this->actingAs($this->admin);

        $permissionData = [
            'name' => 'Test Permission',
            'value' => 'test_permission',
        ];

        $response = $this->from(route('company.permission.create', $this->company))
            ->post(
                route('company.permission.store', $this->company),
                $permissionData
            );

        $response->assertRedirect(route('company.list'));

        $this->assertDatabaseHas('permissions', [
            'com_id' => $this->company->id,
            'name' => 'Test Permission',
            'value' => 'test_permission',
        ]);
    }

    public function test_permission_name_is_required(): void
    {
        $this->actingAs($this->admin);

        $response = $this->from(route('company.permission.create', $this->company))
            ->post(route('company.permission.store', $this->company), [
                'name' => '',
                'value' => 'test_value',
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_permission_value_is_required(): void
    {
        $this->actingAs($this->admin);

        $response = $this->from(route('company.permission.create', $this->company))
            ->post(route('company.permission.store', $this->company), [
                'name' => 'Test Permission',
                'value' => '',
            ]);

        $response->assertSessionHasErrors('value');
    }

    public function test_permission_name_must_be_at_least_3_characters(): void
    {
        $this->actingAs($this->admin);

        $response = $this->from(route('company.permission.create', $this->company))
            ->post(route('company.permission.store', $this->company), [
                'name' => 'AB',
                'value' => 'test_value',
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_permission_name_must_not_exceed_30_characters(): void
    {
        $this->actingAs($this->admin);

        $response = $this->from(route('company.permission.create', $this->company))
            ->post(route('company.permission.store', $this->company), [
            'name' => str_repeat('A', 31),
            'value' => 'test_value',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_permission_value_must_be_at_least_3_characters(): void
    {
        $this->actingAs($this->admin);

        $response = $this->from(route('company.permission.create', $this->company))
            ->post(route('company.permission.store', $this->company), [
                'name' => 'Test Permission',
                'value' => 'ab',
            ]);

        $response->assertSessionHasErrors('value');
    }

    public function test_permission_value_must_not_exceed_20_characters(): void
    {
        $this->actingAs($this->admin);

        $response = $this->from(route('company.permission.create', $this->company))
            ->post(route('company.permission.store', $this->company), [
                'name' => 'Test Permission',
                'value' => str_repeat('a', 21),
            ]);

        $response->assertSessionHasErrors('value');
    }

    public function test_permission_value_must_match_regex_pattern(): void
    {
        $this->actingAs($this->admin);

        $response = $this->from(route('company.permission.create', $this->company))
            ->post(route('company.permission.store', $this->company), [
                'name' => 'Test Permission',
                'value' => 'Invalid-Value',
            ]);

        $response->assertSessionHasErrors('value');
    }

    public function test_permission_value_must_be_unique_per_company(): void
    {
        $this->actingAs($this->admin);

        Permission::factory()->create([
            'com_id' => $this->company->id,
            'value' => 'existing_value',
        ]);

        $response = $this->from(route('company.permission.create', $this->company))
            ->post(route('company.permission.store', $this->company), [
                'name' => 'Test Permission',
                'value' => 'existing_value',
            ]);

        $response->assertRedirect(route('company.list'));
        $response->assertSessionHasErrors();
    }

    public function test_admin_can_view_permission_update_form(): void
    {
        $this->actingAs($this->admin);

        $permission = Permission::factory()->create([
            'com_id' => $this->company->id,
        ]);

        $response = $this->get(route('company.permission.update', [
            'company' => $this->company,
            'permission' => $permission,
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('company.permission.update');
        $response->assertViewHas('company', $this->company);
        $response->assertViewHas('permission', $permission);
    }

    public function test_admin_can_update_permission(): void
    {
        $this->actingAs($this->admin);

        $permission = Permission::factory()->create([
            'com_id' => $this->company->id,
            'name' => 'Old Name',
        ]);

        $response = $this->from(route('company.permission.update', [
            'company' => $this->company,
            'permission' => $permission,
        ]))
            ->post(route('company.permission.modify', [
                'company' => $this->company,
                'permission' => $permission,
            ]), [
                'name' => 'New Permission Name',
            ]);

        $response->assertRedirect(route('company.list'));

        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'name' => 'New Permission Name',
        ]);
    }

    public function test_admin_can_delete_permission_not_in_use(): void
    {
        $this->actingAs($this->admin);

        $permission = Permission::factory()->create([
            'com_id' => $this->company->id,
        ]);

        $response = $this->get(route('company.permission.delete', [
            'company' => $this->company,
            'permission' => $permission,
        ]));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Право успешно удалено');

        $this->assertDatabaseMissing('permissions', [
            'id' => $permission->id,
        ]);
    }

    public function test_admin_cannot_delete_permission_in_use(): void
    {
        $this->actingAs($this->admin);

        $permission = Permission::factory()->create([
            'com_id' => $this->company->id,
        ]);

        $department = Department::factory()->create([
            'com_id' => $this->company->id,
        ]);

        Post::factory()->create([
            'com_id' => $this->company->id,
            'dep_id' => $department->id,
            'permission' => json_encode([$permission->id]),
        ]);

        $response = $this->get(route('company.permission.delete', [
            'company' => $this->company,
            'permission' => $permission,
        ]));

        $response->assertRedirect();
        $response->assertSessionHasErrors();

        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
        ]);
    }

    public function test_non_admin_cannot_access_permission_management(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        $response = $this->from(route('company.list'))
            ->get(route('company.permission.create', $this->company));

        $response->assertStatus(403);
    }
}
