<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;

class CompanyController extends Controller
{

	public function create() {
		return view('company.create');
	}

	public function store() {
		$req = request() -> validate([
			'name' => 'required|string|min:3|max:20',
			'data' => 'nullable|string'
		]);

		Company::create($req);

		return redirect() -> route('home.index');

	}
}
