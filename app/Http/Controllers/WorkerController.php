<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Worker;
use Illuminate\Http\Request;

class WorkerController extends Controller
{
	public function create(Company $company) {
		return view('company.worker.create', compact('company'));
	}

	public function store(Request $req, Company $company) {

	}
}
