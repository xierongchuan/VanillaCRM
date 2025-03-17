<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Permission;
use App\Models\User;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as WXlsx;

class ModController extends Controller
{
    public function create_worker(Company $company)
    {

        $req = request()->validate([
            'login' => 'required|unique:users',
            'full_name' => 'required|min:3|max:30',
            'phone_number' => 'required|string|min:1|max:22',
            'password' => 'required|min:6|max:256'
        ]);

        $user = new User();
        $user->login = $req['login'];
        $user->role = 'user';
        $user->password = Hash::make($req['password']);
        $user->com_id = $company->id;
        $user->dep_id = Auth::user()->dep_id;
        $user->full_name = $req['full_name'];
        $user->phone_number = str_replace(' ', '', $req['phone_number']);
        $user->save();

        return redirect()->route('user.permission');
    }

    public function report_service(Company $company, Request $request)
    {

        $request->validate([
            'date' => 'required',
            'dop' => 'required|numeric',
            'now' => 'required|numeric',
            'to' => 'required|numeric',
            'kuz' => 'required|numeric',
            'store' => 'required|numeric',

            'zap' => 'required|numeric',
            'srv' => 'required|numeric',
        ]);

        $data = [
            'dop' => $request->dop,
            'now' => $request->now,
            'to' => $request->to,
            'kuz' => $request->kuz,
            'store' => $request->store,

            'zap' => $request->zap,
            'srv' => $request->srv
        ];

        $forDate = date('Y-m-d H:i:s', strtotime($request->date));

        $reportModel = Report::where([
            'com_id' => $company->id,
            'for_date' => $forDate,
            'type' => 'report_service'
        ])->first();

        if ($reportModel) {
            // Если запись существует, обновим ее
            $reportModel->data = json_encode($data);
            $reportModel->updated_at = now()->toDateTimeString();
            $reportModel->save();
        } else {
            // Если запись не существует, создадим новую
            Report::create([
                'com_id' => $company->id,
                'for_date' => $forDate,
                'type' => 'report_service',
                'data' => json_encode($data),
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ]);
        }
        return redirect()->route('home.index')->with('success', 'Отчёт успешно загружен.');

    }

    public function report_caffe(Company $company, Request $request)
    {

        $request->validate([
            'date' => 'required',
            'profit_nal' => 'required|numeric',
            'profit_bez_nal' => 'required|numeric',
            'waste_nal' => 'required|numeric',
            'waste_bez_nal' => 'required|numeric',
            'remains_nal' => 'required|numeric',
            'remains_bez_nal' => 'required|numeric',
            'safe_nal' => 'required|numeric',
            'safe_bez_nal' => 'required|numeric',
        ]);

        $data = [
            'profit_nal' => (int)$request->profit_nal,
            'profit_bez_nal' => (int)$request->profit_bez_nal,
            'waste_nal' => (int)$request->waste_nal,
            'waste_bez_nal' => (int)$request->waste_bez_nal,
            'remains_nal' => (int)$request->remains_nal,
            'remains_bez_nal' => (int)$request->remains_bez_nal,
            'safe_nal' => (int)$request->safe_nal,
            'safe_bez_nal' => (int)$request->safe_bez_nal,
        ];

        $forDate = date('Y-m-d H:i:s', strtotime($request->date));

        $reportModel = Report::where([
            'com_id' => $company->id,
            'for_date' => $forDate,
            'type' => 'report_caffe'
        ])->first();

        if ($reportModel) {
            // Если запись существует, обновим ее
            $reportModel->data = json_encode($data);
            $reportModel->updated_at = now()->toDateTimeString();
            $reportModel->save();
        } else {
            // Если запись не существует, создадим новую
            Report::create([
                'com_id' => $company->id,
                'for_date' => $forDate,
                'type' => 'report_caffe',
                'data' => json_encode($data),
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ]);
        }
        return redirect()->route('home.index')->with('success', 'Отчёт успешно загружен.');

    }

    public function report_xlsx_sales(Company $company)
    {
        // Получаем последний отчет report_xlsx для данной компании
        $lastReport = Report::where('com_id', $company->id)
            ->where('type', 'report_xlsx')
            ->orderBy('for_date', 'desc')
            ->first();

        if (!$lastReport) {
            return redirect()->route('home.index')->with('error', 'Отчет не найден.');
        }

        // Декодируем данные отчета
        $data = json_decode($lastReport->data, true);

        // Проверка на наличие данных о продажах
        if (!isset($data['Sales'])) {
            return redirect()->route('home.index')->with('error', 'Отчет не содержит данных о продажах.');
        }

        // Получаем все входные данные из запроса
        $inputData = request()->all();

        // Инициализируем массив для хранения данных работников
        $workers = $data['Sales'];

        // Перебираем входные данные и обновляем данные о продажах работников
        foreach ($inputData as $key => $value) {
            if (preg_match('/^worker_name_(\d+)$/', $key, $matches)) {
                $workerNumber = $matches[1];
                if (isset($inputData['worker_sold_' . $workerNumber])) {
                    $workerSold = (int) $inputData['worker_sold_' . $workerNumber];
                    $workers[$workerNumber] = $workerSold;
                }
            }
        }

        // Обновляем данные о продажах в отчете
        $data['Sales'] = $workers;
        $lastReport->data = json_encode($data);

        // Сохраняем обновленный отчет
        $lastReport->save();

        return redirect()->route('home.index')->with('success', 'Продажи успешно изменены.');
    }



    private function numberToColumn($number)
    {
        $column = "";
        while ($number > 0) {
            $remainder = ($number - 1) % 26;
            $column = chr(65 + $remainder) . $column;
            $number = (int) (($number - $remainder) / 26);
        }
        return $column;
    }

}
