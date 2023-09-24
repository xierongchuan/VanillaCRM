<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

	public function sign_in()
	{
		return view('auth.sign_in');
	}

	public function login(Request $request)
	{
		$request->validate([
			'login' => 'required|string',
			'password' => 'required|string',
		]);

		$credentials = $request->only('login', 'password');

		if (Auth::attempt($credentials)) {
			// Аутентификация успешна
			return redirect()->route('home.index');
		}

		// Аутентификация не удалась
		return back()->withErrors(['login' => 'Неверные учетные данные'])->withInput($request->only('login'));
	}

	public function create()
	{
		return view('admin.create');
	}

	public function store(Request $request)
	{
//		+----+-------+--------------------------------------------------------------+----------------+---------------------+---------------------+
//		| id | login | password                                                     | remember_token | created_at          | updated_at          |
//		+----+-------+--------------------------------------------------------------+----------------+---------------------+---------------------+
//		|  1 | admin | $2y$10$aUu5mi2aquDAIo97E4fmJOyUqzaiP7B0m3bE.w0Nu8Wtn8GK7SneK | NULL           | 2023-09-24 12:26:05 | 2023-09-24 12:26:05 |
//		+----+-------+--------------------------------------------------------------+----------------+---------------------+---------------------+


	$request->validate([
			'login' => 'required|unique:users',
			'password' => 'required|min:6',
		]);

		$user = new User();
		$user->login = $request->input('login');
		$user->password = Hash::make($request->input('password'));
		$user->save();

		return redirect()->route('home.index')->with('success', 'Пользователь создан!');
	}

	public function logout()
	{
		Auth::logout();

		return redirect() -> route('auth.sign_in');
	}
}
