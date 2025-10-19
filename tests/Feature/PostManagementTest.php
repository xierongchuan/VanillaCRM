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

class PostManagementTest extends TestCase
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

    public function test_admin_can_view_post_index(): void
    {
        $this->actingAs($this->admin);

        $post = Post::factory()->create([
            'com_id' => $this->company->id,
            'dep_id' => $this->department->id,
        ]);

        $response = $this->get(route('company.department.post.index', [
            'company' => $this->company,
            'department' => $this->department,
            'post' => $post,
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('company.department.post.index');
        $response->assertViewHas('company', $this->company);
        $response->assertViewHas('department', $this->department);
        $response->assertViewHas('post');
    }

    public function test_admin_can_view_post_create_form(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('company.department.post.create', [
            'company' => $this->company,
            'department' => $this->department,
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('company.department.post.create');
        $response->assertViewHas('company', $this->company);
        $response->assertViewHas('department', $this->department);
        $response->assertViewHas('permissions');
    }

    public function test_admin_can_create_post_without_permissions(): void
    {
        $this->actingAs($this->admin);

        $postData = [
            'name' => 'Test Post',
        ];

        $response = $this->from(route('company.department.post.create', [
            'company' => $this->company,
            'department' => $this->department,
        ]))
            ->post(route('company.department.post.store', [
                'company' => $this->company,
                'department' => $this->department,
            ]), $postData);

        $response->assertRedirect(route('company.department.index', [
            'company' => $this->company,
            'department' => $this->department,
        ]));
        $response->assertSessionHas('success', 'Successfully created');

        $this->assertDatabaseHas('posts', [
            'com_id' => $this->company->id,
            'dep_id' => $this->department->id,
            'name' => 'Test Post',
        ]);
    }

    public function test_admin_can_create_post_with_permissions(): void
    {
        $this->actingAs($this->admin);

        $permission = Permission::factory()->create([
            'com_id' => $this->company->id,
        ]);

        $postData = [
            'name' => 'Test Post',
            'permission' => [$permission->id],
        ];

        $response = $this->from(route('company.department.post.create', [
            'company' => $this->company,
            'department' => $this->department,
        ]))
            ->post(route('company.department.post.store', [
                'company' => $this->company,
                'department' => $this->department,
            ]), $postData);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Successfully created');

        $this->assertDatabaseHas('posts', [
            'com_id' => $this->company->id,
            'dep_id' => $this->department->id,
            'name' => 'Test Post',
        ]);
    }

    public function test_post_name_is_required(): void
    {
        $this->actingAs($this->admin);

        $response = $this->from(route('company.department.post.create', [
            'company' => $this->company,
            'department' => $this->department,
        ]))
            ->post(route('company.department.post.store', [
                'company' => $this->company,
                'department' => $this->department,
            ]), [
                'name' => '',
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_post_name_must_be_at_least_3_characters(): void
    {
        $this->actingAs($this->admin);

        $response = $this->from(route('company.department.post.create', [
            'company' => $this->company,
            'department' => $this->department,
        ]))
            ->post(route('company.department.post.store', [
                'company' => $this->company,
                'department' => $this->department,
            ]), [
                'name' => 'AB',
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_post_name_must_not_exceed_30_characters(): void
    {
        $this->actingAs($this->admin);

        $response = $this->from(route('company.department.post.create', [
            'company' => $this->company,
            'department' => $this->department,
        ]))
            ->post(route('company.department.post.store', [
                'company' => $this->company,
                'department' => $this->department,
            ]), [
                'name' => str_repeat('A', 31),
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_admin_can_view_post_update_form(): void
    {
        $this->actingAs($this->admin);

        $post = Post::factory()->create([
            'com_id' => $this->company->id,
            'dep_id' => $this->department->id,
        ]);

        $response = $this->get(route('company.department.post.update', [
            'company' => $this->company,
            'department' => $this->department,
            'post' => $post,
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('company.department.post.update');
        $response->assertViewHas('company', $this->company);
        $response->assertViewHas('department', $this->department);
        $response->assertViewHas('post', $post);
        $response->assertViewHas('permissions');
    }

    public function test_admin_can_update_post(): void
    {
        $this->actingAs($this->admin);

        $post = Post::factory()->create([
            'com_id' => $this->company->id,
            'dep_id' => $this->department->id,
            'name' => 'Old Name',
        ]);

        $response = $this->from(route('company.department.post.update', [
            'company' => $this->company,
            'department' => $this->department,
            'post' => $post,
        ]))
            ->post(route('company.department.post.modify', [
                'company' => $this->company,
                'department' => $this->department,
                'post' => $post,
            ]), [
                'name' => 'New Post Name',
            ]);

        $response->assertRedirect(route('company.department.index', [
            'company' => $this->company,
            'department' => $this->department,
        ]));
        $response->assertSessionHas('success', 'Successfully updated');

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'name' => 'New Post Name',
        ]);
    }

    public function test_admin_can_delete_post_without_users(): void
    {
        $this->actingAs($this->admin);

        $post = Post::factory()->create([
            'com_id' => $this->company->id,
            'dep_id' => $this->department->id,
        ]);

        $response = $this->get(route('company.department.post.delete', [
            'company' => $this->company,
            'department' => $this->department,
            'post' => $post,
        ]));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Должность успешно удалена');

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
        ]);
    }

    public function test_admin_cannot_delete_post_with_users(): void
    {
        $this->actingAs($this->admin);

        $post = Post::factory()->create([
            'com_id' => $this->company->id,
            'dep_id' => $this->department->id,
        ]);

        User::factory()->create([
            'com_id' => $this->company->id,
            'dep_id' => $this->department->id,
            'post_id' => $post->id,
        ]);

        $response = $this->get(route('company.department.post.delete', [
            'company' => $this->company,
            'department' => $this->department,
            'post' => $post,
        ]));

        $response->assertRedirect();
        $response->assertSessionHasErrors();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
        ]);
    }

    public function test_non_admin_cannot_access_post_management(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        $post = Post::factory()->create([
            'com_id' => $this->company->id,
            'dep_id' => $this->department->id,
        ]);

        $response = $this->get(route('company.department.post.index', [
            'company' => $this->company,
            'department' => $this->department,
            'post' => $post,
        ]));

        $response->assertRedirect(route('auth.sign_in'));
    }
}
