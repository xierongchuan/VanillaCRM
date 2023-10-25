<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Department;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
	public function create(Company $company) {
		return view('company.permission.create', compact('company'));
	}

	public function store(Request $req, Company $company) {
		$req -> validate([
			'name' => 'required|min:3|max:20',
			'value' => 'required|min:3|max:20|regex:/^[a-z_]+$/',
			'data' => 'nullable|string'
		]);


		if(@Permission::where('com_id', $company -> id)->where('value', $req -> value)->first()) {
			return redirect() -> route('company.list') -> withErrors("Такое право уже существует у этой компаний");
		}


		if (Company::where('id', $company -> id)->first()) {
			$per = new Permission();
			$per -> com_id = $company -> id;
			$per -> name = $req -> name;
			$per -> value = $req -> value;
			$per -> data = $req -> data;
			$per -> save();

			return redirect() -> route('company.list');
		}

		return redirect() -> route('company.list') -> withErrors("Company not found");
	}


	public function update(Company $company, Permission $permission) {
		return view('company.permission.update', compact('company', 'permission'));
	}

	public function modify(Company $company, Permission $permission) {
		$req = request() -> validate([
			'name' => 'required|min:3|max:20',
			'data' => 'nullable|string'
		]);

		if (Company::where('id', $company -> id)->exists()) {

			$permission -> name = $req['name'];
			$permission -> data = $req['data'];
			$permission -> save();

			return redirect() -> route('company.list');
		}

		return redirect() -> route('company.list') -> withErrors("Company not found");
	}

	public function delete(Company $company, Permission $permission)
	{

		if (!@$permission -> id) {
			return redirect()->back()->withErrors('Право не найдено');
		}

		if(DB::table('posts')
			->whereRaw('JSON_CONTAINS(permission, \'['.$permission -> id.']\')')
			->exists()) {
			return redirect()->back()->withErrors('Это право ещё используется.');
		}

		$permission->delete();
		return redirect()->back()->with('success', 'Право успешно удалено');

	}
}
