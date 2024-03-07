<?php

namespace App\Http\Controllers;

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
                'url' => (string)asset($filePath),
                'date' => $this->getRussianMonthName($file_name_data[1]),
                'sum' => number_format((int)$file_name_data[3], 0, '', ' '),
                'count' => number_format((int)$file_name_data[4], 0, '', ' '),
                'fakt' => number_format(@(int)$file_name_data[5], 0, '', ' ')
            ];

            // Добавляем URL в массив
            $files_data[] = (object)$file_data;
        }

        $files_data = array_reverse($files_data);

        return view('company.archive', compact('company', 'files_data'));
    }

    public function remove_last_report(Company $company)
    {
        if (empty($company->data)) {
            return redirect()->route('home.index')->withErrors('Последний отчтёт и так был удалён!');
        }

        $file = (string)@((array)json_decode($company->data))['Last File'];
        $file_tmp_path = storage_path('app/public/tmp/' . $file);
        $file_path = storage_path('app/public/archive/' . $file);

        // Проверяем, существует ли файл
        if (File::exists($file_tmp_path)) {
            // Удаляем файл
            File::delete($file_tmp_path);
        }

        // Проверяем, существует ли файл
        if (File::exists($file_path)) {
            // Удаляем файл
            File::delete($file_path);
        }

        $company->data = '';
        $company->save();

        return redirect()->route('home.index')->with('success', 'Последний отчёт успешно удалён!');
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

        $sheet->setCellValue('A1', 'Дата');
        $sheet->setCellValue('B1', 'Доп');
        $sheet->setCellValue('C1', 'Текущий');
        $sheet->setCellValue('D1', 'ТО');
        $sheet->setCellValue('E1', 'Кузов');
        $sheet->setCellValue('F1', 'Магазин');
        $sheet->setCellValue('G1', 'Всего');

        $i = 2;
        foreach ($reports as $key => $value) {

            $val = (object)json_decode($value->data);

            $sheet->setCellValue('A' . $i, $value->for_date);
            $sheet->setCellValue('B' . $i, $val->dop);
            $sheet->setCellValue('C' . $i, $val->now);
            $sheet->setCellValue('D' . $i, $val->to);
            $sheet->setCellValue('E' . $i, $val->kuz);
            $sheet->setCellValue('F' . $i, $val->store);
            $sheet->setCellValue('G' . $i, '=SUM(A' . $i . ':F' . $i . ')');

            $i++;
        }

        $sheet->setCellValue('A33', 'Всего за мес');
        $sheet->setCellValue('B33', '=SUM(B2:B32)');
        $sheet->setCellValue('C33', '=SUM(C2:C32)');
        $sheet->setCellValue('D33', '=SUM(D2:D32)');
        $sheet->setCellValue('E33', '=SUM(E2:E32)');
        $sheet->setCellValue('F33', '=SUM(F2:F32)');
        $sheet->setCellValue('G33', '=SUM(G2:G32)');

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
            $formattedMonth = $this->getRussianMonthName($date->format('m'));

            $reports[$formattedYear . ' ' . $formattedMonth] = [
                $item['date'],
                (int)$item['total_sum'],
            ];
        }

        return view('company.service_archive', compact('company','reports'));
    }

    public function deleteLastServiceReport(Company $company)
    {
        $latestReport = DB::table('reports')
            ->where('type', 'report_service')
            ->where('com_id', $company->id)
            ->orderByDesc('for_date')
            ->first();

        if ($latestReport) {
            DB::table('reports')->where('id', $latestReport->id)->delete();
            return redirect()->route('home.index')->with('success', 'Последний отчёт успешно удалён!');
        }

        return redirect()->route('home.index')->withErrors( 'Небыло никакого отчёта!');
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
}
