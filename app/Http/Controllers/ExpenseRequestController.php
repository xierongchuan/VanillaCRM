<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Services\ExpenseRequestService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ExpenseRequestController extends Controller
{
    private ExpenseRequestService $expenseService;

    public function __construct(ExpenseRequestService $expenseService)
    {
        $this->expenseService = $expenseService;
    }

    /**
     * Display the expense requests dashboard
     */
    public function index(Request $request, Company $company)
    {
        $companyId = $company->id;

        return view('company.expense_requests_dashboard', compact('companyId'));
    }

    /**
     * Save the VanillaFlow API token
     */
    public function saveToken(Request $request, Company $company)
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        // Save the token to the session
        Session::put('vanillaflow_token_' . $company->id, $request->input('token'));

        return response()->json(['success' => true]);
    }

    /**
     * Get pending expense requests
     */
    public function getPendingRequests(Request $request, Company $company)
    {
        return $this->fetchExpenseRequests('pending', $company->id, $request);
    }

    /**
     * Get approved expense requests
     */
    public function getApprovedRequests(Request $request, Company $company)
    {
        return $this->fetchExpenseRequests('approved', $company->id, $request);
    }

    /**
     * Get declined expense requests
     */
    public function getDeclinedRequests(Request $request, Company $company)
    {
        return $this->fetchExpenseRequests('declined', $company->id, $request);
    }

    /**
     * Get issued expense requests
     */
    public function getIssuedRequests(Request $request, Company $company)
    {
        return $this->fetchExpenseRequests('issued', $company->id, $request);
    }

    /**
     * Get a specific expense request details
     */
    public function getRequestDetails(Company $company, $requestId)
    {
        $result = $this->expenseService->getExpenseRequestDetails($company->id, $requestId);

        if ($result['success']) {
            return response()->json($result['data']);
        }

        return response()->json(['error' => $result['error']], $result['status'] ?? 500);
    }

    /**
     * Export expenses to CSV
     */
    public function exportExpenses(Request $request, Company $company, $status)
    {
        $queryParams = $this->buildQueryParams($request);

        $result = $this->expenseService->exportExpenses($status, $company->id, $queryParams);

        if ($result['success']) {
            return response($result['csv'])
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', "attachment; filename={$result['filename']}");
        }

        return response()->json(['error' => $result['error']], 500);
    }

    /**
     * Fetch expense requests from VanillaFlow API
     */
    private function fetchExpenseRequests(string $status, $companyId, Request $request)
    {
        $queryParams = $this->buildQueryParams($request);

        $result = $this->expenseService->fetchExpenseRequests($status, $companyId, $queryParams);

        if ($result['success']) {
            return response()->json($result['data']);
        }

        return response()->json(['error' => $result['error']], $result['status'] ?? 500);
    }

    /**
     * Build query parameters for API requests
     */
    private function buildQueryParams(Request $request): array
    {
        $params = [];

        if ($request->has('per_page')) {
            $params['per_page'] = $request->query('per_page');
        }

        if ($request->has('page')) {
            $params['page'] = $request->query('page');
        }

        if ($request->has('sort_by')) {
            $params['sort_by'] = $request->query('sort_by');
        }

        if ($request->has('sort_direction')) {
            $params['sort_direction'] = $request->query('sort_direction');
        }

        // Date range filtering
        if ($request->has('date_from')) {
            $params['date_from'] = $request->query('date_from');
        }

        if ($request->has('date_to')) {
            $params['date_to'] = $request->query('date_to');
        }

        // Amount filtering
        if ($request->has('amount_min')) {
            $params['amount_min'] = $request->query('amount_min');
        }

        if ($request->has('amount_max')) {
            $params['amount_max'] = $request->query('amount_max');
        }

        return $params;
    }
}
