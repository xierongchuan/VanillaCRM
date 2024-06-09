<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\ReportXlsxService;
use Illuminate\Http\Request;
use App\Models\Report;
use Carbon\Carbon;

class StatController extends Controller
{
    public function index()
    {

        $companies = Company::all();

        $sales = (new ReportXlsxService())->getSalesDataNoSum($companies);

        return view("company.stat", compact('sales'));
    }
}
