<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Permission;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Company;

class CompanyController extends Controller
{

	public function list() {
		$companies = Company::all();

		foreach ($companies as $company) {
			$company->departments = Department::where('com_id', $company->id)->get(); // Получаем департаменты для компании
			$company->workers = User::where('com_id', $company->id)->get(); // Получаем сотрудников для компании
			$company->permissions = Permission::where('com_id', $company->id)->get(); // Получаем права для компании

		}
		return view('company.list', ['companies' => $companies]);
	}

	public function create() {
		return view('company.create');
	}

	public function store() {
		$req = request() -> validate([
			'name' => 'required|string|unique:companies|min:3|max:20',
			'data' => 'nullable|string'
		]);

		Company::create($req);

		return redirect() -> route('company.list');

	}

	public function update(Company $company) {
		return view('company.update', ['company' => $company]);
	}

	public function modify(Company $company) {
		$req = request() -> validate([
			'name' => 'required|string|unique:companies|min:3|max:20',
		]);

		$company -> name = $req['name'];
		$company -> save();

		return redirect() -> route('company.list') -> with('success', 'Successfully updated');
	}

	public function delete(Company $company)
	{
		if (!@$company -> id) {
			return redirect()->back()->withErrors('Компания не найдена');
		}

		if(Department::where('com_id', $company -> id)->exists() || Permission::where('com_id', $company -> id)->exists()) {
			return redirect()->back()->withErrors('Эта компания ещё используется.');
		}

		$company->delete();
		return redirect()->back()->with('success', 'Компания успешно удалена');

	}
}
