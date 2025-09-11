<?php

declare(strict_types=1);

use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ModController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ReportXlsxController;
use App\Http\Controllers\StatController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CashierReportController; // Added this import
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/theme/{name}', [ThemeController::class, 'switch'])->name('theme.switch');

// Authentication routes
Route::get('/sign_in', [UserController::class, 'sign_in'])->name('auth.sign_in');
Route::post('/login', [UserController::class, 'login'])->name('auth.login');
Route::get('/logout', [UserController::class, 'logout'])->name('auth.logout');

// Admin routes
Route::group(['middleware' => 'admin'], function () {
    // Company management
    Route::get('/company/list', [CompanyController::class, 'list'])->name('company.list');
    Route::get('/company/create', [CompanyController::class, 'create'])->name('company.create');
    Route::post('/company/store', [CompanyController::class, 'store'])->name('company.store');
    Route::get('/company/{company}/update', [CompanyController::class, 'update'])->name('company.update');
    Route::post('/company/{company}/modify', [CompanyController::class, 'modify'])->name('company.modify');
    Route::get('/company/{company}/delete', [CompanyController::class, 'delete'])->name('company.delete');

    // Department management
    Route::get(
        '/company/{company}/department/{department}/index',
        [DepartmentController::class, 'index']
    )->name('company.department.index');
    Route::get(
        '/company/{company}/department/create',
        [DepartmentController::class, 'create']
    )->name('company.department.create');
    Route::post(
        '/company/{company}/department/store',
        [DepartmentController::class, 'store']
    )->name('company.department.store');
    Route::get(
        '/company/{company}/department/{department}/update',
        [DepartmentController::class, 'update']
    )->name('company.department.update');
    Route::post(
        '/company/{company}/department/{department}/modify',
        [DepartmentController::class, 'modify']
    )->name('company.department.modify');
    Route::post(
        '/company/{company}/department/{department}/posts',
        [DepartmentController::class, 'posts']
    )->name('company.department.posts');
    Route::get(
        '/company/{company}/department/{department}/delete',
        [DepartmentController::class, 'delete']
    )->name('company.department.delete');

    // Permission management
    Route::get(
        '/company/{company}/permission/create',
        [PermissionController::class, 'create']
    )->name('company.permission.create');
    Route::post(
        '/company/{company}/permission/store',
        [PermissionController::class, 'store']
    )->name('company.permission.store');
    Route::get(
        '/company/{company}/permission/{permission}/update',
        [PermissionController::class, 'update']
    )->name('company.permission.update');
    Route::post(
        '/company/{company}/permission/{permission}/modify',
        [PermissionController::class, 'modify']
    )->name('company.permission.modify');
    Route::get(
        '/company/{company}/permission/{permission}/delete',
        [PermissionController::class, 'delete']
    )->name('company.permission.delete');

    // Post management
    Route::get(
        '/company/{company}/department/{department}/post/{post}/index',
        [PostController::class, 'index']
    )->name('company.department.post.index');
    Route::get(
        '/company/{company}/department/{department}/post/create',
        [PostController::class, 'create']
    )->name('company.department.post.create');
    Route::post(
        '/company/{company}/department/{department}/post/store',
        [PostController::class, 'store']
    )->name('company.department.post.store');
    Route::get(
        '/company/{company}/department/{department}/post/{post}/update',
        [PostController::class, 'update']
    )->name('company.department.post.update');
    Route::post(
        '/company/{company}/department/{department}/post/{post}/modify',
        [PostController::class, 'modify']
    )->name('company.department.post.modify');
    Route::get(
        '/company/{company}/department/{department}/post/{post}/delete',
        [PostController::class, 'delete']
    )->name('company.department.post.delete');

    // User management
    Route::get('/admin/', [UserController::class, 'createAdmin'])->name('admin.index');
    Route::post('/admin/store', [UserController::class, 'storeAdmin'])->name('admin.store');
    Route::get('/admin/{admin}/delete', [UserController::class, 'deleteAdmin'])->name('admin.delete');
    Route::get(
        '/company/{company}/user/create',
        [UserController::class, 'create']
    )->name('company.user.create');
    Route::post(
        '/company/{company}/user/store',
        [UserController::class, 'store']
    )->name('company.user.store');
    Route::get(
        '/company/{company}/user/{user}/update',
        [UserController::class, 'update']
    )->name('company.user.update');
    Route::post(
        '/company/{company}/user/{user}/modify',
        [UserController::class, 'modify']
    )->name('company.user.modify');
    Route::get(
        '/company/{company}/user/{user}/activate',
        [UserController::class, 'activate']
    )->name('company.user.activate');
    Route::get(
        '/company/{company}/user/{user}/deactivate',
        [UserController::class, 'deactivate']
    )->name('company.user.deactivate');
    Route::get(
        '/company/{company}/user/{user}/delete',
        [UserController::class, 'delete']
    )->name('company.user.delete');

    // Custom field management
    Route::get(
        '/company/{company}/field/create',
        [FieldController::class, 'create']
    )->name('company.field.create');
    Route::post(
        '/company/{company}/field/store',
        [FieldController::class, 'store']
    )->name('company.field.store');
    Route::get(
        '/company/{company}/field/{field}/update',
        [FieldController::class, 'update']
    )->name('company.field.update');
    Route::post(
        '/company/{company}/field/{field}/modify',
        [FieldController::class, 'modify']
    )->name('company.field.modify');
    Route::get(
        '/company/{company}/field/{field}/delete',
        [FieldController::class, 'delete']
    )->name('company.field.delete');

    // Archive management
    Route::get('/company/{company}/archive', [ArchiveController::class, 'archive'])->name('company.archive');
    Route::get(
        '/company/{company}/service/archive',
        [ArchiveController::class, 'serviceArchive']
    )->name('company.service.archive.list');
    Route::get(
        '/company/{company}/service/archive/{date}',
        [ArchiveController::class, 'getServiceReportXlsx']
    )->name('company.service.archive');
    Route::get(
        '/company/{company}/caffe/archive',
        [ArchiveController::class, 'caffeArchive']
    )->name('company.caffe.archive.list');
    Route::get(
        '/company/{company}/caffe/archive/{date}',
        [ArchiveController::class, 'getCaffeReportXlsx']
    )->name('company.caffe.archive');
    Route::get(
        '/company/{company}/cashier/archive',
        [ArchiveController::class, 'cashierArchive']
    )->name('company.cashier.archive.list');
    Route::get(
        '/company/{company}/cashier/archive/{date}',
        [ArchiveController::class, 'getCashierReportXlsx']
    )->name('company.cashier.archive');
    Route::get(
        '/company/{company}/archive/remove_last_report',
        [ArchiveController::class, 'removeLastReport']
    )->name('company.remove_last_report');
    Route::get(
        '/company/{company}/service/remove_last_report',
        [ArchiveController::class, 'deleteLastServiceReport']
    )->name('company.service.remove_last_report');
    Route::get(
        '/company/{company}/caffe/remove_last_report',
        [ArchiveController::class, 'deleteLastCaffeReport']
    )->name('company.caffe.remove_last_report');
    Route::get(
        '/company/{company}/cashier/remove_last_report',
        [ArchiveController::class, 'deleteLastCashierReport']
    )->name('company.cashier.remove_last_report');

    // Statistics
    Route::get('/stat', [StatController::class, 'index'])->name('stat.index');
});

// User routes
Route::group(['middleware' => 'user'], function () {
    Route::get('/permission', [UserController::class, 'permission'])->name('user.permission');

    // MOD routes
    Route::post(
        '/company/{company}/user/create_worker',
        [ModController::class, 'createWorker']
    )->name('mod.create_worker');
    Route::post(
        '/company/{company}/report_service',
        [ModController::class, 'reportService']
    )->name('mod.report_service');
    Route::post(
        '/company/{company}/report_caffe',
        [ModController::class, 'reportCaffe']
    )->name('mod.report_caffe');
    Route::post(
        '/company/{company}/report_xlsx',
        [ReportXlsxController::class, 'report_xlsx']
    )->name('mod.report_xlsx');
    Route::post(
        '/company/{company}/report_xlsx_sales',
        [ModController::class, 'reportXlsxSales']
    )->name('mod.report_xlsx_sales');

    // Cashier report route
    Route::post(
        '/company/{company}/report_cashier',
        [CashierReportController::class, 'report']
    )->name('mod.report_cashier');
});
