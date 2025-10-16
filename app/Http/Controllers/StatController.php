<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\ReportXlsxService;

class StatController extends Controller
{
    public function index()
    {
        $companies = Company::all();
        $reportService = new ReportXlsxService();

        $sales = $reportService->getSalesDataNoSum($companies);
        $growthStatistics = $reportService->getGrowthStatistics($companies);

        // Create a map of company ID => company name for Vue component
        $companyNames = $companies->pluck('name', 'id')->toArray();

        return view('company.stat', compact('sales', 'growthStatistics', 'companyNames'));
    }
}
