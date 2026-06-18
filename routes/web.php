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
| Protected Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->group(function () {

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
        | Devices
        |--------------------------------------------------------------------------
        */
        Route::put('/devices/{device}/quick', [DeviceController::class, 'quickUpdate'])
            ->name('admin.devices.quickUpdate');

        Route::patch('/devices/{device}/mark-checked', [DeviceController::class, 'markChecked'])
            ->name('admin.devices.markChecked');

        Route::get('/devices/{device}/maintenance-history', [DeviceController::class, 'maintenanceHistory'])
            ->name('admin.devices.history');

        Route::get('/reports/preventive-maintenance/export', [DeviceController::class, 'exportPreventiveMaintenanceReport'])
            ->name('admin.reports.preventiveMaintenance.export');

        Route::get('/devices/generate-qr', [DeviceController::class, 'generateQr'])
            ->name('admin.devices.qr.index');

        Route::resource('/devices', DeviceController::class)->names('admin.devices');

        // ✅ Colleges — now inside admin prefix
        Route::resource('/colleges', CollegeController::class)->names('admin.colleges');
    });

    /*
    |--------------------------------------------------------------------------
    | Offices
    |--------------------------------------------------------------------------
    */
    Route::get('colleges/{college}/offices', [OfficeController::class, 'index'])
        ->name('admin.offices.index');

    Route::post('colleges/{college}/offices', [OfficeController::class, 'store'])
        ->name('admin.offices.store');

    Route::get('colleges/{college}/offices/{office}/edit', [OfficeController::class, 'edit'])
        ->name('admin.offices.edit');

    Route::put('colleges/{college}/offices/{office}', [OfficeController::class, 'update'])
        ->name('admin.offices.update');

    Route::delete('colleges/{college}/offices/{office}', [OfficeController::class, 'destroy'])
        ->name('admin.offices.destroy');

    /*
    |--------------------------------------------------------------------------
    | Staff
    |--------------------------------------------------------------------------
    */
    Route::get('offices/{office}/staff', [StaffController::class, 'index'])
        ->name('admin.staff.index');

    Route::post('offices/{office}/staff', [StaffController::class, 'store'])
        ->name('admin.staff.store');

    Route::get('offices/{office}/staff/{staff}/edit', [StaffController::class, 'edit'])
        ->name('admin.staff.edit');

    Route::put('offices/{office}/staff/{staff}', [StaffController::class, 'update'])
        ->name('admin.staff.update');

    Route::delete('offices/{office}/staff/{staff}', [StaffController::class, 'destroy'])
        ->name('admin.staff.destroy');

    /*
    |--------------------------------------------------------------------------
    | Office Reports
    |--------------------------------------------------------------------------
    */
    Route::get('offices/{office}/reports/preventive-maintenance/export', [DeviceController::class, 'exportOfficePreventiveMaintenanceReport'])
        ->name('admin.offices.preventiveMaintenance.export');

    /*
    |--------------------------------------------------------------------------
    | Staff Devices
    |--------------------------------------------------------------------------
    */
    Route::get('staff/{staff}/devices', [StaffDeviceController::class, 'index'])
        ->name('admin.staff.devices.index');

    Route::post('staff/{staff}/devices/issue', [StaffDeviceController::class, 'issue'])
        ->name('admin.staff.devices.issue');

    Route::post('staff/{staff}/devices/{assignment}/return', [StaffDeviceController::class, 'return'])
        ->name('admin.staff.devices.return');
});