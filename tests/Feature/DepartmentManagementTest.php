<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Department;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentManagementTest extends TestCase
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

    public function test_admin_can_view_department_index(): void
    {
        $this->actingAs($this->admin);

        $department = Department::factory()->create([
            'com_id' => $this->company->id,
        ]);

        $response = $this->get(route('company.department.index', [
            'company' => $this->company,
            'department' => $department,
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('company.department.index');
        $response->assertViewHas('company', $this->company);
        $response->assertViewHas('department');
    }

    public function test_admin_can_view_department_create_form(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('company.department.create', $this->company));

        $response->assertStatus(200);
        $response->assertViewIs('company.department.create');
        $response->assertViewHas('company', $this->company);
    }

    public function test_admin_can_create_department(): void
    {
        $this->actingAs($this->admin);

        $departmentData = [
            'name' => 'Test Department',
        ];

        $response = $this->post(
            route('company.department.store', $this->company),
            $departmentData
        );

        $response->assertRedirect(route('company.list'));

        $this->assertDatabaseHas('departments', [
            'com_id' => $this->company->id,
            'name' => 'Test Department',
        ]);
    }

    public function test_department_name_is_required(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('company.department.store', $this->company), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_department_name_must_be_at_least_3_characters(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('company.department.store', $this->company), [
            'name' => 'AB',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_department_name_must_not_exceed_20_characters(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('company.department.store', $this->company), [
            'name' => str_repeat('A', 21),
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_admin_can_view_department_update_form(): void
    {
        $this->actingAs($this->admin);

        $department = Department::factory()->create([
            'com_id' => $this->company->id,
        ]);

        $response = $this->get(route('company.department.update', [
            'company' => $this->company,
            'department' => $department,
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('company.department.update');
        $response->assertViewHas('company', $this->company);
        $response->assertViewHas('department', $department);
    }

    public function test_admin_can_update_department(): void
    {
        $this->actingAs($this->admin);

        $department = Department::factory()->create([
            'com_id' => $this->company->id,
            'name' => 'Old Name',
        ]);

        $response = $this->post(route('company.department.modify', [
            'company' => $this->company,
            'department' => $department,
        ]), [
            'name' => 'New Department Name',
        ]);

        $response->assertRedirect(route('company.list'));

        $this->assertDatabaseHas('departments', [
            'id' => $department->id,
            'name' => 'New Department Name',
        ]);
    }

    public function test_admin_can_get_department_posts_as_json(): void
    {
        $this->actingAs($this->admin);

        $department = Department::factory()->create([
            'com_id' => $this->company->id,
        ]);

        $posts = Post::factory()->count(3)->create([
            'com_id' => $this->company->id,
            'dep_id' => $department->id,
        ]);

        $response = $this->post(route('company.department.posts', [
            'company' => $this->company,
            'department' => $department,
        ]));

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    public function test_admin_can_delete_department_without_posts(): void
    {
        $this->actingAs($this->admin);

        $department = Department::factory()->create([
            'com_id' => $this->company->id,
        ]);

        $response = $this->get(route('company.department.delete', [
            'company' => $this->company,
            'department' => $department,
        ]));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Департамент успешно удален');

        $this->assertDatabaseMissing('departments', [
            'id' => $department->id,
        ]);
    }

    public function test_admin_cannot_delete_department_with_posts(): void
    {
        $this->actingAs($this->admin);

        $department = Department::factory()->create([
            'com_id' => $this->company->id,
        ]);

        Post::factory()->create([
            'com_id' => $this->company->id,
            'dep_id' => $department->id,
        ]);

        $response = $this->get(route('company.department.delete', [
            'company' => $this->company,
            'department' => $department,
        ]));

        $response->assertRedirect();
        $response->assertSessionHasErrors();

        $this->assertDatabaseHas('departments', [
            'id' => $department->id,
        ]);
    }

    public function test_non_admin_cannot_access_department_management(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        $department = Department::factory()->create([
            'com_id' => $this->company->id,
        ]);

        $response = $this->get(route('company.department.index', [
            'company' => $this->company,
            'department' => $department,
        ]));

        $response->assertStatus(403);
    }
}
