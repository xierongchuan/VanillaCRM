<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Department;
use App\Models\Permission;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
	public function index(): \Illuminate\Contracts\View\View
	{
		if(Auth::check()){
			$companies = Company::all();
			return view('home', compact('companies'));
		} else {
			return view('home');
		}

	}
}
