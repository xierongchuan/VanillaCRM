<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ArchiveController extends Controller
{
	public function archive(Company $company) {
		$files = File::allFiles(storage_path('app/public/archive'));

		// Инициализируем пустой массив для хранения URL файлов
		$files_data = [];

		// Перебираем каждый файл и получаем его URL
		foreach ($files as $file) {

			// Получаем путь к файлу относительно public директории
			$filePath = 'storage/app/public' . str_replace(storage_path('app/public'), '', $file);
			$file_name_data = explode('_', basename($file));
			if($file_name_data[0] != $company -> name) {
				continue;
			}
			// Генерируем данные файла
			$file_data = [
				'name' => basename($file),
				'url' => (string)asset($filePath),
				'date' => $file_name_data[1].' '.$file_name_data[2].' '.$this->getRussianMonthName($file_name_data[1]),
				'sum' => number_format((int)$file_name_data[3], 0, '', ' '),
				'count' => number_format((int)$file_name_data[4], 0, '', ' ')
			];

			// Добавляем URL в массив
			$files_data[] = (object)$file_data;
		}

		$files_data = array_reverse($files_data);

		return view('company.archive', compact('company', 'files_data'));
	}

	public function remove_last_report(Company $company) {
		if(empty($company -> data)) {
			return redirect() -> route('home.index') -> withErrors('Последний отчтёт и так был удалён!');
		}

		$file = (string)@((array)json_decode($company -> data))['Last File'];
		$file_tmp_path = storage_path('app/public/tmp/'.$file);
		$file_path = storage_path('app/public/archive/'.$file);

		// Проверяем, существует ли файл
		if(File::exists($file_tmp_path)) {
			// Удаляем файл
			File::delete($file_tmp_path);
		}

		// Проверяем, существует ли файл
		if(File::exists($file_path)) {
			// Удаляем файл
			File::delete($file_path);
		}

		$company -> data = '';
		$company -> save();

		return redirect() -> route('home.index') -> with('success', 'Последний отчёт успешно удалён!');
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
