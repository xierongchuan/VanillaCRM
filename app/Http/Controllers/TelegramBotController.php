<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Keyboard\Keyboard;

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

		switch ($req -> message -> text) {

			case '/start':

				Telegram::sendMessage([
					'chat_id' => $req -> message -> chat -> id,
					'text' => 'Добро пожаловать '.$req -> message -> from -> firstName.'!',
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
