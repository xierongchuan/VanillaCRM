<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Report;
use App\Services\CashierReportService;
use Illuminate\Http\Request;

class CashierReportController extends Controller
{
    protected $cashierReportService;

    public function __construct(CashierReportService $cashierReportService)
    {
        $this->cashierReportService = $cashierReportService;
    }

    public function report(Company $company, Request $request)
    {
        try {
            $this->cashierReportService->createOrUpdateReport($company, $request);

            return redirect()->route('home.index')->with('success', 'Отчёт успешно загружен.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ошибка при сохранении отчёта: ' . $e->getMessage());
        }
    }
}
