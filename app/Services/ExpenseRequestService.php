<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExpenseRequestService
{
    private string $apiUrl;
    private string $apiToken;

    public function __construct()
    {
        $this->apiUrl = config('services.vanillaflow.api_url');
        $this->apiToken = config('services.vanillaflow.api_token');
    }

    /**
     * Fetch expense requests from VanillaFlow API
     */
    public function fetchExpenseRequests(string $status, $companyId, array $params = [])
    {
        try {
            $response = Http::withToken($this->apiToken)
                ->get("{$this->apiUrl}/companies/{$companyId}/expenses/{$status}", $params);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to fetch expense requests',
                'status' => $response->status()
            ];
        } catch (\Exception $e) {
            Log::error("Error fetching {$status} expense requests: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Internal server error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get a specific expense request details
     */
    public function getExpenseRequestDetails(int $companyId, int $requestId)
    {
        try {
            $response = Http::withToken($this->apiToken)
                ->get("{$this->apiUrl}/companies/{$companyId}/expenses/{$requestId}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to fetch expense request details',
                'status' => $response->status()
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching expense request details: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Internal server error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Export expenses to CSV format
     */
    public function exportExpenses(string $status, int $companyId, array $params): array
    {
        $result = $this->fetchExpenseRequests($status, $companyId, $params);

        if (!$result['success']) {
            return $result;
        }

        $data = $result['data']['data'] ?? [];

        if (empty($data)) {
            return [
                'success' => false,
                'error' => 'No data to export'
            ];
        }

        // Generate CSV content
        $csvContent = $this->generateCsvContent($data, $status);

        return [
            'success' => true,
            'csv' => $csvContent,
            'filename' => "expense_requests_{$status}_company_{$companyId}_" . date('Y-m-d_H-i-s') . ".csv"
        ];
    }

    /**
     * Generate CSV content from expense data
     */
    private function generateCsvContent(array $data, string $status): string
    {
        if (empty($data)) {
            return "No data available";
        }

        // Create CSV header based on status
        $headers = $this->getCsvHeaders($status);
        $csv = implode(',', $headers) . "\n";

        // Add data rows
        foreach ($data as $item) {
            $row = $this->formatCsvRow($item, $status);
            $csv .= implode(',', $row) . "\n";
        }

        return $csv;
    }

    /**
     * Get CSV headers based on expense status
     */
    private function getCsvHeaders(string $status): array
    {
        $baseHeaders = ['ID', 'Date', 'Requester Name', 'Description', 'Amount'];

        switch ($status) {
            case 'approved':
                $baseHeaders[] = 'Status';
                break;
            case 'issued':
                $baseHeaders[] = 'Issuer Name';
                $baseHeaders[] = 'Issued Amount';
                break;
                // declined and pending don't have additional columns
        }

        return $baseHeaders;
    }

    /**
     * Format a CSV row based on expense data and status
     */
    private function formatCsvRow(array $item, string $status): array
    {
        $row = [
            $item['id'] ?? '',
            $item['date'] ?? '',
            '"' . ($item['requester_name'] ?? '') . '"',
            '"' . ($item['description'] ?? '') . '"',
            $item['amount'] ?? 0
        ];

        switch ($status) {
            case 'approved':
                $row[] = $item['status'] ?? '';
                break;
            case 'issued':
                $row[] = '"' . ($item['issuer_name'] ?? '') . '"';
                $row[] = $item['issued_amount'] ?? 0;
                break;
                // declined and pending don't have additional columns
        }

        // Escape commas and quotes in fields
        foreach ($row as $key => $value) {
            if (is_string($value)) {
                $row[$key] = '"' . str_replace('"', '""', $value) . '"';
            }
        }

        return $row;
    }
}
