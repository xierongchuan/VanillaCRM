<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Company;
use App\Models\Report;
use Illuminate\Http\Request;

class CashierReportService
{
    /**
     * Create or update a cashier report for a company
     *
     * @param Company $company
     * @param Request $request
     * @return Report
     */
    public function createOrUpdateReport(Company $company, Request $request): Report
    {
        $request->validate([
            'date' => 'required',
            'file' => 'required|file|max:51200',
            'oborot_plus' => 'nullable|numeric',
            'oborot_minus' => 'nullable|numeric',
            'nalichka' => 'nullable|numeric',
            'rs' => 'nullable|numeric',
            'plastic' => 'nullable|numeric',
            'skidki' => 'nullable|numeric',
        ]);

        // Save the uploaded file
        $fileName = $this->saveFile($request);

        $data = [
            'file' => $fileName,
            'oborot_plus' => $request->oborot_plus ? (int) $request->oborot_plus : null,
            'oborot_minus' => $request->oborot_minus ? (int) $request->oborot_minus : null,
            'saldo' => $request->oborot_plus - $request->oborot_minus,
            'nalichka' => $request->nalichka ? (int) $request->nalichka : null,
            'rs' => $request->rs ? (int) $request->rs : null,
            'plastic' => $request->plastic ? (int) $request->plastic : null,
            'skidki' => $request->skidki ? (int) $request->skidki : null,
        ];

        $forDate = date('Y-m-d H:i:s', strtotime($request->date));

        $reportModel = Report::where([
            'com_id' => $company->id,
            'for_date' => $forDate,
            'type' => 'report_cashier',
        ])->first();

        if ($reportModel) {
            // If record exists, update it
            $reportModel->data = json_encode($data);
            $reportModel->updated_at = now()->toDateTimeString();
            $reportModel->save();
        } else {
            // If record doesn't exist, create a new one
            $reportModel = Report::create([
                'com_id' => $company->id,
                'for_date' => $forDate,
                'type' => 'report_cashier',
                'data' => json_encode($data),
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ]);
        }

        return $reportModel;
    }

    /**
     * Save the uploaded file to storage
     *
     * @param Request $request
     * @return string
     */
    private function saveFile(Request $request): string
    {
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('public/cashier_reports', $fileName);

        return $fileName;
    }
}
