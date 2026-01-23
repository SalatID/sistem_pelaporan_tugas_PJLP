<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminCotroller;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\PermisionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TugasController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\UserApprovalController;

use App\Http\Controllers\JabatanController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LokasiController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [WebController::class, 'index']);
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'procLogin'])->name('proc.login');
Route::post('/register/newpassword', [UserManagementController::class, 'newPassword'])->name('newpassword');
Route::post('/forgot/password', [UserManagementController::class, 'procForgotPassword'])->name('proc.forgot.password');
Route::get('/forgot/password', [UserManagementController::class, 'forgotPassword'])->name('forgot.password');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/dashboard', [AdminCotroller::class, 'dashboard'])->name('dashboard');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserManagementController::class);
    Route::resource('permissions', PermisionController::class);
    Route::get('/register/resend/link/{token}', [UserManagementController::class, 'resendActivationLink'])->name('users.resend.activation.link');
    /*
        GET           /users                      index   users.index
        GET           /users/create               create  users.create
        POST          /users                      store   users.store
        GET           /users/{user}               show    users.show
        GET           /users/{user}/edit          edit    users.edit
        PUT|PATCH     /users/{user}               update  users.update
        DELETE        /users/{user}               destroy users.destroy
     */
        Route::resource('jabatan', JabatanController::class);
    Route::resource('kategori', KategoriController::class);
    Route::resource('lokasi', LokasiController::class);
    
    Route::get('approvals/users', [UserApprovalController::class, 'index'])
        ->name('approvals.users.index');

    Route::post('approvals/users/{userChangeRequest}/approve', [UserApprovalController::class, 'approve'])
        ->name('approvals.users.approve');

    Route::post('approvals/users/{userChangeRequest}/reject', [UserApprovalController::class, 'reject'])
        ->name('approvals.users.reject');


        
    Route::resource('tugas', TugasController::class);

    // Workflow tambahan untuk Tugas
    Route::post('tugas/{tugas}/submit',  [TugasController::class, 'submit'])->name('tugas.submit');
    Route::post('tugas/{tugas}/approve', [TugasController::class, 'approve'])->name('tugas.approve');
    Route::post('tugas/{tugas}/reject',  [TugasController::class, 'reject'])->name('tugas.reject');

});
