<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Department;
use App\Models\Permission;
use App\Models\Post;
use App\Models\Temp;
use App\Models\User;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Keyboard\Keyboard;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class TelegramBotController extends Controller
{

	public function index(): void
	{
		$req = Telegram::getWebhookUpdate();

//		Telegram::sendMessage([
//			'chat_id' => 5577711248,
//			'text' =>
//				'Тут работает!!!'.$req -> callbackQuery -> data
//		]);

		// Проверка Авторизации
		$user = Worker::where('tg_client_id', $req -> message -> chat -> id) -> first();
		if(isset($user)) {


			/// ТУТ АВТОРИЗОВАННЫЕ

			$token = '6420552414:AAEntQusu0s2kn2gvG9TDEUCsmEZ8r_u310';

			$w_company = Company::where('id', $user -> com_id) -> first();


			// Проверка на право report_xlsx
			$c_permission_xlsx = Permission::where('com_id', $w_company -> id)->where('value', 'report_xlsx') -> first();
			$w_post = Post::where('id', $user -> post_id) -> first();

			if (in_array($c_permission_xlsx -> id, (array)json_decode($w_post -> permission))) {
				// Проверка имеютсяли стадии
				if ($user -> stage) {

					// Проверка стадии кокумента
					if ($user -> stage == 'close_day') {
						$temp = Temp::with('user_id', $user -> id) -> first();

						if ($temp -> step == 'file') {
							if (@$req->message->document) {
								if ($req->message->document->mimeType == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {

									$file_id = $req->message->document->fileId;
									$file_path_req = "https://api.telegram.org/bot$token/getFile?file_id=$file_id";
									$file_path = ((object)json_decode(file_get_contents($file_path_req))->result)->file_path;

									$file_url = 'https://api.telegram.org/file/bot' . $token . '/' . $file_path;

									$file_name = 'KIA_' . date('Y-m-d_H:i:s') . '.xlsx';
									$local_file_path = storage_path('app/public/archive/' . $file_name);

									file_put_contents($local_file_path, file_get_contents($file_url));
									// Теперь у вас есть файл сохраненный по локальному пути $local_file_path

									$keyboard['inline_keyboard'] = [
										[
											['text' => 'forward me to groups', 'callback_data' => 'someString']
										]
									];

									Telegram::sendMessage([
										'chat_id' => $req->message->chat->id,
										'text' =>
											'Теперь выбирайте менеджеров для отчёта.'
										,
										'reply_markup' => $keyboard
									]);
									return;
								}
							}
						}
						Telegram::sendMessage([
							'chat_id' => $req->message->chat->id,
							'text' =>
								'Вам нужно отправить Документ в формате xlsx!'
//							,
//							'reply_markup' => $keyboard
						]);

						return;
					}
				}

			}


			$keyboard = Keyboard::make()
				->row([
					Keyboard::button(['text' => 'Закрыть день']),
					Keyboard::button(['text' => 'Зыкрыть мес'])
				])
				->row([
					Keyboard::button(['text' => '/exit'])
				]);
//				->inline()
//				->row([
//					Keyboard::inlineButton(['text' => 'forward me to groups', 'callback_data' => 'someString'])
//				])

//			if($user -> stage == 'close_day') {
//				$keyboard = Keyboard::make()
//					->row([
//						Keyboard::button(['text' => 'Зыкрыть мес'])
//					]);
//			}


			// Тут обработка команд
			switch ($req->message->text) {

				case '/start':
					Telegram::sendMessage([
						'chat_id' => $req->message->chat->id,
						'text' =>
							'Вы уже авторизованы!'
						,
						'reply_markup' => $keyboard
					]);
					break;

				case 'Закрыть день':
					$user -> stage = 'close_day';
					$temp = new Temp();
					$temp -> user_id = $user -> id;
					$temp -> step = 'document';
					$user -> save();
					$temp -> save();

					Telegram::sendMessage([
						'chat_id' => $req->message->chat->id,
						'text' =>
							'Загрузите Exel (xlsx) документ для отчёта'
						,
						'reply_markup' => $keyboard
					]);
					break;

				case '/exit':
					$user -> tg_client_id = '';
					$user -> save();

					Telegram::sendMessage([
						'chat_id' => $req->message->chat->id,
						'text' => 'Вы успешко вышли из аккаунта!'
					]);
					break;

				default:
					Telegram::sendMessage([
						'chat_id' => $req->message->chat->id,
						'text' => 'Неизвестная команда!'.$keyboard,
						'reply_markup' => json_encode($keyboard)
					]);
					break;
			}


		} else {

			/// ТУТ НЕ АВТОРИЗОВАННЫЕ

			$keyboard = Keyboard::make()
				->row([
					Keyboard::button(['text' => 'Войти', 'request_contact' => true]),
				])
				->row([
					Keyboard::button(['text' => 'Кнопка 4'])
				]);

			if(isset($req -> message -> contact)) {

				if(Worker::where('phone_number', $req -> message -> contact -> phoneNumber) -> exists()) {
					$user = Worker::where('phone_number', $req -> message -> contact -> phoneNumber) -> first();
					$user -> stage = '';
					$user -> tg_client_id = $req -> message -> contact -> userId;
					$user -> save();

					Telegram::sendMessage([
						'chat_id' => $req -> message -> chat -> id,
						'text' =>
							'Вы успешно авторизовались "'.$user -> full_name.'"!'
							.PHP_EOL.
							'Компания: '.(Company::find($user -> com_id)) -> name
							.PHP_EOL.
							'Департамент: '.(Department::find($user -> dep_id)) -> name
							.PHP_EOL.
							'Должность: '.((@$user -> post_id) ? (Post::find($user -> post_id)) -> name : 'Отсутствует')
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

			}

			// Тут обработка команд
			switch ($req->message->text) {

				case '/start':

					Telegram::sendMessage([
						'chat_id' => $req->message->chat->id,
						'text' =>
							'Добро пожаловать ' . $req->message->from->firstName . '!'
							. PHP_EOL .
							'Для начала авторизуйтесь, нажав кнопку ниже "Войти"'
						,
						'reply_markup' => $keyboard
					]);
					break;

				default:
					Telegram::sendMessage([
						'chat_id' => $req->message->chat->id,
						'text' => 'Неизвестная команда!'
					]);
					break;
			}
		}

	}
}
