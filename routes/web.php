<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ThemeController;

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
