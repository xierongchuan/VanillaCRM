<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserApiController extends Controller
{
    public function index()
    {
        $items = User::all();
        return response()->json($items);
    }

    public function show($id)
    {
        $item = User::findOrFail($id);
        return response()->json($item);
    }
}
