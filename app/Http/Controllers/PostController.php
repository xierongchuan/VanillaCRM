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
		$permissions = Permission::where('com_id', $company -> id) -> get();
		return view('company.department.post.create', compact('company', 'department', 'permissions'));
	}

	public function store(Company $company, Department $department) {
		$req = request() -> validate([
			'name' => 'required|min:3|max:30',
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

	public function update(Company $company, Department $department, Post $post) {
		$permissions = Permission::where('com_id', $company -> id) -> get();
		return view('company.department.post.update', compact('company', 'department', 'post', 'permissions'));
	}

	public function modify(Company $company, Department $department, Post $post) {
		$req = request() -> validate([
			'name' => 'required|min:3|max:30',
			'permission' => 'nullable'
		]);

		$post -> name = $req['name'];
		$post -> permission = json_encode((object)$req['permission']);
		$post -> save();

		return redirect() -> route('company.department.index', compact('company', 'department')) -> with('success', 'Successfully updated');
	}

	public function delete(Company $company, Department $department, Post $post)
	{

		if (!@$post -> id) {
			return redirect()->back()->withErrors('Должность не найдена');
		}

		if(Worker::where('post_id', $post -> id)->exists()) {
			return redirect()->back()->withErrors('Эта должность ещё используется.');
		}

		$post->delete();
		return redirect()->back()->with('success', 'Должность успешно удалена');

	}
}
