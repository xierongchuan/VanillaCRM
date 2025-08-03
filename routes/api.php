<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
| API Routes
*/

Route::middleware('admin:sanctum')->get('/admin', function (Request $request) {
    return $request->user();
});
