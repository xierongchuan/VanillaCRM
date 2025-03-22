<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Field;

class FieldController extends Controller
{
    public function create(Company $company)
    {
        return view('company.field.create', compact('company'));
    }

    public function update(Company $company, Field $field)
    {
        return view('company.field.update', compact('company', 'field'));
    }

    public function modify(Company $company, Field $field)
    {
        $req = request()->validate([
            'title' => 'required|min:3|max:30|string',
            'link' => 'nullable|string',
        ]);

        $field->title = $req['title'];
        $field->link = $req['link'];
        $field->save();

        return redirect()->route('company.list', compact('company'))->with('success', 'Successfully updated');
    }

    public function store(Company $company)
    {
        $req = request()->validate([
            'title' => 'required|min:3|max:30|string',
            'link' => 'nullable|string',
        ]);

        $field = new Field;
        $field->com_id = $company->id;
        $field->title = $req['title'];
        $field->link = $req['link'];
        $field->save();

        return redirect()->route('company.list', compact('company'))->with('success', 'Successfully created');

    }

    public function delete(Company $company, Field $field)
    {
        if (! @$field->id) {
            return redirect()->back()->withErrors('Динамическая ссылка не найдена');
        }

        $field->delete();

        return redirect()->back()->with('success', 'Ссылка успешно удалена');

    }
}
