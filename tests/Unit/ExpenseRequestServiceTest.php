<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ExpenseRequestService;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class ExpenseRequestServiceTest extends TestCase
{
    protected ExpenseRequestService $expenseService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->expenseService = new ExpenseRequestService();
    }

    /** @test */
    public function it_returns_error_when_no_token_configured()
    {
        // Temporarily clear the token
        $originalToken = env('VANILLAFLOW_API_TOKEN');
        putenv('VANILLAFLOW_API_TOKEN=');

        $result = $this->expenseService->fetchExpenseRequests('pending', 1, []);

        $this->assertFalse($result['success']);
        $this->assertEquals('API token not configured', $result['error']);

        // Restore the token
        if ($originalToken) {
            putenv("VANILLAFLOW_API_TOKEN={$originalToken}");
        }
    }

    /** @test */
    public function it_can_be_instantiated()
    {
        $this->assertInstanceOf(ExpenseRequestService::class, $this->expenseService);
    }
}
