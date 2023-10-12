<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\TelegramBotController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PostController;

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
// Route::post('/webhook', [TelegramBotController::class, 'index']);

/// UserController

// Маршрут для отображения формы входа
Route::get('/sign_in', [UserController::class, 'sign_in']) -> name('auth.sign_in');

// Маршрут для обработки входа
Route::post('/login', [UserController::class, 'login']) -> name('auth.login');


Route::group(['middleware' => 'admin'], function () {
	// Здесь находятся маршруты, к которым доступ разрешен только аутентифицированным пользователям

	/// Company Controller

	Route::get('/company/list', [CompanyController::class, 'list']) -> name('company.list');

	Route::get('/company/create', [CompanyController::class, 'create']) -> name('company.create');

	Route::post('/company/store', [CompanyController::class, 'store']) -> name('company.store');

	Route::get('/company/{company}/update', [CompanyController::class, 'update']) -> name('company.update');

	Route::post('/company/{company}/modify', [CompanyController::class, 'modify']) -> name('company.modify');

	Route::get('/company/{company}/delete', [CompanyController::class, 'delete']) -> name('company.delete');

	/// Department Controller
	Route::get('/company/{company}/department/{department}/index', [DepartmentController::class, 'index']) -> name('company.department.index');

	Route::get('/company/{company}/department/create', [DepartmentController::class, 'create']) -> name('company.department.create');

	Route::post('/company/{company}/department/store', [DepartmentController::class, 'store']) -> name('company.department.store');

	Route::get('/company/{company}/department/{department}/update', [DepartmentController::class, 'update']) -> name('company.department.update');

	Route::post('/company/{company}/department/{department}/modify', [DepartmentController::class, 'modify']) -> name('company.department.modify');

	Route::post('/company/{company}/department/{department}/posts', [DepartmentController::class, 'posts']) -> name('company.department.posts');

	Route::get('/company/{company}/department/{department}/delete', [DepartmentController::class, 'delete']) -> name('company.department.delete');


	/// Permission Controller
	Route::get('/company/{company}/permission/create', [PermissionController::class, 'create']) -> name('company.permission.create');

	Route::post('/company/{company}/permission/store', [PermissionController::class, 'store']) -> name('company.permission.store');

	Route::get('/company/{company}/permission/{permission}/update', [PermissionController::class, 'update']) -> name('company.permission.update');

	Route::post('/company/{company}/permission/{permission}/modify', [PermissionController::class, 'modify']) -> name('company.permission.modify');

	Route::get('/company/{company}/permission/{permission}/delete', [PermissionController::class, 'delete']) -> name('company.permission.delete');

	/// PostController
	Route::get('/company/{company}/department/{department}/post/{post}/index', [PostController::class, 'index']) -> name('company.department.post.index');

	Route::get('/company/{company}/department/{department}/post/create', [PostController::class, 'create']) -> name('company.department.post.create');

	Route::post('/company/{company}/department/{department}/post/store', [PostController::class, 'store']) -> name('company.department.post.store');

	Route::get('/company/{company}/department/{department}/post/{post}/update', [PostController::class, 'update']) -> name('company.department.post.update');

	Route::post('/company/{company}/department/{department}/post/{post}/modify', [PostController::class, 'modify']) -> name('company.department.post.modify');

	Route::get('/company/{company}/department/{department}/post/{post}/delete', [PostController::class, 'delete']) -> name('company.department.post.delete');

	/// Worker/User Controller
//	Route::get('/company/{company}/user/{user}/index', [UserController::class, 'index']) -> name('company.user.index');

	Route::get('/company/{company}/user/create', [UserController::class, 'create']) -> name('company.user.create');

	Route::post('/company/{company}/user/store', [UserController::class, 'store']) -> name('company.user.store');

	Route::get('/company/{company}/user/{user}/update', [UserController::class, 'update']) -> name('company.user.update');

	Route::post('/company/{company}/user/{user}/modify', [UserController::class, 'modify']) -> name('company.user.modify');

	Route::get('/company/{company}/user/{user}/delete', [UserController::class, 'delete']) -> name('company.user.delete');
});

Route::group(['middleware' => 'user'], function () {
	// Здесь находятся маршруты, доступные только работникам

	Route::get('/company/{company}/user/create', [UserController::class, 'create']) -> name('company.user.create');
});

Route::get('/logout', [UserController::class, 'logout']) -> name('auth.logout');
