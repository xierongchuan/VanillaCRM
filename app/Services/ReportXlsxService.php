<?php

namespace App\Services;

use App\Enums\ReportXlsxRule;
use App\Models\Company;
use App\Models\Permission;
use App\Models\Report;
use Carbon\Carbon;
use Exception;
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

        $workers = [];

        foreach ($inputData as $key => $value) {
            if (preg_match('/^worker_name_(\d+)$/', $key, $matches)) {
                $workerNumber = $matches[1];
                $workerSold = $inputData['worker_sold_' . $workerNumber];
                $workers[$workerNumber] = $workerSold;
            }
        }

        $sheetData = $this->getSheetData($request, $company, $workers);

        $this->validateRequest($request);
        $this->validateFileType($request);

        $permission = $this->getPermissionData($company);
        $this->fillSheetData($sheetData, $permission, $request, $company);

        return $sheetData;
    }

    private function getSheetData(Request $request, Company $company, array $workers): array
    {
        return [
            'Дата' => '',
            'UploadDate' => date('Y-m-d H:i:s'),
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
            ReportXlsxRule::START_OF_SALES_LIST => '',
            'Sales' => $workers,
            'File' => '',
            'Note' => $request->note,
        ];
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
            throw new Exception('Файл должен быть типа xlsx (Exel).');
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

        foreach ($wsheet->getRowIterator() as $row) {

            $cellIterate = $row->getCellIterator();
            $cellIterate->setIterateOnlyExistingCells(true);
            foreach ($cellIterate as $cell) {
                $cellAddress = $cell->getCoordinate();
                $cellLetter = preg_replace('/[0-9]/', '', $cellAddress);
                $cellNum = preg_replace('/[A-z]/', '', $cellAddress);

                if (($cellLetter == 'A') && (int) $cell->getRow() >= (int) $rule[ReportXlsxRule::START_OF_REPORTS] && (int) $cell->getRow() <= (int) $rule[ReportXlsxRule::END_OF_REPORTS]) {
                    $date = date('d.m.Y', Date::excelToTimestamp((int) $wsheet->getCell('A' . $cellNum)->getCalculatedValue()));
                    if ($date == Carbon::createFromFormat('Y-m-d', $request->for_date)->format('d.m.Y')) {
                        echo $cellNum . '<br>';
                        $sheetData[ReportXlsxRule::CONTRACTS] = $wsheet->getCell($rule[ReportXlsxRule::CONTRACTS] . $cellNum)->getCalculatedValue();
                        $sheetData[ReportXlsxRule::PAYMENT_QUANTITY] = $wsheet->getCell($rule[ReportXlsxRule::PAYMENT_QUANTITY] . $cellNum)->getCalculatedValue();
                        $sheetData[ReportXlsxRule::PAYMENT_SUM] = $wsheet->getCell($rule[ReportXlsxRule::PAYMENT_SUM] . $cellNum)->getCalculatedValue();
                        $sheetData[ReportXlsxRule::ADDITIONAL_PAYMENT] = $wsheet->getCell($rule[ReportXlsxRule::ADDITIONAL_PAYMENT] . $cellNum)->getCalculatedValue();
                        $sheetData[ReportXlsxRule::LEASING] = $wsheet->getCell($rule[ReportXlsxRule::LEASING] . $cellNum)->getCalculatedValue();
                        $sheetData[ReportXlsxRule::TOTAL] = $wsheet->getCell($rule[ReportXlsxRule::TOTAL] . $cellNum)->getCalculatedValue();
                    }
                }

                if (($cellLetter == 'E') && (int) $cell->getRow() >= (int) $rule[ReportXlsxRule::START_OF_REPORTS] && (int) $cell->getRow() <= (int) $rule[ReportXlsxRule::END_OF_REPORTS]) {
                    $date2 = date('d.m.Y', Date::excelToTimestamp((int) $wsheet->getCell('A' . $cellNum)->getCalculatedValue()));
                    if ($date2 == Carbon::createFromFormat('Y-m-d', $request->for_date)->format('d.m.Y')) {
                        $sheetData[ReportXlsxRule::CONTRACTS_2] = $wsheet->getCell($rule[ReportXlsxRule::CONTRACTS_2] . $cellNum)->getCalculatedValue();
                    }
                }
            }

            if ($sheetData[ReportXlsxRule::CONTRACTS] != '' && $sheetData[ReportXlsxRule::CONTRACTS] !== null)
                break;
        }

        if ($sheetData[ReportXlsxRule::CONTRACTS] == '' || $sheetData[ReportXlsxRule::CONTRACTS] === null) {
            throw new Exception('Не найден отчёт на заданный день');
        }

        $sheetData['Дата'] = $request->for_date;
        $sheetData['File'] = $request->file('file')->getClientOriginalName();

        $sheetData[ReportXlsxRule::PLAN_QUANTITY] = $wsheet->getCell($rule[ReportXlsxRule::PLAN_QUANTITY])->getCalculatedValue();
        $sheetData[ReportXlsxRule::PLAN_SUM] = $wsheet->getCell($rule[ReportXlsxRule::PLAN_SUM])->getCalculatedValue();
        $sheetData[ReportXlsxRule::ACTUAL_QUANTITY] = $wsheet->getCell($rule[ReportXlsxRule::ACTUAL_QUANTITY])->getCalculatedValue();
        $sheetData[ReportXlsxRule::ACTUAL_SUM] = $wsheet->getCell($rule[ReportXlsxRule::ACTUAL_SUM])->getCalculatedValue();
        $sheetData[ReportXlsxRule::CONTRACTS_2] = $wsheet->getCell($rule[ReportXlsxRule::CONTRACTS_2])->getCalculatedValue();
        $sheetData[ReportXlsxRule::PERCENT_OF_QUANTITY] = round(($wsheet->getCell($rule[ReportXlsxRule::PERCENT_OF_QUANTITY])->getCalculatedValue() * 100), 2);

        $num1 = (int) $wsheet->getCell($rule[ReportXlsxRule::ACTUAL_QUANTITY])->getCalculatedValue();
        $num2 = (int) $wsheet->getCell($rule[ReportXlsxRule::CONTRACTS_2])->getCalculatedValue();
        $result = 0;
        if ($num1 != 0 && $num2 != 0) {
            $result = $num1 / ($num2 / 100);
        }
        $sheetData[ReportXlsxRule::CONVERSION_2] = round($result, 2);

        $sheetData[ReportXlsxRule::PERCENT_OF_SUM] = round(($wsheet->getCell($rule[ReportXlsxRule::PERCENT_OF_SUM])->getCalculatedValue()), 2);

        $sheetData[ReportXlsxRule::PAYMENT_3] = $wsheet->getCell($rule[ReportXlsxRule::PAYMENT_3])->getCalculatedValue();
        $sheetData[ReportXlsxRule::ADDITIONAL_PAYMENT_3] = $wsheet->getCell($rule[ReportXlsxRule::ADDITIONAL_PAYMENT_3])->getCalculatedValue();
        $sheetData[ReportXlsxRule::LEASING_3] = $wsheet->getCell($rule[ReportXlsxRule::LEASING_3])->getCalculatedValue();
        $sheetData[ReportXlsxRule::BALANCE_3] = $wsheet->getCell($rule[ReportXlsxRule::BALANCE_3])->getCalculatedValue();

        $sheetData[ReportXlsxRule::THROUGH_BANK_QTY_5] = $wsheet->getCell($rule[ReportXlsxRule::THROUGH_BANK_QTY_5])->getCalculatedValue();
        $sheetData[ReportXlsxRule::THROUGH_BANK_SUM_5] = $wsheet->getCell($rule[ReportXlsxRule::THROUGH_BANK_SUM_5])->getCalculatedValue();
        $sheetData[ReportXlsxRule::THROUGH_LEASING_QTY_5] = $wsheet->getCell($rule[ReportXlsxRule::THROUGH_LEASING_QTY_5])->getCalculatedValue();
        $sheetData[ReportXlsxRule::THROUGH_LEASING_SUM_5] = $wsheet->getCell($rule[ReportXlsxRule::THROUGH_LEASING_SUM_5])->getCalculatedValue();
        $sheetData[ReportXlsxRule::TOTAL_QTY_5] = $wsheet->getCell($rule[ReportXlsxRule::TOTAL_QTY_5])->getCalculatedValue();
        $sheetData[ReportXlsxRule::SUM_5] = $wsheet->getCell($rule[ReportXlsxRule::SUM_5])->getCalculatedValue();


        $report = new Report();
        $report->type = 'report_xlsx';
        $report->com_id = $company->id;
        $report->for_date = Carbon::createFromFormat('Y-m-d', $request->for_date)->format('Y-m-d');
        $report->data = json_encode($sheetData, true);
        $report->save();
    }
}
