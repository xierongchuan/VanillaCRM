<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Department;
use App\Models\Permission;
use App\Models\Post;
use App\Models\Field;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            switch (Auth::user()->role) {
                case('admin'):
                    $companies = Company::all();


                    $files = File::allFiles(storage_path('app/public/archive'));

                    $tmp_files = File::allFiles(storage_path('app/public/tmp'));

                    // Инициализируем пустой массив для хранения URL файлов
                    $files_data = [];
                    $last_repor_urls = [];


                    $srv_reps = [];


                    // Перебираем каждый файл и получаем его URL
                    foreach ($files as $file) {
                        // Получаем путь к файлу относительно public директории
                        $filePath = 'storage/app/public' . str_replace(storage_path('app/public'), '', $file);
                        $file_name_data = explode('_', basename($file));

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

                    foreach ($companies as $company) {
                        $l_r_f_n = (string)@((array)json_decode($company->data))['Last File'];
                        $l_r_path = storage_path('app/public/tmp/' . $l_r_f_n);
                        $l_r_path_proj = 'storage/app/public' . str_replace(storage_path('app/public'), '', $l_r_path);
                        $last_repor_urls[] = asset($l_r_path_proj);

                        $company->fields = Field::where('com_id', $company->id)->get(); // Получаем сотрудников для компании

                        $startDate = now()->startOfMonth(); // Начало текущего месяца
                        $endDate = now()->endOfMonth(); // Конец текущего месяца

                        $companyId = $company->id;

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

                        $latestReport = DB::table('reports')
                            ->where('type', 'report_service')
                            ->where('com_id', $companyId)
                            ->orderByDesc('for_date')
                            ->first();

                        if ($latestReport == null) {
                            $srv_rep = [
                                'dop' => 0,
                                'now' => 0,
                                'to' => 0,
                                'kuz' => 0,
                                'store' => 0,
                                'zap' => 0,
                                'srv' => 0,
                                'SUM' => (0),
                                'dop_sum' => 0,
                                'now_sum' => 0,
                                'to_sum' => 0,
                                'kuz_sum' => 0,
                                'store_sum' => 0,
                                'zap_sum' => 0,
                                'srv_sum' => 0,
                                'SUM_sum' => (0),

                                'for_date' => null,
                                'created_at' => null,
                                'have' => null
                            ];
                            $srv_reps[$company->id] = $srv_rep;
                            continue;
                        }

                        $result = json_decode($latestReport->data);

                        // Преобразуем результат в массив
                        $srv_rep = [
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
                            'have' => true
                        ];


                        $srv_reps[$company->id] = $srv_rep;

                    }

                    $files_data = array_reverse($files_data);//dd($files_data);


                    return view('home', compact('companies', 'files_data', 'last_repor_urls', 'srv_reps'));
                    break;

                case('user'):
                    $company = Company::find(Auth::user()->com_id);

                    $department = Department::find(Auth::user()->dep_id);

                    $post = Post::find(@Auth::user()->post_id);
                    $permission_ids = (array)json_decode(@$post->permission);
                    $permissions = Permission::whereIn('id', @$permission_ids)->get();
                    $permission_vals = @$permissions->pluck('value')->toArray();

                    $data = (object)[
                        'company' => $company,
                        'department' => $department,
                        'post' => $post,
                        'perm' => $permission_vals,
                    ];

                    $startDate = now()->startOfMonth(); // Начало текущего месяца
                    $endDate = now()->endOfMonth(); // Конец текущего месяца

                    $companyId = $company->id;

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

                    $latestReport = DB::table('reports')
                        ->where('type', 'report_service')
                        ->where('com_id', $companyId)
                        ->orderByDesc('for_date')
                        ->first();

                    if ($latestReport == null) {
                        $srv_rep = [
                            'dop' => 0,
                            'now' => 0,
                            'to' => 0,
                            'kuz' => 0,
                            'store' => 0,
                            'zap' => 0,
                            'srv' => 0,
                            'SUM' => (0),
                            'dop_sum' => 0,
                            'now_sum' => 0,
                            'to_sum' => 0,
                            'kuz_sum' => 0,
                            'store_sum' => 0,
                            'zap_sum' => 0,
                            'srv_sum' => 0,
                            'SUM_sum' => (0),

                            'for_date' => null,
                            'created_at' => null,
                            'have' => null
                        ];
                    } else {

                        $result = json_decode($latestReport->data);

                        // Преобразуем результат в массив
                        $srv_rep = [
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
                            'have' => true
                        ];
                    }

                    return view('home', compact('company', 'data', 'srv_rep'));
                    break;

                default:
                    echo "Кто ты?";
                    return false;
                    break;
            }
        } else {
            return view('home');
        }

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
