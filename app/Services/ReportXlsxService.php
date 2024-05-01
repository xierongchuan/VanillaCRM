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
                return (array) ((array) json_decode($company->data))['Продажи'];
            }
        }

        return [];
    }

    private function getSheetData(Request $request, Company $company, array $workers): array
    {
        return [
            'Дата' => date('Y-m-d H:i:s'),
            'Договора' => '',
            'Оплата Кол-во' => '',
            'Оплата Сумм' => '',
            'Доплата' => '',
            'Лизинг' => '',
            'Всего' => '',
            'План Кол-во' => '',
            'План Сумм' => '',
            'Факт Кол-во' => '',
            'Факт Сумм' => '',
            '2 Договора' => '',
            '2 Конверсия' => '',
            '% от кол-во' => '',
            '% от сумм' => '',
            '3 Оплата' => '',
            '3 Доплата' => '',
            '3 Лизинг' => '',
            '3 Остаток' => '',
            '5 Через банк шт' => '',
            '5 Через банк сумма' => '',
            '5 Через лизинг шт' => '',
            '5 Через лизинг сумма' => '',
            '5 Итог шт' => '',
            '5 Cумма' => '',
            'Начало отчётов' => '',
            'Конец отчётов' => '',
            'Заметка' => $request->note,
            'Начало списка продаж' => '',
            'Продажи' => $workers,
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
        }
    }

    private function validateRequest(Request $request): void
    {
        $request->validate([
            'file' => 'required|max:51200',
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

                if (($cellLetter == 'A') && (int) $cell->getRow() >= (int) $rule[ReportXlsxRule::НАЧАЛО_ОТЧЕТОВ] && (int) $cell->getRow() <= (int) $rule[ReportXlsxRule::КОНЕЦ_ОТЧЕТОВ]) {
                    if (!$request->close_month) {
                        $date = date('d.m.Y', Date::excelToTimestamp((int) $cell->getValue()));
                        if ($date == date('d.m.Y')) {
                            $sheetData[ReportXlsxRule::ДОГОВОРА] = $wsheet->getCell($rule[ReportXlsxRule::ДОГОВОРА] . $cellNum)->getCalculatedValue();
                            $sheetData[ReportXlsxRule::ОПЛАТА_КОЛ_ВО] = $wsheet->getCell($rule[ReportXlsxRule::ОПЛАТА_КОЛ_ВО] . $cellNum)->getCalculatedValue();
                            $sheetData[ReportXlsxRule::ОПЛАТА_СУММ] = $wsheet->getCell($rule[ReportXlsxRule::ОПЛАТА_СУММ] . $cellNum)->getCalculatedValue();
                            $sheetData[ReportXlsxRule::ДОПЛАТА] = $wsheet->getCell($rule[ReportXlsxRule::ДОПЛАТА] . $cellNum)->getCalculatedValue();
                            $sheetData[ReportXlsxRule::ЛИЗИНГ] = $wsheet->getCell($rule[ReportXlsxRule::ЛИЗИНГ] . $cellNum)->getCalculatedValue();
                            $sheetData[ReportXlsxRule::ВСЕГО] = $wsheet->getCell($rule[ReportXlsxRule::ВСЕГО] . $cellNum)->getCalculatedValue();
                        }
                    } else {
                        if ((string) $wsheet->getCell($rule[ReportXlsxRule::ДОГОВОРА] . $cellNum)->getCalculatedValue() == '' || $cellNum >= (int) $rule[ReportXlsxRule::КОНЕЦ_ОТЧЕТОВ]) {
                            if ($sheetData[ReportXlsxRule::ДОГОВОРА] == '') {
                                $dateM = date('Y-m-d', Date::excelToTimestamp((int) $wsheet->getCell('A' . ($cellNum - 1))->getValue()));
                                $sheetData[ReportXlsxRule::ДОГОВОРА] = $wsheet->getCell($rule[ReportXlsxRule::ДОГОВОРА] . ($cellNum - 1))->getCalculatedValue();
                                $sheetData[ReportXlsxRule::ОПЛАТА_КОЛ_ВО] = $wsheet->getCell($rule[ReportXlsxRule::ОПЛАТА_КОЛ_ВО] . ($cellNum - 1))->getCalculatedValue();
                                $sheetData[ReportXlsxRule::ОПЛАТА_СУММ] = $wsheet->getCell($rule[ReportXlsxRule::ОПЛАТА_СУММ] . ($cellNum - 1))->getCalculatedValue();
                                $sheetData[ReportXlsxRule::ДОПЛАТА] = $wsheet->getCell($rule[ReportXlsxRule::ДОПЛАТА] . ($cellNum - 1))->getCalculatedValue();
                                $sheetData[ReportXlsxRule::ЛИЗИНГ] = $wsheet->getCell($rule[ReportXlsxRule::ЛИЗИНГ] . ($cellNum - 1))->getCalculatedValue();
                                $sheetData[ReportXlsxRule::ВСЕГО] = $wsheet->getCell($rule[ReportXlsxRule::ВСЕГО] . ($cellNum - 1))->getCalculatedValue();
                            }
                        }
                    }
                }
            }
        }

        if ($sheetData[ReportXlsxRule::ДОГОВОРА] == '') {
            throw new \Exception('Не найден сегодняшний отчёт');
        }

        $sheetData[ReportXlsxRule::ПЛАН_КОЛ_ВО] = $wsheet->getCell($rule[ReportXlsxRule::ПЛАН_КОЛ_ВО])->getCalculatedValue();
        $sheetData[ReportXlsxRule::ПЛАН_СУММ] = $wsheet->getCell($rule[ReportXlsxRule::ПЛАН_СУММ])->getCalculatedValue();
        $sheetData[ReportXlsxRule::ФАКТ_КОЛ_ВО] = $wsheet->getCell($rule[ReportXlsxRule::ФАКТ_КОЛ_ВО])->getCalculatedValue();
        $sheetData[ReportXlsxRule::ФАКТ_СУММ] = $wsheet->getCell($rule[ReportXlsxRule::ФАКТ_СУММ])->getCalculatedValue();
        $sheetData[ReportXlsxRule::ДОГОВОРА_2] = $wsheet->getCell($rule[ReportXlsxRule::ДОГОВОРА_2])->getCalculatedValue();
        $sheetData[ReportXlsxRule::ПРОЦЕНТ_ОТ_КОЛ_ВО] = round(($wsheet->getCell($rule[ReportXlsxRule::ПРОЦЕНТ_ОТ_КОЛ_ВО])->getCalculatedValue() * 100), 2);

        $num1 = (int) $wsheet->getCell($rule[ReportXlsxRule::ФАКТ_КОЛ_ВО])->getCalculatedValue();
        $num2 = (int) $wsheet->getCell($rule[ReportXlsxRule::ДОГОВОРА_2])->getCalculatedValue();
        $result = 0;
        if ($num1 != 0 && $num2 != 0) {
            $result = $num1 / ($num2 / 100);
        }
        $sheetData[ReportXlsxRule::КОНВЕРСИЯ_2] = round($result, 2);

        $sheetData[ReportXlsxRule::ПРОЦЕНТ_ОТ_СУММ] = round(($wsheet->getCell($rule[ReportXlsxRule::ПРОЦЕНТ_ОТ_СУММ])->getCalculatedValue()), 2);

        $sheetData[ReportXlsxRule::ОПЛАТА_3] = $wsheet->getCell($rule[ReportXlsxRule::ОПЛАТА_3])->getCalculatedValue();
        $sheetData[ReportXlsxRule::ДОПЛАТА_3] = $wsheet->getCell($rule[ReportXlsxRule::ДОПЛАТА_3])->getCalculatedValue();
        $sheetData[ReportXlsxRule::ЛИЗИНГ_3] = $wsheet->getCell($rule[ReportXlsxRule::ЛИЗИНГ_3])->getCalculatedValue();
        $sheetData[ReportXlsxRule::ОСТАТОК_3] = $wsheet->getCell($rule[ReportXlsxRule::ОСТАТОК_3])->getCalculatedValue();

        $sheetData[ReportXlsxRule::ЧЕРЕЗ_БАНК_ШТ_5] = $wsheet->getCell($rule[ReportXlsxRule::ЧЕРЕЗ_БАНК_ШТ_5])->getCalculatedValue();
        $sheetData[ReportXlsxRule::ЧЕРЕЗ_БАНК_СУММА_5] = $wsheet->getCell($rule[ReportXlsxRule::ЧЕРЕЗ_БАНК_СУММА_5])->getCalculatedValue();
        $sheetData[ReportXlsxRule::ЧЕРЕЗ_ЛИЗИНГ_ШТ_5] = $wsheet->getCell($rule[ReportXlsxRule::ЧЕРЕЗ_ЛИЗИНГ_ШТ_5])->getCalculatedValue();
        $sheetData[ReportXlsxRule::ЧЕРЕЗ_ЛИЗИНГ_СУММА_5] = $wsheet->getCell($rule[ReportXlsxRule::ЧЕРЕЗ_ЛИЗИНГ_СУММА_5])->getCalculatedValue();
        $sheetData[ReportXlsxRule::ИТОГ_ШТ_5] = $wsheet->getCell($rule[ReportXlsxRule::ИТОГ_ШТ_5])->getCalculatedValue();
        $sheetData[ReportXlsxRule::СУММА_5] = $wsheet->getCell($rule[ReportXlsxRule::СУММА_5])->getCalculatedValue();

        $sheetData[ReportXlsxRule::НАЧАЛО_СПИСКА_ПРОДАЖ] = (int) $rule[ReportXlsxRule::НАЧАЛО_СПИСКА_ПРОДАЖ];

        $this->createSalesList($sheetData, $wsheet, $rule);

        // Удаление проблемной ячейки
        $wsheet->setCellValue('X13', '');

        $this->saveReport($request, $sheetData, $company, $dateM, $sheet);
    }

    private function createSalesList(array &$sheetData, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $wsheet, array $rule): void
    {
        $salesStart = $sheetData[ReportXlsxRule::НАЧАЛО_СПИСКА_ПРОДАЖ];

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];

        $managers = (array) $sheetData['Продажи'];
        $totalSum = 0;
        foreach ($managers as $manager) {
            $totalSum += (int) $manager['month'];
        }

        $percentages = [];
        foreach ($managers as $key => $manager) {
            if ((int) $manager['month'] == 0) {
                $percentages[$key] = 0;
                continue;
            }

            $percentage = ($manager['month'] / $totalSum) * 100;
            $percentages[$key] = round($percentage, 1);
        }

        $wsheet->mergeCells("A" . ($salesStart) . ":B" . ($salesStart));
        $wsheet->setCellValue("A" . ($salesStart), 'Имя');
        $wsheet->setCellValue("C" . ($salesStart), 'Штук');
        $wsheet->setCellValue("D" . ($salesStart), 'Мес');
        $wsheet->setCellValue("E" . ($salesStart), '%');
        $wsheet->getStyle("A" . $salesStart . ":E" . $salesStart)->getFont()->setBold(true);
        $wsheet->getStyle('A' . ($salesStart) . ':E' . ($salesStart))->applyFromArray($styleArray);

        foreach ($sheetData['Продажи'] as $key => $sold) {
            $sold = (array) $sold;
            $numAddress = $key + $salesStart;
            $wsheet->getStyle("A" . $numAddress . ":E" . $numAddress)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $wsheet->mergeCells("A" . $numAddress . ":B" . $numAddress);
            $wsheet->setCellValue("A" . $numAddress, $sold['name']);
            $wsheet->setCellValue("C" . $numAddress, $sold['sold']);
            $wsheet->setCellValue("D" . $numAddress, $sold['month']);
            $wsheet->setCellValue("E" . $numAddress, $percentages[$key]);
            $wsheet->getStyle('A' . $numAddress . ':E' . $numAddress)->applyFromArray($styleArray);
        }
    }

    private function saveReport(Request $request, array $sheetData, Company $company, string $dateM, \PhpOffice\PhpSpreadsheet\Spreadsheet $sheet)
    {
        $writer = IOFactory::createWriter($sheet, 'Xlsx');

        if ($request->file('file')->isValid()) {
            $oldFile = (string) @((array) json_decode($company->data))['Last File'];
            $oldFilePath = storage_path('app/public/tmp/' . $oldFile);

            if (File::exists($oldFilePath)) {
                File::delete($oldFilePath);
            }

            $fileName = ($dateM != '')
                ? $company->name . '_' . $dateM . date('_H:i:s') . '_' . $sheetData[ReportXlsxRule::СУММА_5] . '_' . $sheetData[ReportXlsxRule::ИТОГ_ШТ_5] . '_' . $sheetData[ReportXlsxRule::ФАКТ_КОЛ_ВО] . '.xlsx'
                : $company->name . '_' . date('Y-m-d_H:i:s') . '_' . $sheetData[ReportXlsxRule::СУММА_5] . '_' . $sheetData[ReportXlsxRule::ИТОГ_ШТ_5] . '_' . $sheetData[ReportXlsxRule::ФАКТ_КОЛ_ВО] . '.xlsx';

            $sheetData['Last File'] = $fileName;

            if ($request->close_month) {
                $writer->save(storage_path('app/public/archive/' . $fileName), 1);
                $writer->save(storage_path('app/public/tmp/' . $fileName), 1);

                $sheetData['Clear Sales'] = true;
                $company->data = json_encode($sheetData);
                $company->save();

                return redirect()->route('home.index')->with('success', 'Отчёт с закрытием месяца успешно загружен.');
            } else {
                $writer->save(storage_path('app/public/tmp/' . $fileName), 1);
                $company->data = json_encode($sheetData);
                $company->save();

                return redirect()->route('home.index')->with('success', 'Отчёт успешно загружен.');
            }
        } else {
            throw new \Exception('Ошибка при загрузке файла.');
        }
    }
}
