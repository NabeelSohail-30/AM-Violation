<?php

// Controllers

use App\Http\Controllers\DictionaryController;
use App\Http\Controllers\ViolationApiController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Security\RolePermission;
use App\Http\Controllers\Security\RoleController;
use App\Http\Controllers\Security\PermissionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Artisan;
// Packages
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

require __DIR__.'/auth.php';

Route::get('/storage', function () {
    Artisan::call('storage:link');
});

Route::get('/', function () {
    if (Auth::check()) {

        return redirect()->route('dashboard');
    } else {

        return redirect()->route('login');
    }
});

Route::group(['middleware' => 'auth'], function () {
    // Permission Module
    Route::get('/role-permission',[RolePermission::class, 'index'])->name('role.permission.list');
    Route::resource('permission',PermissionController::class);
    Route::resource('role', RoleController::class);

    // Dashboard Routes
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    
    // Users Module
    Route::resource('users', UserController::class);
    Route::get('/violation_api', [ViolationApiController::class, 'index'])->name('violation_api.index');
    Route::post('/fetch_records', [ViolationApiController::class, 'fetch_records'])->name('violation_api.fetch_records');
    Route::get('/violation_records', [ViolationApiController::class, 'get_violation_records'])->name('violation_api.violation_records');
    Route::post('/validate_address', [ViolationApiController::class, 'validate_specific_address'])->name('violation_api.validate_address');
});

