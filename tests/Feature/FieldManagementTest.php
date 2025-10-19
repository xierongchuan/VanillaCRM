<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Field;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FieldManagementTest extends TestCase
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

    public function test_admin_can_view_field_create_form(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('company.field.create', $this->company));

        $response->assertStatus(200);
        $response->assertViewIs('company.field.create');
        $response->assertViewHas('company', $this->company);
    }

    public function test_admin_can_create_field(): void
    {
        $this->actingAs($this->admin);

        $fieldData = [
            'title' => 'Test Field',
            'link' => 'https://example.com',
        ];

        $response = $this->post(
            route('company.field.store', $this->company),
            $fieldData
        );

        $response->assertRedirect(route('company.list', $this->company));
        $response->assertSessionHas('success', 'Successfully created');

        $this->assertDatabaseHas('custom_fields', [
            'com_id' => $this->company->id,
            'title' => 'Test Field',
            'link' => 'https://example.com',
        ]);
    }

    public function test_admin_can_create_field_without_link(): void
    {
        $this->actingAs($this->admin);

        $fieldData = [
            'title' => 'Test Field',
        ];

        $response = $this->post(
            route('company.field.store', $this->company),
            $fieldData
        );

        $response->assertRedirect(route('company.list', $this->company));
        $response->assertSessionHas('success', 'Successfully created');

        $this->assertDatabaseHas('custom_fields', [
            'com_id' => $this->company->id,
            'title' => 'Test Field',
        ]);
    }

    public function test_field_title_is_required(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('company.field.store', $this->company), [
            'title' => '',
            'link' => 'https://example.com',
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_field_title_must_be_at_least_3_characters(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('company.field.store', $this->company), [
            'title' => 'AB',
            'link' => 'https://example.com',
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_field_title_must_not_exceed_30_characters(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('company.field.store', $this->company), [
            'title' => str_repeat('A', 31),
            'link' => 'https://example.com',
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_admin_can_view_field_update_form(): void
    {
        $this->actingAs($this->admin);

        $field = new Field();
        $field->com_id = $this->company->id;
        $field->title = 'Test Field';
        $field->link = 'https://example.com';
        $field->save();

        $response = $this->get(route('company.field.update', [
            'company' => $this->company,
            'field' => $field,
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('company.field.update');
        $response->assertViewHas('company', $this->company);
        $response->assertViewHas('field', $field);
    }

    public function test_admin_can_update_field(): void
    {
        $this->actingAs($this->admin);

        $field = new Field();
        $field->com_id = $this->company->id;
        $field->title = 'Old Title';
        $field->link = 'https://old-example.com';
        $field->save();

        $response = $this->post(route('company.field.modify', [
            'company' => $this->company,
            'field' => $field,
        ]), [
            'title' => 'New Title',
            'link' => 'https://new-example.com',
        ]);

        $response->assertRedirect(route('company.list', $this->company));
        $response->assertSessionHas('success', 'Successfully updated');

        $this->assertDatabaseHas('custom_fields', [
            'id' => $field->id,
            'title' => 'New Title',
            'link' => 'https://new-example.com',
        ]);
    }

    public function test_admin_can_delete_field(): void
    {
        $this->actingAs($this->admin);

        $field = new Field();
        $field->com_id = $this->company->id;
        $field->title = 'Test Field';
        $field->link = 'https://example.com';
        $field->save();

        $response = $this->get(route('company.field.delete', [
            'company' => $this->company,
            'field' => $field,
        ]));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Ссылка успешно удалена');

        $this->assertDatabaseMissing('custom_fields', [
            'id' => $field->id,
        ]);
    }

    public function test_non_admin_cannot_access_field_management(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        $response = $this->get(route('company.field.create', $this->company));

        $response->assertRedirect(route('auth.sign_in'));
    }
}
