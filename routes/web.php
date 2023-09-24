<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\TelegramBotController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

/// Home Controller
Route::get('/', [HomeController::class, 'index']) -> name('home.index');

/// Theme Controller
Route::get('/theme/{name}', [ThemeController::class, 'switch']) -> name('theme.switch');

/// Telegram Bot Controller
Route::post('/webhook', [TelegramBotController::class, 'index']);


/// UserController

// Маршрут для отображения формы входа
Route::get('/sign_in', [UserController::class, 'sign_in']) -> name('auth.sign_in');

// Маршрут для обработки входа
Route::post('/login', [UserController::class, 'login']) -> name('auth.login');

Route::group(['middleware' => 'admin'], function () {
	// Здесь находятся маршруты, к которым доступ разрешен только аутентифицированным пользователям

	/// Company Controller
	Route::get('/company/create', [CompanyController::class, 'create']) -> name('company.create');

	Route::post('/company', [CompanyController::class, 'store']) -> name('company.store');

	/// UserController
	Route::get('/admin/create', [UserController::class, 'create']) -> name('admin.create');

	Route::post('/admin/store', [UserController::class, 'store']) -> name('admin.store');


	Route::get('/logout', [UserController::class, 'logout']) -> name('auth.logout');
});
