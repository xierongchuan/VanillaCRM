<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

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

			default:
				Telegram::sendMessage([
					'chat_id' => $req -> message -> chat -> id,
					'text' => 'Неизвестная команда!'
				]);
				break;
		}

	}
}
