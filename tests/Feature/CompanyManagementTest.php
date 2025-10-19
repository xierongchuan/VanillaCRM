<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Department;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
    }

    public function test_admin_can_view_company_list(): void
    {
        $this->actingAs($this->admin);

        $companies = Company::factory()->count(3)->create();

        $response = $this->get(route('company.list'));

        $response->assertStatus(200);
        $response->assertViewIs('company.list');
        $response->assertViewHas('companies');
    }

    public function test_admin_can_view_company_create_form(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('company.create'));

        $response->assertStatus(200);
        $response->assertViewIs('company.create');
    }

    public function test_admin_can_create_company(): void
    {
        $this->actingAs($this->admin);

        $companyData = [
            'name' => 'Test Company',
            'data' => 'Some additional data',
        ];

        $response = $this->from(route('company.create'))
            ->post(route('company.store'), $companyData);

        $response->assertRedirect(route('company.list'));

        $this->assertDatabaseHas('companies', [
            'name' => 'Test Company',
        ]);
    }

    public function test_company_name_must_be_unique(): void
    {
        $this->actingAs($this->admin);

        Company::factory()->create(['name' => 'Existing Company']);

        $response = $this->from(route('company.create'))
            ->post(route('company.store'), [
                'name' => 'Existing Company',
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_company_name_must_be_at_least_3_characters(): void
    {
        $this->actingAs($this->admin);

        $response = $this->from(route('company.create'))
            ->post(route('company.store'), [
                'name' => 'AB',
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_company_name_must_not_exceed_20_characters(): void
    {
        $this->actingAs($this->admin);

        $response = $this->from(route('company.create'))
            ->post(route('company.store'), [
                'name' => str_repeat('A', 21),
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_admin_can_view_company_update_form(): void
    {
        $this->actingAs($this->admin);

        $company = Company::factory()->create();

        $response = $this->get(route('company.update', $company));

        $response->assertStatus(200);
        $response->assertViewIs('company.update');
        $response->assertViewHas('company', $company);
    }

    public function test_admin_can_update_company(): void
    {
        $this->actingAs($this->admin);

        $company = Company::factory()->create(['name' => 'Old Name']);

        $response = $this->from(route('company.update', $company))
            ->post(route('company.modify', $company), [
                'name' => 'New Company Name',
            ]);

        $response->assertRedirect(route('company.list'));
        $response->assertSessionHas('success', 'Successfully updated');

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => 'New Company Name',
        ]);
    }

    public function test_admin_can_delete_company_without_dependencies(): void
    {
        $this->actingAs($this->admin);

        $company = Company::factory()->create();

        $response = $this->get(route('company.delete', $company));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Компания успешно удалена');

        $this->assertDatabaseMissing('companies', [
            'id' => $company->id,
        ]);
    }

    public function test_admin_cannot_delete_company_with_departments(): void
    {
        $this->actingAs($this->admin);

        $company = Company::factory()->create();
        Department::factory()->create(['com_id' => $company->id]);

        $response = $this->get(route('company.delete', $company));

        $response->assertRedirect();
        $response->assertSessionHasErrors();

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
        ]);
    }

    public function test_admin_cannot_delete_company_with_permissions(): void
    {
        $this->actingAs($this->admin);

        $company = Company::factory()->create();
        Permission::factory()->create(['com_id' => $company->id]);

        $response = $this->get(route('company.delete', $company));

        $response->assertRedirect();
        $response->assertSessionHasErrors();

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
        ]);
    }

    public function test_non_admin_cannot_access_company_list(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        $response = $this->get(route('company.list'));

        $response->assertRedirect(route('auth.sign_in'));
    }

    public function test_guest_cannot_access_company_list(): void
    {
        $response = $this->get(route('company.list'));

        $response->assertRedirect(route('auth.sign_in'));
    }
}
