<?php

namespace App\Http\Controllers;

use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Keyboard\Keyboard;

class TelegramBotController extends Controller
{

	public function index(): void
	{
		$req = Telegram::getWebhookUpdate();

		switch ($req -> message -> text) {

			case '/start':
				Telegram::sendMessage([
					'chat_id' => $req -> message -> chat -> id,
					'text' => 'Добро пожаловать '.$req -> message -> from -> firstName.'!'
				]);
				break;

			case 'Клавиатура':
				$keyboard = Keyboard::make()
					->row([
						Keyboard::button(['text' => 'Кнопка 1']),
						Keyboard::button(['text' => 'Кнопка 2'])
					])
					->row([
						Keyboard::button(['text' => 'Кнопка 3']),
						Keyboard::button(['text' => 'Кнопка 4'])
					]);

				Telegram::sendMessage([
					'chat_id' => $req -> message -> chat -> id,
					'text' => 'Вот вам клавиатура Товарищ!\n',
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
