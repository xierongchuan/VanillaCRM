<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Department;
use App\Models\Post;
use App\Models\Worker;
use Illuminate\Http\Request;

class WorkerController extends Controller
{
	public function create(Company $company) {
		$departments = Department::all();
		return view('company.worker.create', compact('company', 'departments'));
	}

	public function store(Company $company) {
		$req = request() -> validate([
			'department' => 'required|numeric|min:1',
			'full_name' => 'required|min:3|max:30',
			'phone_number' => 'required|string|min:1|max:22'
		]);

		if(!Department::where('id', $req['department'])->exists()) {
			return redirect()->back()->withErrors('Департамент не найден');
		}

		$worker = new Worker();
		$worker -> com_id = $company -> id;
		$worker -> dep_id = $req['department'];
		$worker -> full_name = $req['full_name'];
		$worker -> phone_number = str_replace(' ', '', $req['phone_number']);
		$worker -> save();

		return redirect() -> route('company.list');
	}

	public function update(Company $company, Worker $worker) {
		$departments = Department::where('com_id', $worker -> com_id) -> get();
		$posts = Post::where('dep_id', $worker -> dep_id) -> get();
		return view('company.worker.update', compact('company', 'worker','departments', 'posts'));
	}

	public function modify(Company $company, Worker $worker) {
		$req = request() -> validate([
			'department' => 'required|numeric|min:1',
			'post' => 'nullable|numeric|min:1',
			'full_name' => 'required|min:3|max:30',
			'phone_number' => 'required|string|min:1|max:22'
		]);

		if(!Department::where('id', $req['department'])->exists()) {
			return redirect()->back()->withErrors('Департамент не найден');
		}

		if(@$req['post'] && !Post::where('id', @$req['post'])->exists()) {
			return redirect()->back()->withErrors('Должность не найдена');
		}

		$worker -> com_id = $company -> id;
		$worker -> dep_id = $req['department'];
		$worker -> post_id = $req['post'] ?? null;
		$worker -> full_name = $req['full_name'];
		$worker -> phone_number = str_replace(' ', '', $req['phone_number']);
		$worker -> save();

		return redirect() -> route('company.list');
	}

	public function delete(Company $company, Worker $worker)
	{
		if (!@$company -> id) {
			return redirect()->back()->withErrors('Компания не найдена');
		}

		if (!@$worker -> id) {
			return redirect()->back()->withErrors('Сотрудник не найдена');
		}

		$worker->delete();
		return redirect()->back()->with('success', 'Сотрудник успешно удален');

	}
}
