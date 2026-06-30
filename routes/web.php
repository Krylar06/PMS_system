<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\CollegeController;
use App\Http\Controllers\Admin\OfficeController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\StaffDeviceController;
use App\Http\Controllers\Admin\DeviceController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\DeviceChecklistController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Forgot Password Routes
|--------------------------------------------------------------------------
*/

Route::get('/forgot-password', [ForgotPasswordController::class, 'show'])
    ->name('password.request');

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])
    ->name('password.email');

/*
|--------------------------------------------------------------------------
| Reset Password Routes
|--------------------------------------------------------------------------
*/

Route::get('/reset-password/{token}', [ResetPasswordController::class, 'show'])
    ->name('password.reset');

Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
    ->name('password.update');

/*
|--------------------------------------------------------------------------
| Protected Routes — shared by both roles (admin + custodian)
|--------------------------------------------------------------------------
| Devices, issuing/returning devices, reports, dashboard, scanner, and
| browsing the college/office/staff directory are everyday tasks for both
| roles. Org-structure changes (creating/editing/deleting colleges,
| offices, staff) and user management are admin-only — see the nested
| 'role:admin' group further below.
*/

Route::middleware(['auth', 'role:admin,custodian'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Admin Pages
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::view('/org-browser', 'admin.org-browser')->name('admin.org-browser');
        Route::view('/scanner', 'admin.scanner')->name('admin.scanner');

        /*
        |--------------------------------------------------------------------------
        | Reports
        |--------------------------------------------------------------------------
        */
        Route::prefix('reports')->name('admin.reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/assets', [ReportController::class, 'assets'])->name('assets');
            Route::get('/accounts', [ReportController::class, 'accounts'])
                ->middleware('role:admin')
                ->name('accounts');
            Route::get('/checked-equipment', [ReportController::class, 'checkedEquipment'])->name('checkedEquipment');
            Route::get('/checklist', [ReportController::class, 'checklist'])->name('checklist');
        });

        /*
        |--------------------------------------------------------------------------
        | Devices
        |--------------------------------------------------------------------------
        */
        Route::put('/devices/{device}/quick', [DeviceController::class, 'quickUpdate'])
            ->name('admin.devices.quickUpdate');

        Route::patch('/devices/{device}/mark-checked', [DeviceController::class, 'markChecked'])
            ->name('admin.devices.markChecked');

        Route::get('/devices/{device}/maintenance-checklist', [DeviceChecklistController::class, 'create'])
            ->name('admin.devices.checklist.create');

        Route::post('/devices/{device}/maintenance-checklist/pdf', [DeviceChecklistController::class, 'generate'])
            ->name('admin.devices.checklist.generate');

        Route::get('/devices/{device}/maintenance-history', [DeviceController::class, 'maintenanceHistory'])
            ->name('admin.devices.history');

        Route::get('/reports/preventive-maintenance/export', [DeviceController::class, 'exportPreventiveMaintenanceReport'])
            ->name('admin.reports.preventiveMaintenance.export');

        Route::get('/devices/generate-qr', [DeviceController::class, 'generateQr'])
            ->name('admin.devices.qr.index');

        Route::resource('/devices', DeviceController::class)->names('admin.devices')->except(['destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Colleges — browsing is open to both roles
    |--------------------------------------------------------------------------
    */
    Route::get('colleges', [CollegeController::class, 'index'])->name('admin.colleges.index');

    /*
    |--------------------------------------------------------------------------
    | Offices — browsing is open to both roles
    |--------------------------------------------------------------------------
    */
    Route::get('colleges/{college}/offices', [OfficeController::class, 'index'])
        ->name('admin.offices.index');

    /*
    |--------------------------------------------------------------------------
    | Staff — browsing is open to both roles
    |--------------------------------------------------------------------------
    */
    Route::get('offices/{office}/staff', [StaffController::class, 'index'])
        ->name('admin.staff.index');

    /*
    |--------------------------------------------------------------------------
    | Office Reports — both roles may need to pull these for inventory work
    |--------------------------------------------------------------------------
    */
    Route::get('offices/{office}/reports/preventive-maintenance/export', [DeviceController::class, 'exportOfficePreventiveMaintenanceReport'])
        ->name('admin.offices.preventiveMaintenance.export');

    /*
    |--------------------------------------------------------------------------
    | Staff Devices — issuing/returning equipment is core custodian work
    |--------------------------------------------------------------------------
    */
    Route::get('staff/{staff}/devices', [StaffDeviceController::class, 'index'])
        ->name('admin.staff.devices.index');

    Route::post('staff/{staff}/devices/issue', [StaffDeviceController::class, 'issue'])
        ->name('admin.staff.devices.issue');

    Route::post('staff/{staff}/devices/{assignment}/return', [StaffDeviceController::class, 'return'])
        ->name('admin.staff.devices.return');

    /*
    |--------------------------------------------------------------------------
    | Admin-only — org structure changes & user management
    |--------------------------------------------------------------------------
    | Creating/editing/deleting colleges, offices, and staff records changes
    | the university's organizational structure, which is an admin decision.
    | Custodians can browse this directory (routes above) but not modify it.
    */
    Route::middleware('role:admin')->group(function () {

        // Colleges — write actions
        Route::post('colleges', [CollegeController::class, 'store'])->name('admin.colleges.store');
        Route::get('colleges/{college}/edit', [CollegeController::class, 'edit'])->name('admin.colleges.edit');
        Route::put('colleges/{college}', [CollegeController::class, 'update'])->name('admin.colleges.update');
        Route::delete('colleges/{college}', [CollegeController::class, 'destroy'])->name('admin.colleges.destroy');

        // Offices — write actions
        Route::post('colleges/{college}/offices', [OfficeController::class, 'store'])
            ->name('admin.offices.store');
        Route::get('colleges/{college}/offices/{office}/edit', [OfficeController::class, 'edit'])
            ->name('admin.offices.edit');
        Route::put('colleges/{college}/offices/{office}', [OfficeController::class, 'update'])
            ->name('admin.offices.update');
        Route::delete('colleges/{college}/offices/{office}', [OfficeController::class, 'destroy'])
            ->name('admin.offices.destroy');

        // Staff — write actions
        Route::post('offices/{office}/staff', [StaffController::class, 'store'])
            ->name('admin.staff.store');
        Route::get('offices/{office}/staff/{staff}/edit', [StaffController::class, 'edit'])
            ->name('admin.staff.edit');
        Route::put('offices/{office}/staff/{staff}', [StaffController::class, 'update'])
            ->name('admin.staff.update');
        Route::delete('offices/{office}/staff/{staff}', [StaffController::class, 'destroy'])
            ->name('admin.staff.destroy');

        // Device deletion — deleting records is admin-only system-wide
        Route::delete('admin/devices/{device}', [DeviceController::class, 'destroy'])
            ->name('admin.devices.destroy');

        // User accounts & role management
        Route::get('admin/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::post('admin/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::put('admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');

        // Activity logs — admin-only audit trail
        Route::get('admin/logs', [ActivityLogController::class, 'index'])->name('admin.logs.index');
    });
});