<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Department;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Company $company, Department $department)
    {
        $department->posts = Post::where([
            'com_id' => $company->id,
            'dep_id' => $department->id,
        ])->get();

        $department->users = User::where([
            'com_id' => $company->id,
            'dep_id' => $department->id,
        ])->get();

        return view('company.department.index', compact('company', 'department'));
    }

    public function create(Company $company)
    {
        return view('company.department.create', compact('company'));
    }

    public function store(Request $req, Company $company)
    {
        $req->validate([
            'name' => 'required|min:3|max:20',
        ]);

        $dep = new Department;
        $dep->com_id = $company->id;
        $dep->name = $req->name;
        $dep->save();

        return redirect()->route('company.list');
    }

    public function update(Company $company, Department $department)
    {
        return view('company.department.update', compact('company', 'department'));
    }

    public function modify(Company $company, Department $department)
    {
        $req = request()->validate([
            'name' => 'required|min:3|max:20',
        ]);

        if (Company::where('id', $company->id)->exists()) {

            $department->name = $req['name'];
            $department->save();

            return redirect()->route('company.list');
        }

        return redirect()->route('company.list')->withErrors('Company not found');
    }

    public function posts(Company $company, Department $department)
    {
        $posts = Post::where('dep_id', $department->id)->get();

        return response()->json($posts);
    }

    public function delete(Company $company, Department $department)
    {

        if (! @$department->id) {
            return redirect()->back()->withErrors('Департамент не найден');
        }

        if (Post::where('dep_id', $department->id)->exists()) {
            return redirect()->back()->withErrors('Этот департамент ещё используется.');
        }

        $department->delete();

        return redirect()->back()->with('success', 'Департамент успешно удален');

    }
}
