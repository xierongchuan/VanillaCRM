<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Department;
use App\Models\Permission;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class HomeController extends Controller
{
	public function index(): \Illuminate\Contracts\View\View
	{
		if(Auth::check()){
			switch (Auth::user() -> role) {
				case ('admin'):
					$companies = Company::all();

					$files = File::allFiles(storage_path('app/public/archive'));

					// Инициализируем пустой массив для хранения URL файлов
					$files_data = [];

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
							'count' => number_format((int)$file_name_data[4], 0, '', ' ')
						];

						// Добавляем URL в массив
						$files_data[] = (object)$file_data;
					}

					$files_data = array_reverse($files_data);

					return view('home', compact('companies', 'files_data'));
					break;

				case ('user'):
					$company = Company::find(Auth::user() -> com_id);
					return view('home', compact('company'));
					break;
			}
		} else {
			return view('home');
		}

	}

	private function getRussianMonthName($date) {
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
