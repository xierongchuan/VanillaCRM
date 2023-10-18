<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
		$request->validate([
			'file' => 'required|max:51200',
			'note' => 'string|max:5500'
		]);

		if($request -> file('file') ->getMimeType() !== "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") return redirect()->back()->withErrors('Файл должен быть типа xlsx (Exel).');

		$inputData = request()->all(); // Получаем все данные из запроса

		$company = Company::find(Auth::user() -> com_id);

		if(!empty($company -> data)) {
			$workers = (array)((array)json_decode($company -> data))['Продажи'];
		} else {
			$workers = [];
		}

//		var_dump($workers);

		foreach ($inputData as $key => $value) {
			if (preg_match('/^worker_sold_(\d+)$/', $key, $matches) && is_numeric($value)) {
				$workerNumber = $matches[1]; // Извлекаем номер рабочего
				$workerName = $inputData['worker_name_' . $workerNumber]; // Извлекаем соответствующее имя рабочего

				$month = (int)$value;

				if(!empty($workers) && !$request -> close_month) {
					$month = (int) $workers[$workerNumber]->month;
					$month += (int)$value;
				}

				$workers[$workerNumber] = [
					'name' => (string)$workerName,
					'sold' => (int)$value,
					'month' => (int)$month
				];
			}
		}

//		var_dump($workers);
//		return false;

		$sheet_data = [
			'Дата' => '',

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

			'Заметка' => $request -> note,

			'Начало списка продаж' => '',
			'Продажи' => $workers
		];

		$sheet_data['Дата'] = date('Y-m-d H:i:s');

		$permission_data = (Permission::where('value', 'report_xlsx') -> first()) -> data;

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
		foreach($wsheet -> getRowIterator() as $row) {
			$cellIterate = $row->getCellIterator();
			$cellIterate -> setIterateOnlyExistingCells(true);
			echo "<tr>";
			foreach($cellIterate as $cell){
				$cell_address = $cell->getCoordinate(); // Получаем адрес ячейки
				$cell_letter = preg_replace('/[0-9]/', '', $cell_address); // Убираем цифры из адреса
				$cell_num = preg_replace('/[A-z]/', '', $cell_address); // Убираем Буквы из адреса

				if(($cell_letter == 'A') && $cell -> getRow() >= 4 && $cell -> getRow() < 35) {
					$date = date('d.m.Y', Date::excelToTimestamp((int)$cell->getValue()));
					if($date == date('d.m.Y')) {
						$sheet_data['Договора'] = $wsheet -> getCell($rule['Договора'].$cell_num) -> getCalculatedValue();
						$sheet_data['Оплата Кол-во'] = $wsheet -> getCell($rule['Оплата Кол-во'].$cell_num) -> getCalculatedValue();
						$sheet_data['Оплата Сумм'] = $wsheet -> getCell($rule['Оплата Сумм'].$cell_num) -> getCalculatedValue();
						$sheet_data['Доплата'] = $wsheet -> getCell($rule['Доплата'].$cell_num) -> getCalculatedValue();
						$sheet_data['Лизинг'] = $wsheet -> getCell($rule['Лизинг'].$cell_num) -> getCalculatedValue();
						$sheet_data['Всего'] = $wsheet -> getCell($rule['Всего'].$cell_num) -> getCalculatedValue();
					}
				}
			}
		}

		if($sheet_data['Договора'] == '') return redirect()->back()->withErrors('Не найден сегодняшний отчёт');


		$sheet_data['План Кол-во'] = $wsheet -> getCell($rule['План Кол-во']) -> getCalculatedValue();
		$sheet_data['План Сумм'] = $wsheet -> getCell($rule['План Сумм']) -> getCalculatedValue();
		$sheet_data['Факт Кол-во'] = $wsheet -> getCell($rule['Факт Кол-во']) -> getCalculatedValue();
		$sheet_data['Факт Сумм'] = $wsheet -> getCell($rule['Факт Сумм']) -> getCalculatedValue();
		$sheet_data['2 Договора'] = $wsheet -> getCell($rule['2 Договора']) -> getCalculatedValue();
		$sheet_data['2 Конверсия'] = round(($wsheet -> getCell($rule['2 Конверсия']) -> getCalculatedValue() * 100), 2);

		$num1 = (int)$wsheet -> getCell($rule['Факт Кол-во']) -> getCalculatedValue();
		$num2 = (int)$wsheet -> getCell($rule['План Кол-во']) -> getCalculatedValue();
		$result = ($num1/($num2/100));
		$sheet_data['% от кол-во'] =  round($result, 2);

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

		$wsheet->mergeCells("A".($sales_s).":B".($sales_s));
		$wsheet->setCellValue("A".($sales_s), 'Имя');
		$wsheet->setCellValue("C".($sales_s), 'Штук');
		$wsheet->setCellValue("D".($sales_s), 'Мес');
		$wsheet->getStyle("A".$sales_s.":D".$sales_s)->getFont()->setBold(true);
		$wsheet->getStyle('A'.($sales_s).':D'.($sales_s))->applyFromArray($styleArray);

		foreach ($sheet_data['Продажи'] as $key => $sold) {
			$num_address = $key+$sales_s;
			$wsheet->getStyle("A".$num_address.":D".$num_address)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
			$wsheet->mergeCells("A".$num_address.":B".$num_address);
			$wsheet->setCellValue("A".$num_address, $sold['name']);
			$wsheet->setCellValue("C".$num_address, $sold['sold']);
			$wsheet->setCellValue("D".$num_address, $sold['month']);
			$wsheet->getStyle('A'.$num_address.':D'.$num_address)->applyFromArray($styleArray);
		}


		// Удаление проблемной ячейки
		$wsheet->setCellValue('X13', '');

		// Создаем объект для записи в XLSX файл
		$writer = IOFactory::createWriter($sheet, 'Xlsx');

		if ($request->file('file')->isValid()) {
			if($request -> close_month) {
				$file_name = $company -> name.'_' . date('Y-m-d_H:i:s') . '_' . $sheet_data['3 Оплата'] . '_' . $sheet_data['Факт Кол-во'] . '.xlsx';
				$writer->save(storage_path('app/public/archive/'.$file_name), 1);

				$company -> data = json_encode($sheet_data);
				$company -> save();

				return redirect()->route('home.index')->with('success', 'Отчёт с закрытием месяца успешно загружен.');
			} else {
				$company -> data = json_encode($sheet_data);
				$company -> save();

				return redirect()->route('home.index')->with('success', 'Отчёт успешно загружен.');
			}
		} else {
			return redirect()->back()->withErrors('Ошибка при загрузке файла.');
		}
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
