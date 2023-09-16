<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class ThemeController extends Controller
{

	public array $themes = ['light', 'dark'];

	public function switch(string $name) {

		if(in_array($name, $this -> themes)) {
			session(['theme' => $name]);

			return redirect() -> back();

			// return response() -> json(
			// 	[
			// 		'status' =>'success',
			//     'theme' => $name
			// 	],
			// 	200,
			// 	[],
			// 	JSON_PRETTY_PRINT);
		}

	}
}
