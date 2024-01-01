<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Permission;
use App\Models\User;
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
	public function create_worker(Company $company) {

		$req = request() -> validate([
			'login' => 'required|unique:users',
			'full_name' => 'required|min:3|max:30',
			'phone_number' => 'required|string|min:1|max:22',
			'password' => 'required|min:6|max:256'
		]);

		$user = new User();
		$user -> login = $req['login'];
		$user -> role = 'user';
		$user -> password = Hash::make($req['password']);
		$user -> com_id = $company -> id;
		$user -> dep_id = Auth::user() -> dep_id;
		$user -> full_name = $req['full_name'];
		$user -> phone_number = str_replace(' ', '', $req['phone_number']);
		$user -> save();

		return redirect() -> route('user.permission');
	}

	public function report_xlsx(Request $request) {

		$inputData = request()->all(); // Получаем все данные из запроса

		$company = Company::find(Auth::user() -> com_id);

		if(!empty($company -> data)) {
			$com_dat = (array)json_decode($company -> data);
			if($com_dat['Clear Sales']) {
				$workers = [];
			} else {
				$workers = (array)((array)json_decode($company -> data))['Продажи'];
			}
		} else {
			$workers = [];
		}

		foreach ($inputData as $key => $value) {
			if (preg_match('/^worker_name_(\d+)$/', $key, $matches)) {
				$workerNumber = $matches[1]; // Извлекаем номер рабочего
				$workerValue = $inputData['worker_sold_' . $workerNumber];
				$workerName = $inputData['worker_name_' . $workerNumber]; // Извлекаем соответствующее имя рабочего

				$month = (int)$workerValue;

				if(!empty($workers) && !$request -> close_month) {
					$month = (int)@$workers[$workerNumber]->month;
					$month += (int)$workerValue;
				}

				$workers[$workerNumber] = [
					'name' => (string)$workerName,
					'sold' => (int)$workerValue,
					'month' => (int)$month
				];
			}
		}

		$sheet_data = [
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

			'Заметка' => @$request -> note,

			'Начало списка продаж' => '',
			'Продажи' => $workers,
			'Last File' => '',

			'Clear Sales' => false
		];

		if($request -> close_month && !$request->hasFile('file')) {
			$company = Company::find(Auth::user() -> com_id);
			$file_name = (string)@((array)json_decode($company -> data))['Last File'];
//			if(empty($file_name)) return redirect()->back()->withErrors('Месяц и так закрыт!');
			$sourcePath = storage_path('app/public/tmp/'.$file_name); // Путь к оригинальному файлу
			$destinationPath = storage_path('app/public/archive/'.$file_name); // Путь к копии файла
			// Проверяем, существует ли файл в исходной папке
			if (File::exists($sourcePath)) {
				// Копируем файл
				File::copy($sourcePath, $destinationPath);

				$sheet_data['Clear Sales'] = true;
				$company -> data = json_encode($sheet_data);
				$company -> save();

				return redirect()->back()->with('success', 'Месяц успешно закрыт');
			}

			return redirect()->back()->withErrors('Месяц уже был закрыт');
//			Storage::move('public/tmp/' . $file_name, 'public/archive/' . $file_name);
		}

		$request->validate([
			'file' => 'required|max:51200',
			'note' => 'max:5500'
		]);

		if($request -> file('file') ->getMimeType() !== "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") return redirect()->back()->withErrors('Файл должен быть типа xlsx (Exel).');


		$permission_data = (Permission::where('com_id', $company -> id) -> where('value', 'report_xlsx') -> first()) -> data;

		$lines = explode(PHP_EOL, $permission_data);
		$rule = [];
		foreach ($lines as $line) {
			if (trim($line)) {
				list($key, $value) = array_map('trim', explode('=', $line, 2));
				$rule[$key] = $value;
			}
		}

		$sheet = IOFactory::load($request -> file('file'));
		$wsheet = $sheet -> getActiveSheet();
		$date_m = '';
		foreach($wsheet -> getRowIterator() as $row) {
			$cellIterate = $row->getCellIterator();
			$cellIterate -> setIterateOnlyExistingCells(true);
			foreach($cellIterate as $cell){
				$cell_address = $cell->getCoordinate(); // Получаем адрес ячейки
				$cell_letter = preg_replace('/[0-9]/', '', $cell_address); // Убираем цифры из адреса
				$cell_num = preg_replace('/[A-z]/', '', $cell_address); // Убираем Буквы из адреса

				if(($cell_letter == 'A') && $cell -> getRow() >= $rule['Начало отчётов'] && $cell -> getRow() < 35) {
					if(!$request -> close_month) {
						$date = date('d.m.Y', Date::excelToTimestamp((int)$cell->getValue()));
						if($date == date('d.m.Y')) {
							$sheet_data['Договора'] = $wsheet -> getCell($rule['Договора'].$cell_num) -> getCalculatedValue();
							$sheet_data['Оплата Кол-во'] = $wsheet -> getCell($rule['Оплата Кол-во'].$cell_num) -> getCalculatedValue();
							$sheet_data['Оплата Сумм'] = $wsheet -> getCell($rule['Оплата Сумм'].$cell_num) -> getCalculatedValue();
							$sheet_data['Доплата'] = $wsheet -> getCell($rule['Доплата'].$cell_num) -> getCalculatedValue();
							$sheet_data['Лизинг'] = $wsheet -> getCell($rule['Лизинг'].$cell_num) -> getCalculatedValue();
							$sheet_data['Всего'] = $wsheet -> getCell($rule['Всего'].$cell_num) -> getCalculatedValue();
						}
					} else {//if($cell_num > 25) dd((string)$wsheet -> getCell($rule['Договора'].$cell_num) -> getCalculatedValue());
						if((string)$wsheet -> getCell($rule['Договора'].$cell_num) -> getCalculatedValue() == '' || $cell_num >= 34) {
							if($sheet_data['Договора'] == '') {
								$date_m = date('Y-m-d', Date::excelToTimestamp((int)$wsheet -> getCell('A'.($cell_num - 1)) -> getValue()));
								$sheet_data['Договора'] = $wsheet -> getCell($rule['Договора'].($cell_num - 1)) -> getCalculatedValue();
								$sheet_data['Оплата Кол-во'] = $wsheet -> getCell($rule['Оплата Кол-во'].($cell_num - 1)) -> getCalculatedValue();
								$sheet_data['Оплата Сумм'] = $wsheet -> getCell($rule['Оплата Сумм'].($cell_num - 1)) -> getCalculatedValue();
								$sheet_data['Доплата'] = $wsheet -> getCell($rule['Доплата'].($cell_num - 1)) -> getCalculatedValue();
								$sheet_data['Лизинг'] = $wsheet -> getCell($rule['Лизинг'].($cell_num - 1)) -> getCalculatedValue();
								$sheet_data['Всего'] = $wsheet -> getCell($rule['Всего'].($cell_num - 1)) -> getCalculatedValue();
							}
						}
					}
				}
			}
		}

		// dd($sheet_data);

		if($sheet_data['Договора'] == '') return redirect()->back()->withErrors('Не найден сегодняшний отчёт');


		$sheet_data['План Кол-во'] = $wsheet -> getCell($rule['План Кол-во']) -> getCalculatedValue();
		$sheet_data['План Сумм'] = $wsheet -> getCell($rule['План Сумм']) -> getCalculatedValue();
		$sheet_data['Факт Кол-во'] = $wsheet -> getCell($rule['Факт Кол-во']) -> getCalculatedValue();
		$sheet_data['Факт Сумм'] = $wsheet -> getCell($rule['Факт Сумм']) -> getCalculatedValue();
		$sheet_data['2 Договора'] = $wsheet -> getCell($rule['2 Договора']) -> getCalculatedValue();
		$sheet_data['% от кол-во'] = round(($wsheet -> getCell($rule['% от кол-во']) -> getCalculatedValue() * 100), 2);

		$num1 = (int)$wsheet -> getCell($rule['Факт Кол-во']) -> getCalculatedValue();
		$num2 = (int)$wsheet -> getCell($rule['2 Договора']) -> getCalculatedValue();
		$result = 0;
		if($num1 != 0 && $num2 != 0) $result = $num1/($num2/100);
		$sheet_data['2 Конверсия'] =  round($result, 2);

		$sheet_data['% от сумм'] = round(($wsheet -> getCell($rule['% от сумм']) -> getCalculatedValue()), 2);

		$sheet_data['3 Оплата'] = $wsheet -> getCell($rule['3 Оплата']) -> getCalculatedValue();
		$sheet_data['3 Доплата'] = $wsheet -> getCell($rule['3 Доплата']) -> getCalculatedValue();
		$sheet_data['3 Лизинг'] = $wsheet -> getCell($rule['3 Лизинг']) -> getCalculatedValue();
		$sheet_data['3 Остаток'] = $wsheet -> getCell($rule['3 Остаток']) -> getCalculatedValue();

		$sheet_data['5 Через банк шт'] = $wsheet -> getCell($rule['5 Через банк шт']) -> getCalculatedValue();
		$sheet_data['5 Через банк сумма'] = $wsheet -> getCell($rule['5 Через банк сумма']) -> getCalculatedValue();
		$sheet_data['5 Через лизинг шт'] = $wsheet -> getCell($rule['5 Через лизинг шт']) -> getCalculatedValue();
		$sheet_data['5 Через лизинг сумма'] = $wsheet -> getCell($rule['5 Через лизинг сумма']) -> getCalculatedValue();
		$sheet_data['5 Итог шт'] = $wsheet -> getCell($rule['5 Итог шт']) -> getCalculatedValue();
		$sheet_data['5 Cумма'] = $wsheet -> getCell($rule['5 Cумма']) -> getCalculatedValue();

		$sheet_data['Начало списка продаж'] = (int)$rule['Начало списка продаж'];


		// Создания списка продаж в самом xlsx файле
		$sales_s = $sheet_data['Начало списка продаж'];

		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN,
					'color' => ['argb' => '00000000'],
				],
			],
		];

		$managers = (array)$sheet_data['Продажи'];
		$totalSum = 0;
		foreach ($managers as $manager) {
			$totalSum += (int)$manager['month'];
		}

		$percentages = [];
		foreach ($managers as $key => $manager) {
			if((int)$manager['month'] == 0) {
				$percentages[$key] = 0;
				continue;
			}

			$percentage = ($manager['month'] / $totalSum) * 100;
			$percentages[$key] = round($percentage, 1);
		}

		$wsheet->mergeCells("A".($sales_s).":B".($sales_s));
		$wsheet->setCellValue("A".($sales_s), 'Имя');
		$wsheet->setCellValue("C".($sales_s), 'Штук');
		$wsheet->setCellValue("D".($sales_s), 'Мес');
		$wsheet->setCellValue("E".($sales_s), '%');
		$wsheet->getStyle("A".$sales_s.":E".$sales_s)->getFont()->setBold(true);
		$wsheet->getStyle('A'.($sales_s).':E'.($sales_s))->applyFromArray($styleArray);

		foreach ($sheet_data['Продажи'] as $key => $sold) {
			$sold = (array)$sold;
			$num_address = $key+$sales_s;
			$wsheet->getStyle("A".$num_address.":E".$num_address)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
			$wsheet->mergeCells("A".$num_address.":B".$num_address);
			$wsheet->setCellValue("A".$num_address, $sold['name']);
			$wsheet->setCellValue("C".$num_address, $sold['sold']);
			$wsheet->setCellValue("D".$num_address, $sold['month']);
			$wsheet->setCellValue("E".$num_address, $percentages[$key]);
			$wsheet->getStyle('A'.$num_address.':E'.$num_address)->applyFromArray($styleArray);
		}


		// Удаление проблемной ячейки
		$wsheet->setCellValue('X13', '');

		// Создаем объект для записи в XLSX файл
		$writer = IOFactory::createWriter($sheet, 'Xlsx');

		if ($request->file('file')->isValid()) {
			// Путь к старому файлу
			$old_file = (string)@((array)json_decode($company -> data))['Last File'];
			$old_file_path = storage_path('app/public/tmp/'.$old_file);

			// Проверяем, существует ли файл
			if(File::exists($old_file_path)) {
				// Удаляем файл
				File::delete($old_file_path);
			}

			// Путь к новому файлу
			$file_name = ($date_m != '') ? $company -> name.'_' . $date_m . date('H:i:s') . '_' . $sheet_data['5 Cумма'] . '_' . $sheet_data['5 Итог шт'] . '_' . $sheet_data['Факт Кол-во'] . '.xlsx' : $company -> name.'_' . date('Y-m-d_H:i:s') . '_' . $sheet_data['5 Cумма'] . '_' . $sheet_data['5 Итог шт'] . '_' . $sheet_data['Факт Кол-во'] . '.xlsx';

			$sheet_data['Last File'] = $file_name;

			if($request -> close_month) {
				$writer->save(storage_path('app/public/archive/'.$file_name), 1);

				$sheet_data['Clear Sales'] = true;
				$company -> data = json_encode($sheet_data);
				$company -> save();


				return redirect()->route('home.index')->with('success', 'Отчёт с закрытием месяца успешно загружен.');
			} else {
				$writer->save(storage_path('app/public/tmp/'.$file_name), 1);
				$company -> data = json_encode($sheet_data);
				$company -> save();

				return redirect()->route('home.index')->with('success', 'Отчёт успешно загружен.');
			}
		} else {
			return redirect()->back()->withErrors('Ошибка при загрузке файла.');
		}
	}

	public function report_xlsx_sales(Company $company) {
		$inputData = request()->all();

		$data = (array)json_decode($company -> data);

		$workers = (array)$data['Продажи'];

		foreach ($inputData as $key => $value) {
			if (preg_match('/^worker_month_(\d+)$/', $key, $matches) && is_numeric($value)) {
				$workerNumber = $matches[1]; // Извлекаем номер рабочего
				$workerMonth = $inputData['worker_month_' . $workerNumber];
				$workerName = $inputData['worker_name_' . $workerNumber]; // Извлекаем соответствующее имя рабочего

				$workers[$workerNumber] = [
					'name' => (string)$workerName,
					'sold' => (int)$workers[$workerNumber] -> sold,
					'month' => (int)$workerMonth
				];
			}
		}

		$data['Продажи'] = $workers;
		$company -> data = json_encode($data);
		$company -> save();

		return redirect()->route('home.index')->with('success', 'Продажи успешно изменены.');
	}

	private function numberToColumn($number) {
		$column = "";
		while ($number > 0) {
			$remainder = ($number - 1) % 26;
			$column = chr(65 + $remainder) . $column;
			$number = (int)(($number - $remainder) / 26);
		}
		return $column;
	}

}
