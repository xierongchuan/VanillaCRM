<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Department;
use App\Models\Post;
use App\Models\Worker;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Keyboard\Keyboard;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class TelegramBotController extends Controller
{

	public function index(): void
	{
		$req = Telegram::getWebhookUpdate();

		$keyboard = Keyboard::make()
			->row([
				Keyboard::button(['text' => 'Войти', 'request_contact' => true]),
				Keyboard::button(['text' => 'Кнопка 2'])
			])
			->row([
				Keyboard::button(['text' => 'Кнопка 3']),
				Keyboard::button(['text' => 'Кнопка 4'])
			]);

		if (@$req->message->document) {
			if ($req -> message -> document -> mimeType == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {

				$file_id = $req->message->document->fileId;
				$token = '6420552414:AAEntQusu0s2kn2gvG9TDEUCsmEZ8r_u310';
				$file_path_req = "https://api.telegram.org/bot$token/getFile?file_id=$file_id";
				$file_path = ((object)json_decode(file_get_contents($file_path_req))->result)->file_path;

				$file_url = 'https://api.telegram.org/file/bot' . $token . '/' . $file_path;

				$file_name = 'KIA_'.date('Y-m-d_H:i:s').'.xlsx';
				$local_file_path = storage_path('app/public/archive/' . $file_name);

				file_put_contents($local_file_path, file_get_contents($file_url));
				// Теперь у вас есть файл сохраненный по локальному пути $local_file_path

//				// Получаем все файлы в директории storage/app/public
//				$files = File::allFiles(storage_path('app/public/archive'));
//
//				// Инициализируем пустой массив для хранения URL файлов
//				$fileUrls = [];
//
//				// Перебираем каждый файл и получаем его URL
//				foreach ($files as $file) {
//					// Получаем путь к файлу относительно public директории
//					$filePath = 'storage/app/public' . str_replace(storage_path('app/public'), '', $file);
//
//					// Генерируем URL файла
//					$fileUrl = asset($filePath);
//
//					// Добавляем URL в массив
//					$fileUrls[] = $fileUrl;
//				}
//				Telegram::sendMessage([
//					'chat_id' => $req -> message -> chat -> id,
//					'text' => (string)json_encode($fileUrls)
//				]);
			}
		}

		if(isset($req -> message -> contact)) {

			if(Worker::where('phone_number', $req -> message -> contact -> phoneNumber) -> exists()) {
				$worker = Worker::where('phone_number', $req -> message -> contact -> phoneNumber) -> first();
//				$worker -> stage = '';
				$worker -> tg_client_id = $req -> message -> contact -> userId;
				$worker -> save();

				Telegram::sendMessage([
					'chat_id' => $req -> message -> chat -> id,
					'text' =>
						'Вы успешно авторизовались "'.$worker -> full_name.'"!'
						.PHP_EOL.
						'Компания: '.(Company::find($worker -> com_id)) -> name
						.PHP_EOL.
						'Департамент: '.(Department::find($worker -> dep_id)) -> name
						.PHP_EOL.
						'Должность: '.((@$worker -> post_id) ? (Post::find($worker -> post_id)) -> name : 'Отсутствует')
					,
					'reply_markup' => $keyboard
				]);
			} else {
				Telegram::sendMessage([
					'chat_id' => $req -> message -> chat -> id,
					'text' => 'Вы не найдены в системе, просим вас попробовать поже.',
					'reply_markup' => $keyboard
				]);
			}

//			Telegram::sendMessage([
//				'chat_id' => $req -> message -> chat -> id,
//				'text' => $req -> message -> contact -> phoneNumber.PHP_EOL.$req -> message -> contact -> userId
//			]);

			return;
		}

		switch ($req -> message -> text) {

			case '/start':

				Telegram::sendMessage([
					'chat_id' => $req -> message -> chat -> id,
					'text' =>
						'Добро пожаловать '.$req -> message -> from -> firstName.'!'
						.PHP_EOL.
						'Для начала авторизуйтесь, нажав кнопку ниже "Войти"'
					,
					'reply_markup' => $keyboard
				]);
				break;

			case '/rnd_com':
				Company::factory()->create();
				Telegram::sendMessage([
					'chat_id' => $req -> message -> chat -> id,
					'text' => (string)json_encode(Company::all()),
					'reply_markup' => $keyboard
				]);
				break;

			default:
				Telegram::sendMessage([
					'chat_id' => $req -> message -> chat -> id,
					'text' => 'Неизвестная команда!'
				]);
				break;
		}

	}
}
