<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Department;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeAndThemeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_home_page(): void
    {
        $response = $this->get(route('home.index'));

        $response->assertStatus(200);
        $response->assertViewIs('home');
    }

    public function test_admin_can_view_admin_home_page(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $response = $this->get(route('home.index'));

        $response->assertStatus(200);
        $response->assertViewIs('home');
        $response->assertViewHas('companies');
    }

    public function test_user_can_view_user_home_page(): void
    {
        $company = Company::factory()->create();
        $department = Department::factory()->create([
            'com_id' => $company->id,
        ]);
        $post = Post::factory()->create([
            'com_id' => $company->id,
            'dep_id' => $department->id,
        ]);

        $user = User::factory()->create([
            'role' => 'user',
            'com_id' => $company->id,
            'dep_id' => $department->id,
            'post_id' => $post->id,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('home.index'));

        $response->assertStatus(200);
        $response->assertViewIs('home');
        $response->assertViewHas('company');
        $response->assertViewHas('data');
    }

    public function test_can_switch_to_light_theme(): void
    {
        $response = $this->get(route('theme.switch', ['name' => 'light']));

        $response->assertRedirect();
        $response->assertSessionHas('theme', 'light');
    }

    public function test_can_switch_to_dark_theme(): void
    {
        $response = $this->get(route('theme.switch', ['name' => 'dark']));

        $response->assertRedirect();
        $response->assertSessionHas('theme', 'dark');
    }

    public function test_cannot_switch_to_invalid_theme(): void
    {
        $response = $this->get(route('theme.switch', ['name' => 'invalid']));

        $response->assertSessionMissing('theme');
    }

    public function test_theme_persists_in_session(): void
    {
        $this->withSession(['theme' => 'dark']);

        $this->assertEquals('dark', session('theme'));
    }
}
