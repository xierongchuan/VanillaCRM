<?php

namespace App\Services;

use App\Enums\ReportXlsxRule;
use App\Models\Company;
use App\Models\Permission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Shared\Date as Date;

class ReportXlsxService
{

    public function generateReport(Request $request): array
    {
        $inputData = $request->all();
        $company = Company::find(Auth::user()->com_id);

        $workers = $this->getWorkers($company);

        foreach ($inputData as $key => $value) {
            if (preg_match('/^worker_name_(\d+)$/', $key, $matches)) {
                $workerNumber = $matches[1];
                $workerValue = $inputData['worker_sold_' . $workerNumber];
                $workerName = $inputData['worker_name_' . $workerNumber];

                $month = (int) $workerValue;

                if (!empty($workers) && !$request->close_month) {
                    $month = (int) @$workers[$workerNumber]->month;
                    $month += (int) $workerValue;
                }

                $workers[$workerNumber] = [
                    'name' => (string) $workerName,
                    'sold' => (int) $workerValue,
                    'month' => (int) $month,
                ];
            }
        }

        $sheetData = $this->getSheetData($request, $company, $workers);

        if ($request->close_month && !$request->hasFile('file')) {
            $this->closeMonth($company, $sheetData);
        }

        $this->validateRequest($request);
        $this->validateFileType($request);

        $permission = $this->getPermissionData($company);
        $this->fillSheetData($sheetData, $permission, $request, $company);

        return $sheetData;
    }

    private function getWorkers(Company $company): array
    {
        if (!empty($company->data)) {
            $comData = (array) json_decode($company->data);
            if ($comData['Clear Sales']) {
                return [];
            } else {
                return (array) ((array) json_decode($company->data))['Sales'];
            }
        }

        return [];
    }

    private function getSheetData(Request $request, Company $company, array $workers): array
    {
        return [
            'Дата' => date('Y-m-d H:i:s'),
            ReportXlsxRule::CONTRACTS => '',
            ReportXlsxRule::PAYMENT_QUANTITY => '',
            ReportXlsxRule::PAYMENT_SUM => '',
            ReportXlsxRule::ADDITIONAL_PAYMENT => '',
            ReportXlsxRule::LEASING => '',
            ReportXlsxRule::TOTAL => '',
            ReportXlsxRule::PLAN_QUANTITY => '',
            ReportXlsxRule::PLAN_SUM => '',
            ReportXlsxRule::ACTUAL_QUANTITY => '',
            ReportXlsxRule::ACTUAL_SUM => '',
            ReportXlsxRule::CONTRACTS_2 => '',
            ReportXlsxRule::CONVERSION_2 => '',
            ReportXlsxRule::PERCENT_OF_QUANTITY => '',
            ReportXlsxRule::PERCENT_OF_SUM => '',
            ReportXlsxRule::PAYMENT_3 => '',
            ReportXlsxRule::ADDITIONAL_PAYMENT_3 => '',
            ReportXlsxRule::LEASING_3 => '',
            ReportXlsxRule::BALANCE_3 => '',
            ReportXlsxRule::THROUGH_BANK_QTY_5 => '',
            ReportXlsxRule::THROUGH_BANK_SUM_5 => '',
            ReportXlsxRule::THROUGH_LEASING_QTY_5 => '',
            ReportXlsxRule::THROUGH_LEASING_SUM_5 => '',
            ReportXlsxRule::TOTAL_QTY_5 => '',
            ReportXlsxRule::SUM_5 => '',
            ReportXlsxRule::START_OF_REPORTS => '',
            ReportXlsxRule::END_OF_REPORTS => '',
            'Заметка' => $request->note,
            ReportXlsxRule::START_OF_SALES_LIST => '',
            'Sales' => $workers,
            'Last File' => '',
            'Clear Sales' => false,
        ];
    }

    private function closeMonth(Company $company, array &$sheetData): void
    {
        $fileName = (string) @((array) json_decode($company->data))['Last File'];
        $sourcePath = storage_path('app/public/tmp/' . $fileName);
        $destinationPath = storage_path('app/public/archive/' . $fileName);

        if (File::exists($sourcePath)) {
            File::copy($sourcePath, $destinationPath);

            $sheetData['Clear Sales'] = true;
            $company->data = json_encode($sheetData);
            $company->save();
        } else {
            throw new \Exception('Последний отчёт не найден.');
        }
    }

    private function validateRequest(Request $request): void
    {
        $request->validate([
            'file' => 'max:51200',
            'note' => 'max:5500',
        ]);
    }

    private function validateFileType(Request $request): void
    {
        if ($request->file('file')->getMimeType() !== "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
            throw new \Exception('Файл должен быть типа xlsx (Exel).');
        }
    }

    private function getPermissionData(Company $company): array
    {
        $permission = Permission::where('com_id', $company->id)
            ->where('value', 'report_xlsx')
            ->first();

        $lines = explode(PHP_EOL, $permission->data);
        $rule = [];
        foreach ($lines as $line) {
            if (trim($line)) {
                list($key, $value) = array_map('trim', explode('=', $line, 2));
                $rule[$key] = $value;
            }
        }

        return $rule;
    }

    private function fillSheetData(array &$sheetData, array $rule, Request $request, Company $company): void
    {
        $sheet = IOFactory::load($request->file('file'));
        $wsheet = $sheet->getActiveSheet();
        $dateM = '';

        foreach ($wsheet->getRowIterator() as $row) {
            $cellIterate = $row->getCellIterator();
            $cellIterate->setIterateOnlyExistingCells(true);
            foreach ($cellIterate as $cell) {
                $cellAddress = $cell->getCoordinate();
                $cellLetter = preg_replace('/[0-9]/', '', $cellAddress);
                $cellNum = preg_replace('/[A-z]/', '', $cellAddress);

                if (($cellLetter == 'A') && (int) $cell->getRow() >= (int) $rule[ReportXlsxRule::START_OF_REPORTS] && (int) $cell->getRow() <= (int) $rule[ReportXlsxRule::END_OF_REPORTS]) {
                    if (!$request->close_month) {
                        $date = date('d.m.Y', Date::excelToTimestamp((int) $cell->getValue()));
                        if ($date == date('d.m.Y')) {
                            $sheetData[ReportXlsxRule::CONTRACTS] = $wsheet->getCell($rule[ReportXlsxRule::CONTRACTS] . $cellNum)->getCalculatedValue();
                            $sheetData[ReportXlsxRule::PAYMENT_QUANTITY] = $wsheet->getCell($rule[ReportXlsxRule::PAYMENT_QUANTITY] . $cellNum)->getCalculatedValue();
                            $sheetData[ReportXlsxRule::PAYMENT_SUM] = $wsheet->getCell($rule[ReportXlsxRule::PAYMENT_SUM] . $cellNum)->getCalculatedValue();
                            $sheetData[ReportXlsxRule::ADDITIONAL_PAYMENT] = $wsheet->getCell($rule[ReportXlsxRule::ADDITIONAL_PAYMENT] . $cellNum)->getCalculatedValue();
                            $sheetData[ReportXlsxRule::LEASING] = $wsheet->getCell($rule[ReportXlsxRule::LEASING] . $cellNum)->getCalculatedValue();
                            $sheetData[ReportXlsxRule::TOTAL] = $wsheet->getCell($rule[ReportXlsxRule::TOTAL] . $cellNum)->getCalculatedValue();
                        }
                    } else {
                        if ((string) $wsheet->getCell($rule[ReportXlsxRule::CONTRACTS] . $cellNum)->getCalculatedValue() == '' || $cellNum >= (int) $rule[ReportXlsxRule::END_OF_REPORTS]) {
                            if ($sheetData[ReportXlsxRule::CONTRACTS] == '') {
                                $dateM = date('Y-m-d', Date::excelToTimestamp((int) $wsheet->getCell('A' . ($cellNum - 1))->getValue()));
                                $sheetData[ReportXlsxRule::CONTRACTS] = $wsheet->getCell($rule[ReportXlsxRule::CONTRACTS] . ($cellNum - 1))->getCalculatedValue();
                                $sheetData[ReportXlsxRule::PAYMENT_QUANTITY] = $wsheet->getCell($rule[ReportXlsxRule::PAYMENT_QUANTITY] . ($cellNum - 1))->getCalculatedValue();
                                $sheetData[ReportXlsxRule::PAYMENT_SUM] = $wsheet->getCell($rule[ReportXlsxRule::PAYMENT_SUM] . ($cellNum - 1))->getCalculatedValue();
                                $sheetData[ReportXlsxRule::ADDITIONAL_PAYMENT] = $wsheet->getCell($rule[ReportXlsxRule::ADDITIONAL_PAYMENT] . ($cellNum - 1))->getCalculatedValue();
                                $sheetData[ReportXlsxRule::LEASING] = $wsheet->getCell($rule[ReportXlsxRule::LEASING] . ($cellNum - 1))->getCalculatedValue();
                                $sheetData[ReportXlsxRule::TOTAL] = $wsheet->getCell($rule[ReportXlsxRule::TOTAL] . ($cellNum - 1))->getCalculatedValue();
                            }
                        }
                    }
                }

                if (($cellLetter == 'E') && (int) $cell->getRow() >= (int) $rule[ReportXlsxRule::START_OF_REPORTS] && (int) $cell->getRow() <= (int) $rule[ReportXlsxRule::END_OF_REPORTS]) {
                    if (!$request->close_month) {
                        $date = date('d.m.Y', Date::excelToTimestamp((int) $cell->getValue()));
                        if ($date == date('d.m.Y')) {
                            $sheetData[ReportXlsxRule::CONTRACTS_2] = $wsheet->getCell($rule[ReportXlsxRule::CONTRACTS_2] . $cellNum)->getCalculatedValue();
                            $sheetData[ReportXlsxRule::CONVERSION_2] = $wsheet->getCell($rule[ReportXlsxRule::CONVERSION_2] . $cellNum)->getCalculatedValue();
                        }
                    } else {
                        if ((string) $wsheet->getCell($rule[ReportXlsxRule::CONTRACTS_2] . $cellNum)->getCalculatedValue() == '' || $cellNum >= (int) $rule[ReportXlsxRule::END_OF_REPORTS]) {
                            if ($sheetData[ReportXlsxRule::CONTRACTS_2] == '') {
                                $sheetData[ReportXlsxRule::CONTRACTS_2] = $wsheet->getCell($rule[ReportXlsxRule::CONTRACTS_2] . ($cellNum - 1))->getCalculatedValue();
                                $sheetData[ReportXlsxRule::CONVERSION_2] = $wsheet->getCell($rule[ReportXlsxRule::CONVERSION_2] . ($cellNum - 1))->getCalculatedValue();
                            }
                        }
                    }
                }
            }
        }

        if ($request->close_month) {
            $sheetData['Дата'] = $dateM . ' ' . date('H:i:s');
            $sheetData['Last File'] = $request->file('file')->getClientOriginalName();
        }

        $company->data = json_encode($sheetData);
        $company->save();
    }
}
