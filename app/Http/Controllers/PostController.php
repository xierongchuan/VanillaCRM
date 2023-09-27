<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Department;
use App\Models\Permission;
use App\Models\Post;
use App\Models\Worker;
use Illuminate\Http\Request;

class PostController extends Controller
{
	public function index(Company $company, Department $department, Post $post) {

		$post -> workers = Worker::where([
			'com_id' => $company -> id,
			'dep_id' => $department -> id
		]) -> get();

		return view('company.department.post.index', compact('company', 'department', 'post'));
	}

	public function create(Company $company, Department $department) {
		$permissions = Permission::all();
		return view('company.department.post.create', compact('company', 'department', 'permissions'));
	}

	public function store(Company $company, Department $department) {
		$req = request() -> validate([
			'name' => 'required|min:3|max:20',
			'permission' => 'nullable'
		]);

		$post = new Post();
		$post -> com_id = $company -> id;
		$post -> dep_id = $department -> id;
		$post -> name = $req['name'];
		$post -> permission = json_encode((object)$req['permission']);
		$post -> save();

		return redirect() -> route('company.department.index', compact('company', 'department')) -> with('success', 'Successfully created');
	}
}
