<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Department;
use App\Models\Permission;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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
            return redirect()->route('home.index')->with('success', 'Вы успешно Аутентифицированы');
        }

        // Аутентификация не удалась
        return back()->withErrors(['login' => 'Неверные учетные данные'])->withInput($request->only('login'));
    }

    public function create(Company $company)
    {
        $departments = Department::where('com_id', $company->id)->get();

        return view('company.user.create', compact('company', 'departments'));
    }

    public function store(Company $company)
    {

        $req = request()->validate([
            'login' => 'required|unique:users',
            //			'role' => Rule::in(['admin', 'user']),
            'full_name' => 'required|min:3|max:30',
            'department' => 'required|numeric|min:1',
            'phone_number' => 'required|string|min:1|max:22',
            'password' => 'required|min:6|max:256',
        ]);

        if (! Department::where('id', $req['department'])->exists()) {
            return redirect()->back()->withErrors('Департамент не найден');
        }

        $user = new User;
        $user->login = $req['login'];
        $user->role = 'user';
        $user->password = Hash::make($req['password']);
        $user->com_id = $company->id;
        $user->dep_id = $req['department'];
        $user->full_name = $req['full_name'];
        $user->phone_number = str_replace(' ', '', $req['phone_number']);
        $user->save();

        return redirect()->route('company.list');
    }

    public function update(Company $company, User $user)
    {
        $departments = Department::where('com_id', $user->com_id)->get();
        $posts = Post::where('dep_id', $user->dep_id)->get();

        return view('company.user.update', compact('company', 'user', 'departments', 'posts'));
    }

    public function modify(Company $company, User $user)
    {

        if (isset(\request()->password)) {
            $req = request()->validate([
                'department' => 'required|numeric|min:1',
                'post' => 'nullable|numeric|min:1',
                'full_name' => 'required|min:3|max:30',
                'phone_number' => 'required|string|min:1|max:22',
                'password' => 'required|min:6|max:256',
            ]);
        } else {
            $req = request()->validate([
                'department' => 'required|numeric|min:1',
                'post' => 'nullable|numeric|min:1',
                'full_name' => 'required|min:3|max:30',
                'phone_number' => 'required|string|min:1|max:22',
            ]);
        }

        if (! Department::where('id', $req['department'])->exists()) {
            return redirect()->back()->withErrors('Департамент не найден');
        }

        if (@$req['post'] && ! Post::where('id', @$req['post'])->exists()) {
            return redirect()->back()->withErrors('Должность не найден');
        }

        if (! empty($req['password'])) {
            $user->password = Hash::make($req['password']);
        }
        $user->com_id = $company->id;
        $user->dep_id = $req['department'];
        $user->post_id = $req['post'] ?? null;
        $user->full_name = $req['full_name'];
        $user->phone_number = str_replace(' ', '', $req['phone_number']);
        $user->save();

        return redirect()->route('company.list');
    }

    public function activate(Company $company, User $user)
    {
        if (! @$company->id) {
            return redirect()->back()->withErrors('Компания не найдена');
        }

        if (! @$user->id) {
            return redirect()->back()->withErrors('Сотрудник не найден');
        }

        $user->status = "active";

        $user->save();

        return redirect()->back()->with('success', 'Сотрудник успешно активирован');

    }

    public function deactivate(Company $company, User $user)
    {
        if (! @$company->id) {
            return redirect()->back()->withErrors('Компания не найдена');
        }

        if (! @$user->id) {
            return redirect()->back()->withErrors('Сотрудник не найден');
        }

        $user->status = "deactive";

        $user->save();

        return redirect()->back()->with('success', 'Сотрудник успешно деактивирован');

    }

    public function delete(Company $company, User $user)
    {
        if (! @$company->id) {
            return redirect()->back()->withErrors('Компания не найдена');
        }

        if (! @$user->id) {
            return redirect()->back()->withErrors('Сотрудник не найден');
        }

        $user->delete();

        return redirect()->back()->with('success', 'Сотрудник успешно удален');

    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('auth.sign_in');
    }

    // / Administrator Sector

    public function createAdmin()
    {
        $admins = User::where('role', 'admin')->whereNot('login', 'admin')->get();

        return view('admin.index', compact('admins'));
    }

    public function storeAdmin(Company $company)
    {

        $req = request()->validate([
            'login' => 'required|unique:users',
            'full_name' => 'required|min:3|max:30',
            'password' => 'required|min:6|max:256',
        ]);

        $user = new User;
        $user->login = $req['login'];
        $user->role = 'admin';
        $user->password = Hash::make($req['password']);
        $user->full_name = $req['full_name'];
        $user->save();

        return redirect()->route('company.list');
    }

    public function deleteAdmin(User $admin)
    {
        if (! @$admin->id) {
            return redirect()->back()->withErrors('Администратор не найден');
        }

        $admin->delete();

        return redirect()->back()->with('success', 'Администратор успешно удален');

    }

    // / Permissions Sector

    public function permission()
    {

        $company = Company::find(Auth::user()->com_id);

        $department = Department::find(Auth::user()->dep_id);

        $post = Post::find(@Auth::user()->post_id);
        $permission_ids = (array) json_decode(@$post->permission);
        $permissions = Permission::whereIn('id', @$permission_ids)->get();
        $permission_vals = @$permissions->pluck('value')->toArray();

        $data = (object) [
            'company' => $company,
            'department' => $department,
            'post' => $post,
            'perm' => $permission_vals,
        ];

        return view('user.permission', compact('data'));
    }
}
