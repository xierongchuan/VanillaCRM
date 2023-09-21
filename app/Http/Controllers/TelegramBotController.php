<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramBotController extends Controller
{
	public function handle(): void
	{
		$updates = Telegram::getWebhookUpdates();

		Telegram::sendMessage([
			'chat_id' => 5577711248,
			'text' => (string)json_encode($updates)
		]);

	}
}
