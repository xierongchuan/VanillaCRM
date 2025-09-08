<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\User;
use App\Models\Company;
use App\Models\Department;
use App\Models\Post;
use App\Models\Permission;
use App\Services\PermissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PermissionService $permissionService;
    protected Company $company;
    protected Department $department;
    protected Permission $salesConsultantPermission;
    protected Permission $managerPermission;
    protected Post $salesPost;
    protected Post $managerPost;
    protected User $salesUser;
    protected User $managerUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->permissionService = new PermissionService();

        // Create test data
        $this->company = Company::factory()->create([
            'name' => 'Test Company',
        ]);

        $this->department = Department::factory()->create([
            'com_id' => $this->company->id,
            'name' => 'Test Department',
        ]);

        // Create permissions
        $this->salesConsultantPermission = Permission::factory()->create([
            'com_id' => $this->company->id,
            'name' => 'Sales Consultant',
            'value' => 'sales_consultant',
        ]);

        $this->managerPermission = Permission::factory()->create([
            'com_id' => $this->company->id,
            'name' => 'Manager',
            'value' => 'manager',
        ]);

        // Create posts with different permissions
        $this->salesPost = Post::factory()->create([
            'com_id' => $this->company->id,
            'dep_id' => $this->department->id,
            'name' => 'Sales Consultant Post',
            'permission' => json_encode([$this->salesConsultantPermission->id]),
        ]);

        $this->managerPost = Post::factory()->create([
            'com_id' => $this->company->id,
            'dep_id' => $this->department->id,
            'name' => 'Manager Post',
            'permission' => json_encode([$this->managerPermission->id]),
        ]);

        // Create users
        $this->salesUser = User::factory()->create([
            'com_id' => $this->company->id,
            'dep_id' => $this->department->id,
            'post_id' => $this->salesPost->id,
            'login' => 'salesuser',
            'password' => bcrypt('password123'),
            'status' => 'active',
        ]);

        $this->managerUser = User::factory()->create([
            'com_id' => $this->company->id,
            'dep_id' => $this->department->id,
            'post_id' => $this->managerPost->id,
            'login' => 'manageruser',
            'password' => bcrypt('password123'),
            'status' => 'active',
        ]);
    }

    public function testPostHasPermissionReturnsTrueWhenPermissionExists()
    {
        $result = $this->permissionService->postHasPermission($this->salesPost, 'sales_consultant');
        $this->assertTrue($result);
    }

    public function testPostHasPermissionReturnsFalseWhenPermissionDoesNotExist()
    {
        $result = $this->permissionService->postHasPermission($this->salesPost, 'manager');
        $this->assertFalse($result);
    }

    public function testPostHasPermissionReturnsFalseWhenPermissionDoesNotExistInDatabase()
    {
        $result = $this->permissionService->postHasPermission($this->salesPost, 'nonexistent_permission');
        $this->assertFalse($result);
    }

    public function testGetUsersWithPermissionReturnsCorrectUsers()
    {
        $users = $this->permissionService->getUsersWithPermission('sales_consultant', $this->department->id);

        $this->assertCount(1, $users);
        $this->assertEquals($this->salesUser->id, $users->first()->id);
    }

    public function testGetUsersWithPermissionReturnsEmptyCollectionWhenNoUsersHavePermission()
    {
        // Create a department with no users having the manager permission
        $otherDepartment = Department::factory()->create([
            'com_id' => $this->company->id,
            'name' => 'Other Department',
        ]);

        $users = $this->permissionService->getUsersWithPermission('manager', $otherDepartment->id);

        $this->assertCount(0, $users);
    }

    public function testGetUsersWithPermissionReturnsEmptyCollectionWhenPermissionDoesNotExist()
    {
        $users = $this->permissionService->getUsersWithPermission('nonexistent_permission', $this->department->id);

        $this->assertCount(0, $users);
    }

    public function testGetUsersWithPermissionOnlyReturnsActiveUsers()
    {
        // Create an inactive user with sales consultant permission
        $inactiveUser = User::factory()->create([
            'com_id' => $this->company->id,
            'dep_id' => $this->department->id,
            'post_id' => $this->salesPost->id,
            'login' => 'inactivesalesuser',
            'password' => bcrypt('password123'),
            'status' => 'inactive',
        ]);

        $users = $this->permissionService->getUsersWithPermission('sales_consultant', $this->department->id);

        $this->assertCount(1, $users);
        $this->assertEquals($this->salesUser->id, $users->first()->id);
        $this->assertNotContains($inactiveUser->id, $users->pluck('id'));
    }
}
