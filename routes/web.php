<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\TelegramBotController;
use Telegram\Bot\Laravel\Facades\Telegram;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//// Home Controller
Route::get('/', [HomeController::class, 'index']) -> name('home.index');

//// Theme Controller
Route::get('/theme/{name}', [ThemeController::class, 'switch']) -> name('theme.switch');

//// Telegram Bot Controller
Route::post('/webhook', [TelegramBotController::class, 'index']);
