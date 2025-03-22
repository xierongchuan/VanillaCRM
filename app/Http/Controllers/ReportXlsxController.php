<?php

namespace App\Http\Controllers;

use App\Services\ReportXlsxService;
use Illuminate\Http\Request;

class ReportXlsxController extends Controller
{
    private $reportXlsxService;

    public function __construct(ReportXlsxService $reportXlsxService)
    {
        $this->reportXlsxService = $reportXlsxService;
    }

    public function report_xlsx(Request $request)
    {
        try {
            $sheetData = $this->reportXlsxService->generateReport($request);

            // Дальнейшая обработка $sheetData
            return redirect()->route('home.index')->with('success', 'Отчёт успешно загружен.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
}
