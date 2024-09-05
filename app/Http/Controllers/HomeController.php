<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Department;
use App\Models\Permission;
use App\Models\Post;
use App\Models\Field;
use App\Models\User;
use App\Services\ReportXlsxService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class HomeController extends Controller
{
    // Главная функция контроллера
    public function index()
    {
        // Проверка, авторизован ли пользователь
        if (Auth::check()) {
            // Определение роли пользователя и вызов соответствующего метода
            switch (Auth::user()->role) {
                case 'admin':
                    return $this->adminView();
                case 'user':
                    return $this->userView();
                default:
                    echo "Кто ты?";
                    return false;
            }
        } else {
            // Если пользователь не авторизован, показываем домашнюю страницу
            return view('home');
        }
    }

    // Метод для отображения админской панели
    private function adminView()
    {
        // Получение всех компаний
        $companies = Company::all();
        // Получение последнего отчёта последнего месяца (не объязательно именно прошлый месяц)
        $archiveReports = $this->getArchiveReports();
        // Получение URL последних отчетов
        $last_repor_urls = $this->getLastReportUrls($companies);
        // Получение сервисных отчетов
        $srv_reps = $this->getServiceReports($companies);
        // Получение ежедневных отчётов
        $coms_data = $this->getComsData($companies);
        // Получение списка месячных продаж менеджеров
        $sales_data = (new ReportXlsxService())->getSalesData($companies);

        // Возврат вида 'home' с данными
        return view(
            'home',
            compact(
                'companies',
                'archiveReports',
                'last_repor_urls',
                'srv_reps',
                'coms_data',
                'sales_data'
            )
        );
    }

    // Метод для отображения пользовательской панели
    private function userView()
    {
        // Получение компании, департамента и поста пользователя
        $company = Company::find(Auth::user()->com_id);
        $department = Department::find(Auth::user()->dep_id);
        $post = Post::find(Auth::user()->post_id);

        // dd($post->permission);

        // Получение разрешений пользователя
        if ($post !== null) {
            $permission_ids = json_decode((string) $post->permission, true);
            if (!is_array($permission_ids)) {
                $permission_ids = []; // Если результат json_decode не массив, заменяем его на пустой массив
            }
            $permissions = Permission::whereIn('id', $permission_ids)->get();
            $permission_vals = $permissions->pluck('value')->toArray();
        } else {
            $permission_vals = [];
        }


        // Получение ежедневных отчётов
        $com_data = $this->getComsData([$company]);
        // Получение списка месячных продаж менеджеров
        $sales_data = (new ReportXlsxService())->getSalesData([$company]);

        // Создание объекта с данными пользователя
        $data = (object) [
            'company' => $company,
            'department' => $department,
            'post' => $post,
            'perm' => $permission_vals,
            'com_data' => reset($com_data),
            'sales_data' => reset($sales_data)
        ];

        // Получение сервисного отчета
        $srv_rep = $this->getServiceReport($company->id);

        // Возврат вида 'home' с данными
        return view('home', compact('company', 'data', 'srv_rep'));
    }

    private function getArchiveReports(): array
    {
        $companies = Company::all();
        $archiveReports = [];

        foreach ($companies as $company) {
            $months = (new ArchiveController())->groupReportsByMonth($company);

            // Преобразуем ключи массива в индексы и получаем все ключи
            $monthKeys = array_keys($months);

            // Проверяем, есть ли хотя бы два элемента в массиве
            if (count($monthKeys) >= 2) {
                $secondMonthKey = $monthKeys[1];
                $secondMonthReports = $months[$secondMonthKey];
                $archiveReports[$company->id] = [$secondMonthKey => (object) reset($secondMonthReports)];
            } else {
                // Если второго месяца нет, добавляем пустой элемент или обрабатываем иначе
                $archiveReports[$company->id] = [];
            }
        }

        return $archiveReports;
    }


    // Метод для получения ежедневного отчёта
    private function getComsData($companies): array
    {
        $com_data = [];
        foreach ($companies as $company) {
            $data = @Report::where('type', 'report_xlsx')
                ->where('com_id', $company->id)
                ->orderBy('for_date', 'desc')
                ->first()['data'];

            if (empty($data))
                continue;

            $com_data[$company->id] = $data;
        }

        return $com_data;
    }

    // Метод для получения данных файлов из указанной папки
    private function getFilesData($folder)
    {
        // Определение пути к папке
        $path = storage_path('app/public/' . $folder);

        // Если папка не существует, возвращаем пустой массив
        if (!File::exists($path)) {
            return [];
        }

        // Получение всех файлов из папки
        $files = File::allFiles($path);
        $files_data = [];

        // Обработка каждого файла
        foreach ($files as $file) {
            $filePath = 'storage/' . $folder . '/' . $file->getFilename();
            $file_name_data = explode('_', basename($file));

            // Сбор данных о файле
            $files_data[] = (object) [
                'name' => basename($file),
                'company' => $file_name_data[0],
                'url' => (string) asset($filePath),
                'date' => $this->getRussianMonthName($file_name_data[1]),
                'sum' => number_format((int) $file_name_data[3], 0, '', ' '),
                'count' => number_format((int) $file_name_data[4], 0, '', ' '),
                'fakt' => number_format(@(int) $file_name_data[5], 0, '', ' ')
            ];
        }

        // Возврат данных файлов в обратном порядке
        return array_reverse($files_data);
    }

    // Метод для получения URL последних отчетов
    private function getLastReportUrls($companies)
    {
        $last_report_urls = [];

        // Обработка каждой компании
        foreach ($companies as $company) {
            // Получение последнего отчета для данной компании
            $lastReport = Report::where('com_id', $company->id)
                ->where('type', 'report_xlsx')
                ->orderBy('for_date', 'desc')
                ->first();

            if ($lastReport) {
                // Декодирование данных отчета
                $data = json_decode($lastReport->data, true);

                // Проверка на наличие ключа 'File' в данных отчета
                if (isset($data['File'])) {
                    // Получение имени файла из данных отчета
                    $fileName = (string) $data['File'];
                    $filePath = storage_path('app/public/tmp/' . $fileName);
                    $fileUrl = 'storage/app/public' . str_replace(storage_path('app/public'), '', $filePath);

                    // Формирование URL
                    $last_report_urls[] = asset($fileUrl);
                }
            }
        }

        return $last_report_urls;
    }


    // Метод для получения сервисных отчетов
    private function getServiceReports($companies)
    {
        $srv_reps = [];

        // Обработка каждой компании
        foreach ($companies as $company) {
            // Получение полей компании
            $company->fields = Field::where('com_id', $company->id)->get();
            // Получение сервисного отчета для компании
            $srv_reps[$company->id] = $this->getServiceReport($company->id);
        }

        return $srv_reps;
    }

    // Метод для получения сервисного отчета для конкретной компании
    private function getServiceReport($companyId)
    {
        // Определение начальной и конечной даты текущего месяца
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();

        // Получение данных из таблицы 'reports'
        $result_full = json_decode(DB::table('reports')
            ->select(DB::raw('SUM(CAST(JSON_UNQUOTE(JSON_EXTRACT(data, "$.dop")) AS UNSIGNED)) as dop_sum'))
            ->addSelect(DB::raw('SUM(CAST(JSON_UNQUOTE(JSON_EXTRACT(data, "$.now")) AS UNSIGNED)) as now_sum'))
            ->addSelect(DB::raw('SUM(CAST(JSON_UNQUOTE(JSON_EXTRACT(data, "$.to")) AS UNSIGNED)) as to_sum'))
            ->addSelect(DB::raw('SUM(CAST(JSON_UNQUOTE(JSON_EXTRACT(data, "$.kuz")) AS UNSIGNED)) as kuz_sum'))
            ->addSelect(DB::raw('SUM(CAST(JSON_UNQUOTE(JSON_EXTRACT(data, "$.store")) AS UNSIGNED)) as store_sum'))
            ->addSelect(DB::raw('SUM(CAST(JSON_UNQUOTE(JSON_EXTRACT(data, "$.zap")) AS UNSIGNED)) as zap_sum'))
            ->addSelect(DB::raw('SUM(CAST(JSON_UNQUOTE(JSON_EXTRACT(data, "$.srv")) AS UNSIGNED)) as srv_sum'))
            ->where('for_date', '>=', $startDate)
            ->where('for_date', '<=', $endDate)
            ->where('type', 'report_service')
            ->where('com_id', $companyId)
            ->get())[0];

        // Получение последнего отчета
        $latestReport = DB::table('reports')
            ->where('type', 'report_service')
            ->where('com_id', $companyId)
            ->orderByDesc('for_date')
            ->first();

        // Если отчета нет, возвращаем данные по умолчанию
        if (!$latestReport) {
            return $this->defaultServiceReport();
        }

        $result = json_decode($latestReport->data);

        // Формирование данных для отчета
        return [
            'dop' => $result->dop,
            'now' => $result->now,
            'to' => $result->to,
            'kuz' => $result->kuz,
            'store' => $result->store,
            'zap' => $result->zap,
            'srv' => $result->srv,
            'SUM' => (
                $result->dop +
                $result->now +
                $result->to +
                $result->kuz +
                $result->store
            ),
            'dop_sum' => $result_full->dop_sum,
            'now_sum' => $result_full->now_sum,
            'to_sum' => $result_full->to_sum,
            'kuz_sum' => $result_full->kuz_sum,
            'store_sum' => $result_full->store_sum,
            'zap_sum' => $result_full->zap_sum,
            'srv_sum' => $result_full->srv_sum,
            'SUM_sum' => (
                $result_full->dop_sum +
                $result_full->now_sum +
                $result_full->to_sum +
                $result_full->kuz_sum +
                $result_full->store_sum
            ),
            'for_date' => $latestReport->for_date,
            'created_at' => $latestReport->created_at,
            'updated_at' => $latestReport->updated_at,
            'have' => true
        ];
    }

    // Метод для возвращения данных по умолчанию, если отчета нет
    private function defaultServiceReport()
    {
        return [
            'dop' => 0,
            'now' => 0,
            'to' => 0,
            'kuz' => 0,
            'store' => 0,
            'zap' => 0,
            'srv' => 0,
            'SUM' => 0,
            'dop_sum' => 0,
            'now_sum' => 0,
            'to_sum' => 0,
            'kuz_sum' => 0,
            'store_sum' => 0,
            'zap_sum' => 0,
            'srv_sum' => 0,
            'SUM_sum' => 0,
            'for_date' => null,
            'created_at' => null,
            'updated_at' => null,
            'have' => null
        ];
    }

    // Метод для получения названия месяца на русском языке
    private function getRussianMonthName($date)
    {
        // Определение номера месяца
        $monthNumber = date('n', strtotime($date));
        // Массив с названиями месяцев на русском языке
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

        // Возвращение названия месяца
        return $months[$monthNumber];
    }
}
