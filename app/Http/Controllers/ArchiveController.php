<?php

namespace App\Http\Controllers;

use App\Enums\ReportXlsxRule;
use App\Models\Report;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as WXlsx;
use Carbon\Carbon;

class ArchiveController extends Controller
{
    public function archive(Company $company)
    {
        $files = File::allFiles(storage_path('app/public/archive'));

        // Инициализируем пустой массив для хранения URL файлов
        $files_data = [];

        // Перебираем каждый файл и получаем его URL
        foreach ($files as $file) {

            // Получаем путь к файлу относительно public директории
            $filePath = 'storage/app/public' . str_replace(storage_path('app/public'), '', $file);
            $file_name_data = explode('_', basename($file));
            if ($file_name_data[0] != $company->name) {
                continue;
            }
            // Генерируем данные файла
            $file_data = [
                'name' => basename($file),
                'company' => $file_name_data[0],
                'url' => (string) asset($filePath),
                'date' => $this->getRussianMonthName($file_name_data[1]),
                'sum' => number_format((int) $file_name_data[3], 0, '', ' '),
                'count' => number_format((int) $file_name_data[4], 0, '', ' '),
                'fakt' => number_format(@(int) $file_name_data[5], 0, '', ' ')
            ];

            // Добавляем URL в массив
            $files_data[] = (object) $file_data;
        }

        $files_data = array_reverse($files_data);

        $groupedReports = $this->groupReportsByMonth($company);

        return view('company.archive', compact('company', 'files_data', 'groupedReports'));
    }


    public function groupReportsByMonth(Company $company)
    {
        // Извлекаем все отчеты типа "report_xlsx"
        $reports = Report::where('com_id', $company->id)
            ->where('type', 'report_xlsx')
            ->orderBy('for_date', 'desc')
            ->get();

        // Инициализируем массив для хранения отчетов, сгруппированных по месяцам
        $groupedReports = [];

        // Проходим по каждому отчету
        foreach ($reports as $report) {
            // Получаем месяц и год из даты отчета
            $date = Carbon::parse($report->for_date);
            $month = $date->format('Y-m'); // Форматируем дату как 'YYYY-MM'

            // Если месяц еще не существует в массиве, создаем его
            if (!isset($groupedReports[$month])) {
                $groupedReports[$month] = [];
            }

            // Декодируем данные отчета
            $reportData = json_decode($report->data, true);

            // Извлекаем суммы, количество и фактические значения из данных
            $url = '/storage/app/public/tmp/' . $reportData['File'];
            $sum = isset($reportData[ReportXlsxRule::SUM_5]) ? $reportData[ReportXlsxRule::SUM_5] : 0;
            $quantity = isset($reportData[ReportXlsxRule::TOTAL_QTY_5]) ? $reportData[ReportXlsxRule::TOTAL_QTY_5] : 0;
            $fact = isset($reportData[ReportXlsxRule::ACTUAL_QUANTITY]) ? $reportData[ReportXlsxRule::ACTUAL_QUANTITY] : 0;

            // Добавляем отчет и извлеченные данные в соответствующий месяц
            $groupedReports[$month][] = [
                'report' => $report,
                'url' => $url,
                'sum' => $sum,
                'quantity' => $quantity,
                'fact' => $fact
            ];
        }

        return $groupedReports;
    }

    public function remove_last_report(Company $company)
    {
        // Получение последнего отчета report_xlsx для данной компании
        $lastReport = Report::where('com_id', $company->id)
            ->where('type', 'report_xlsx')
            ->orderBy('for_date', 'desc')
            ->first();

        // Проверка, существует ли отчет
        if (!$lastReport) {
            return redirect()->route('home.index')->withErrors('Последний отчет уже был удален!');
        }

        // Декодирование данных отчета
        $data = json_decode($lastReport->data, true);

        // Получение имени последнего файла отчета
        $file = isset($data['File']) ? (string) $data['File'] : '';

        // Путь к файлу во временной папке и архиве
        $file_tmp_path = storage_path('app/public/tmp/' . $file);
        $file_path = storage_path('app/public/archive/' . $file);

        // Проверка и удаление файла из временной папки
        if (File::exists($file_tmp_path)) {
            File::delete($file_tmp_path);
        }

        // Проверка и удаление файла из архива
        if (File::exists($file_path)) {
            File::delete($file_path);
        }

        // Удаление данных последнего отчета из компании
        $lastReport->delete();

        return redirect()->route('home.index')->with('success', 'Последний отчет успешно удален!');
    }


    public function getServiceReportXlsx(Company $company, string $date)
    {

        $startDate = Carbon::parse($date)->startOfMonth();
        $endDate = Carbon::parse($date)->endOfMonth();

        $reports = DB::table('reports')
            ->select('com_id', 'for_date', 'type', 'data')
            ->where('for_date', '>=', $startDate)
            ->where('for_date', '<=', $endDate)
            ->where('type', 'report_service')
            ->where('com_id', $company->id)
            ->orderBy('for_date')
            ->get();

        // Create a new spreadsheet
        $spreadsheet = new Spreadsheet();

        // Set the active sheet
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getColumnDimension('A')->setWidth(13);
        $sheet->getColumnDimension('B')->setWidth(13);
        $sheet->getColumnDimension('C')->setWidth(13);
        $sheet->getColumnDimension('D')->setWidth(13);
        $sheet->getColumnDimension('E')->setWidth(13);
        $sheet->getColumnDimension('F')->setWidth(13);
        $sheet->getColumnDimension('G')->setWidth(13);
        $sheet->getColumnDimension('H')->setWidth(13);
        $sheet->getColumnDimension('I')->setWidth(13);

        $sheet->setCellValue('A1', 'Дата');
        $sheet->setCellValue('B1', 'Доп');
        $sheet->setCellValue('C1', 'Текущий');
        $sheet->setCellValue('D1', 'ТО');
        $sheet->setCellValue('E1', 'Кузов');
        $sheet->setCellValue('F1', 'Магазин');
        $sheet->setCellValue('G1', 'Всего');
        $sheet->setCellValue('H1', 'Запчасть');
        $sheet->setCellValue('I1', 'Сервис');

        $i = 2;
        foreach ($reports as $key => $value) {

            $val = (object) json_decode($value->data);

            $sheet->setCellValue('A' . $i, $value->for_date);
            $sheet->setCellValue('B' . $i, $val->dop);
            $sheet->setCellValue('C' . $i, $val->now);
            $sheet->setCellValue('D' . $i, $val->to);
            $sheet->setCellValue('E' . $i, $val->kuz);
            $sheet->setCellValue('F' . $i, $val->store);
            $sheet->setCellValue('G' . $i, '=SUM(A' . $i . ':F' . $i . ')');
            $sheet->setCellValue('H' . $i, $val->zap);
            $sheet->setCellValue('I' . $i, $val->srv);

            $i++;
        }

        $sheet->setCellValue('A33', 'Всего за мес');
        $sheet->setCellValue('B33', '=SUM(B2:B32)');
        $sheet->setCellValue('C33', '=SUM(C2:C32)');
        $sheet->setCellValue('D33', '=SUM(D2:D32)');
        $sheet->setCellValue('E33', '=SUM(E2:E32)');
        $sheet->setCellValue('F33', '=SUM(F2:F32)');
        $sheet->setCellValue('G33', '=SUM(G2:G32)');
        $sheet->setCellValue('H33', '=SUM(H2:H32)');
        $sheet->setCellValue('I33', '=SUM(I2:I32)');

        $fileName = $company->name . ' Service Report.xlsx';

        // Save the spreadsheet
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save(storage_path('app/public/reports_archive/' . $fileName), 1);

        return response()->download(storage_path('app/public/reports_archive/' . $fileName));

    }

    public function serviceArchive(Company $company)
    {
        $monthsData = DB::table('reports')
            ->select(
                DB::raw('DISTINCT DATE_FORMAT(for_date, "%Y-%m-01") as month'),
                DB::raw('SUM(CAST(JSON_UNQUOTE(JSON_EXTRACT(data, "$.dop")) AS UNSIGNED)) +
                  SUM(CAST(JSON_UNQUOTE(JSON_EXTRACT(data, "$.now")) AS UNSIGNED)) +
                  SUM(CAST(JSON_UNQUOTE(JSON_EXTRACT(data, "$.to")) AS UNSIGNED)) +
                  SUM(CAST(JSON_UNQUOTE(JSON_EXTRACT(data, "$.kuz")) AS UNSIGNED)) +
                  SUM(CAST(JSON_UNQUOTE(JSON_EXTRACT(data, "$.store")) AS UNSIGNED)) as total_sum')
            )
            ->where('type', 'report_service')
            ->where('com_id', $company->id)
            ->groupBy('month')
            ->get();

        $monthsDataArray = $monthsData->map(function ($item) {
            return [
                'date' => $item->month,
                'total_sum' => $item->total_sum,
            ];
        })->toArray();

        $reports = [];

        foreach ($monthsDataArray as $item) {
            $date = Carbon::parse($item['date']);
            $formattedYear = $date->format('Y');
            $formattedMonth = $this->getRussianMonthNameStr($date->format('m'));

            $reports[$formattedYear . ' ' . $formattedMonth] = [
                $item['date'],
                (int) $item['total_sum'],
            ];
        }

        return view('company.service_archive', compact('company', 'reports'));
    }

    public function deleteLastServiceReport(Company $company)
    {
        $latestReport = DB::table('reports')
            ->where('type', 'report_service')
            ->where('com_id', $company->id)
            ->orderBy('for_date')
            ->first();

        if ($latestReport) {
            DB::table('reports')->where('id', $latestReport->id)->delete();
            return redirect()->route('home.index')->with('success', 'Последний отчёт успешно удалён!');
        }

        return redirect()->route('home.index')->withErrors('Небыло никакого отчёта!');
    }

    private function getRussianMonthName($date)
    {
        $monthNumber = date('n', strtotime($date));
        $months = [
            1 => 'Январь',
            2 => 'Февраль',
            3 => 'Март',
            4 => 'Апрель',
            5 => 'Май',
            6 => 'Июнь',
            7 => 'Июль',
            8 => 'Август',
            9 => 'Сентябрь',
            10 => 'Октябрь',
            11 => 'Ноябрь',
            12 => 'Декабрь'
        ];

        return $months[$monthNumber];
    }

    private function getRussianMonthNameStr(string $monthNumber)
    {
        $months = [
            '01' => 'Январь',
            '02' => 'Февраль',
            '03' => 'Март',
            '04' => 'Апрель',
            '05' => 'Май',
            '06' => 'Июнь',
            '07' => 'Июль',
            '08' => 'Август',
            '09' => 'Сентябрь',
            '10' => 'Октябрь',
            '11' => 'Ноябрь',
            '12' => 'Декабрь'
        ];

        return $months[$monthNumber];
    }
}
