<?php

declare(strict_types=1);

use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Api\V1\UserApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Создание сессии (логин)
Route::post(
    '/session/start',
    [SessionController::class, 'start']
)->middleware('throttle:1,1440');

// Проверка работоспособности API
Route::get('/up', function () {
    return response()->json(['success' => true], 200);
})->middleware('throttle:900,1');

// Защищённые маршруты только для админов
Route::middleware([
        'auth:sanctum',      // проверка токена Sanctum
        'admin.token',       // middleware, сверяющий role === 'admin'
        'throttle:300,1'     // лимит запросов
    ])
    ->prefix('v1')
    ->group(function () {
        Route::get('/user', [UserApiController::class, 'index']);
        Route::get('/user/{id}', [UserApiController::class, 'show']);
    });
