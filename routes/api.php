<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/signup', [UserController::class, 'register'])->name('register');
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/request-otp', [UserController::class, 'sendOTP'])->name('send.otp');
Route::post('/verify-otp', [UserController::class, 'verifyOTP'])->name('verify.otp');

Route::middleware('auth.jwt')->group(function () {
    Route::controller(UserController::class)
        ->group(function () {
            Route::post('/verify-email', 'verifyEmail')->name('verify.email');
            Route::get('/resend-email-verification-code', 'resendEmailVerificationOTP')->name('resend.emailVerification');
            Route::patch('/reset-password', 'resetPassword')->name('password.reset');
            Route::patch('/change-password', 'changePassword')->name('password.change');
            Route::patch('/user', 'update')->name('update.user');
            Route::get('/user-details', 'getUser')->name('user.details');
            Route::get('/summary', 'getSummary')->name('dashboard.summary')->middleware('roles:owner');
            Route::post('/sales-report', 'generateSalesReport')->name('salesReport')->middleware('roles:owner');
        });

    Route::middleware('roles:owner')->group(function () {
        Route::controller(EmployeeController::class)
            ->prefix('/employee')
            ->as('employee.')
            ->group(function () {
                Route::get('', 'index')->name('all');
                Route::get('/{id}', 'find')->name('single');
                Route::post('', 'store')->name('create');
                Route::delete('', 'delete')->name('delete');
            });

        Route::controller(RoleController::class)
            ->prefix('/role')
            ->as('role.')
            ->group(function () {
                Route::get('/get-roles', 'getRoles')->name('getRoles')->withoutMiddleware('roles:owner');
                Route::post('/get-assignable-roles', 'getAssignableRoles')->name('assignable');
                Route::get('', 'all')->name('all');
                Route::get('/{id}', 'find')->name('single');
                Route::post('', 'store')->name('create');
                Route::patch('', 'update')->name('update');
                Route::delete('', 'delete')->name('delete');

            });
    });

    Route::controller(UserRoleController::class)
        ->middleware('roles:owner')
        ->prefix('/user-role')
        ->as('userRole.')
        ->group(function () {
            Route::post('', 'store')->name('create');
            Route::delete('', 'delete')->name('delete');
        });

    Route::controller(CategoryController::class)
        ->middleware('roles:owner,manager,editor')
        ->prefix('/category')
        ->as('category.')
        ->group(function () {
            Route::get('', 'all')->name('all');
            Route::get('/{id}', 'find')->name('single');
            Route::post('', 'store')->name('create');
            Route::patch('', 'update')->name('update');
            Route::delete('', 'delete')->name('delete')->withoutMiddleware('roles:owner,manager,editor')->middleware('roles:owner,manager');
        });

    Route::controller(CustomerController::class)
        ->middleware('roles:owner,manager,editor,cashier')
        ->prefix('/customer')
        ->as('customer.')
        ->group(function () {
            Route::get('', 'all')->name('all');
            Route::get('/{id}', 'find')->name('single');
            Route::post('', 'store')->name('create');
            Route::patch('', 'update')->name('update')->withoutMiddleware('roles:owner,manager,editor,cashier')->middleware('roles:owner,manager,editor');
            Route::delete('', 'delete')->name('delete')->withoutMiddleware('roles:owner,manager,editor,cashier')->middleware('roles:owner,manager');
        });

    Route::controller(ProductController::class)
        ->middleware('roles:owner,manager,editor,cashier')
        ->prefix('/product')
        ->as('product.')
        ->group(function () {
            Route::get('', 'all')->name('all');
            Route::get('/{id}', 'find')->name('single');
            Route::post('', 'store')->name('create')->withoutMiddleware('roles:owner,manager,editor,cashier')->middleware('roles:owner,manager,editor');
            Route::patch('', 'update')->name('update')->withoutMiddleware('roles:owner,manager,editor,cashier')->middleware('roles:owner,manager,editor');
            Route::delete('', 'delete')->name('delete')->withoutMiddleware('roles:owner,manager,editor,cashier')->middleware('roles:owner,manager');
        });

    Route::controller(InvoiceController::class)
        ->middleware('roles:owner,manager,cashier')
        ->prefix('/invoice')
        ->as('invoice.')
        ->group(function () {
            Route::get('', 'all')->name('all');
            Route::get('/{id}', 'find')->name('single');
            Route::post('', 'store')->name('create');
            Route::delete('', 'delete')->name('delete');
        });
});
