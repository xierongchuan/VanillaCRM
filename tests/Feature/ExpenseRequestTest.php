<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ExpenseRequestTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $adminUser;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test company
        $this->company = Company::factory()->create();

        // Create an admin user
        $this->adminUser = User::factory()->create([
            'role' => 'admin'
        ]);
    }

    /** @test */
    public function admin_can_view_expense_requests_dashboard()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('company.expense.requests', $this->company));

        $response->assertStatus(200);
        $response->assertViewIs('company.expense_requests_dashboard');
    }

    /** @test */
    public function non_admin_cannot_view_expense_requests_dashboard()
    {
        $regularUser = User::factory()->create([
            'role' => 'user'
        ]);

        $response = $this->actingAs($regularUser)
            ->get(route('company.expense.requests', $this->company));

        $response->assertStatus(302); // Redirected
    }

    /** @test */
    public function guest_cannot_view_expense_requests_dashboard()
    {
        $response = $this->get(route('company.expense.requests', $this->company));

        $response->assertStatus(302); // Redirected to login
    }

    /** @test */
    public function it_returns_error_when_no_token_configured()
    {
        // Temporarily clear the token
        $originalToken = env('VANILLAFLOW_API_TOKEN');
        putenv('VANILLAFLOW_API_TOKEN=');

        $response = $this->actingAs($this->adminUser)
            ->get(route('company.expenses.pending', $this->company));

        $response->assertStatus(500);
        $response->assertJson(['error' => 'API token not configured']);

        // Restore the token
        if ($originalToken) {
            putenv("VANILLAFLOW_API_TOKEN={$originalToken}");
        }
    }
}
