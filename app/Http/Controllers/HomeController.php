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
                case 'admin':
                    return $this->adminView();
                case 'user':
                    return $this->userView();
                default:
                    echo "Кто ты?";
                    return false;
            }
        } else {
            return view('home');
        }
    }

    private function adminView()
    {
        $companies = Company::all();
        $files_data = $this->getFilesData('archive');
        $last_repor_urls = $this->getLastReportUrls($companies);
        $srv_reps = $this->getServiceReports($companies);

        return view('home', compact('companies', 'files_data', 'last_repor_urls', 'srv_reps'));
    }

    private function userView()
    {
        $company = Company::find(Auth::user()->com_id);
        $department = Department::find(Auth::user()->dep_id);
        $post = Post::find(Auth::user()->post_id);
        $permissions = Permission::whereIn('id', json_decode($post->permission, true))->get();
        $permission_vals = $permissions->pluck('value')->toArray();

        $data = (object)[
            'company' => $company,
            'department' => $department,
            'post' => $post,
            'perm' => $permission_vals,
        ];

        $srv_rep = $this->getServiceReport($company->id);

        return view('home', compact('company', 'data', 'srv_rep'));
    }

    private function getFilesData($folder)
    {
        $path = storage_path('app/public/' . $folder);

        if (!File::exists($path)) {
            return [];
        }

        $files = File::allFiles($path);
        $files_data = [];

        foreach ($files as $file) {
            $filePath = 'storage/' . $folder . '/' . $file->getFilename();
            $file_name_data = explode('_', basename($file));

            $files_data[] = (object)[
                'name' => basename($file),
                'company' => $file_name_data[0],
                'url' => (string)asset($filePath),
                'date' => $this->getRussianMonthName($file_name_data[1]),
                'sum' => number_format((int)$file_name_data[3], 0, '', ' '),
                'count' => number_format((int)$file_name_data[4], 0, '', ' '),
                'fakt' => number_format(@(int)$file_name_data[5], 0, '', ' ')
            ];
        }

        return array_reverse($files_data);
    }

    private function getLastReportUrls($companies)
    {
        $last_repor_urls = [];

        foreach ($companies as $company) {
            $l_r_f_n = (string)@((array)json_decode($company->data))['Last File'];
            $l_r_path = storage_path('app/public/tmp/' . $l_r_f_n);
            $l_r_path_proj = 'storage/app/public' . str_replace(storage_path('app/public'), '', $l_r_path);
            $last_repor_urls[] = asset($l_r_path_proj);
        }

        return $last_repor_urls;
    }

    private function getServiceReports($companies)
    {
        $srv_reps = [];

        foreach ($companies as $company) {
            $company->fields = Field::where('com_id', $company->id)->get();
            $srv_reps[$company->id] = $this->getServiceReport($company->id);
        }

        return $srv_reps;
    }

    private function getServiceReport($companyId)
    {
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();

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

        if (!$latestReport) {
            return $this->defaultServiceReport();
        }

        $result = json_decode($latestReport->data);

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
